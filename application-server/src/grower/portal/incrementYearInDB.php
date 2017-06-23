<?php

class Year
{
    public function isCurrent($mysqli)
    {
        $YearIsCurrent = mysqli_query($mysqli, "SHOW COLUMNS FROM `grower_crop-estimates` LIKE '" . date('Y') . "est'");
        if (!mysqli_num_rows($YearIsCurrent)) {
            return false;
        }
        return true;
    }

    /**
     * @param $mysqli
     */
    public function increment($mysqli)
    {
            //create new column in crop-estimates
            $newCol = mysqli_query($mysqli, "ALTER TABLE `crop-estimates` ADD " . (date('Y') - 1) . "act INT NOT NULL AFTER " . (date('Y') - 1) . "est");
            $newCol = mysqli_query($mysqli, "ALTER TABLE `crop-estimates` ADD " . date('Y') . "est INT NOT NULL AFTER " . (date('Y') - 1) . "act");

            //set all blocks to not done picking
            $notFinished = mysqli_query($mysqli, "UPDATE `grower_crop-estimates` SET isFinished=0, isSameAsLastYear=0");

            //set (year - 1) received from bulkRT, also set the current year's suggested estimate to the same
            $setReceiptsToActual = mysqli_query($mysqli, "UPDATE `grower_crop-estimates` JOIN
(SELECT
rtrim(
`grower_crop-estimates`.`PK`)      AS `PK`,
sum(
`BULKRTCSV`.`Bu`)                          AS `Bushels`
FROM (`BULKRTCSV`
JOIN `grower_crop-estimates`
ON (((rtrim(`BULKRTCSV`.`Comm Desc`) =
rtrim(
`grower_crop-estimates`.`Comm Desc`))
AND rtrim((`BULKRTCSV`.`VarDesc` =
rtrim(
`grower_crop-estimates`.`VarDesc`)))
AND rtrim((`BULKRTCSV`.`StrDesc` =
rtrim(
`grower_crop-estimates`.`Str Desc`)))
AND rtrim((`BULKRTCSV`.`BlockDesc` =
rtrim(
`grower_crop-estimates`.`BlockDesc`)))
AND rtrim((`BULKRTCSV`.`FarmDesc` =
rtrim(
`grower_crop-estimates`.`FarmDesc`)))
AND rtrim((`BULKRTCSV`.`Grower` =
rtrim(
`grower_crop-estimates`.`Grower`))))))
  WHERE `Crop Year`= substr(YEAR(CURDATE())-1, 4,1)
GROUP BY `crop-estimates`.`PK`) t1
ON t1.PK = `crop-estimates`.PK SET " . (date('Y') - 1) . "act = t1.Bushels, " . date('Y') . "est = t1.Bushels;");

            //set those new recommendations into the timeseries table
            mysqli_query($mysqli, "INSERT INTO `grower_crop_estimates_changes_timeseries` (`block_PK`, `cropYear`, `belongs_to_Grower`, `changed_by`, `new_bushel_value`) SELECT `PK`, " . date('Y') . ", `Grower`, 'System', `" . (date('Y') - 1) . "est` FROM `crop-estimates`");

            //update the view with new column names for reporting to ReceivedAndEstimates.csv
            $replace_old_view = mysqli_query($mysqli,
                "CREATE OR REPLACE VIEW grower_ReceivedAndEstimates AS 
   (select rtrim(`grower_crop-estimates`.`PK`) AS `BlockID`,
           rtrim(`grower_crop-estimates`.`Grower`) AS `Code`,
           rtrim(`grower_crop-estimates`.`Comm Desc`) AS `Commodity`,
           rtrim(`grower_crop-estimates`.`FarmDesc`) AS `Farm`,
           ifnull(rtrim(`grower_CurYearReceived`.`Farm`), '') AS `FarmCode`,
           rtrim(`grower_crop-estimates`.`BlockDesc`) AS `Block`,
           ifnull(rtrim(`grower_CurYearReceived`.`Block`),'') AS `BlockCode`,
           rtrim(`grower_crop-estimates`.`VarDesc`) AS `Variety`,
           rtrim(`crop-estimates`.`Str Desc`) AS `Strain`,
           rtrim(`crop-estimates`.`" . (date('Y') - 3) . "act`) AS `" . (date('Y') - 3) . " Received`,
           rtrim(`crop-estimates`.`" . (date('Y') - 2) . "act`) AS `" . (date('Y') - 2) . " Received`,
           rtrim(`grower_crop-estimates`.`" . (date('Y') - 1) . "act`) AS `" . (date('Y') - 1) . " Received`,
            rtrim(case when (`grower_crop-estimates`.`isDeleted` = 0) then `grower_crop-estimates`.`" . (date('Y') - 1) . "est` else 0 end) AS `" . (date('Y') - 1) . " Estimate`,
           ifnull(sum(`grower_CurYearReceived`.`Bu`), '0') AS `" . date('Y') . " Received`,
           (case when (`grower_crop-estimates`.`isDeleted` = 0) then 'false' else 'true' end) AS `isDeletedBlock`,
           (case when (`grower_crop-estimates`.`isFinished` = 0) then 'false' else 'true' end) AS `isDonePicking`,
           (case when (`grower_crop-estimates`.`" . date('Y') . "est` <> `grower_crop-estimates`.`" . (date('Y') - 1) . "act` OR `isSameAsLastYear` = 1) then 'true' else 'false' end) AS `isUserConfirmedEstimate`
         from (`grower_crop-estimates` 
           LEFT join grower_CurYearReceived
             on(((rtrim(`grower_CurYearReceived`.`Comm Desc`) = rtrim(`grower_crop-estimates`.`Comm Desc`)) 
                 and rtrim((`grower_CurYearReceived`.`VarDesc` = rtrim(`grower_crop-estimates`.`VarDesc`))) 
                 and rtrim((`grower_CurYearReceived`.`StrDesc` = rtrim(`grower_crop-estimates`.`Str Desc`))) 
                 and rtrim((`grower_CurYearReceived`.`BlockDesc` = rtrim(`grower_crop-estimates`.`BlockDesc`))) 
                 and rtrim((`grower_CurYearReceived`.`FarmDesc` = rtrim(`grower_crop-estimates`.`FarmDesc`))) 
                 and rtrim((`grower_CurYearReceived`.`Grower` = rtrim(`grower_crop-estimates`.`Grower`)))))) 
         group by `grower_crop-estimates`.`PK`)
  
  union (select 'Unmatched Block' AS `BlockID`,
     rtrim(`BULKRTCSV`.`Grower`) AS `Code`,
     rtrim(`BULKRTCSV`.`Comm Desc`) AS `Commodity`,
     rtrim(`BULKRTCSV`.`FarmDesc`) AS `Farm`,
     rtrim(`BULKRTCSV`.`Farm`) AS `FarmCode`,
     rtrim(`BULKRTCSV`.`BlockDesc`) AS `Block`,
     rtrim(`BULKRTCSV`.`Block`) AS `BlockCode`,
     rtrim(`BULKRTCSV`.`VarDesc`) AS `Variety`,
     rtrim(`BULKRTCSV`.`StrDesc`) AS `Strain`,
     '0' AS `" . (date('Y') - 3) . " Received`,
     '0' AS `" . (date('Y') - 2) . " Received`,
     '0' AS `" . (date('Y') - 1) . " Received`,
     '0' AS `" . date('Y') . " Estimate`,
     sum(`BULKRTCSV`.`Bu`) AS `" . date('Y') . " Received`,
     'false' AS `isDeletedBlock`,
     'false' AS `isDonePicking`,
     'false' AS `isUserConfirmedEstimate` 
   from (`BULKRTCSV` 
     left join `grower_crop-estimates` 
       on(((rtrim(`BULKRTCSV`.`Comm Desc`) = rtrim(`grower_crop-estimates`.`Comm Desc`)) 
           and rtrim((`BULKRTCSV`.`VarDesc` = rtrim(`grower_crop-estimates`.`VarDesc`))) 
           and rtrim((`BULKRTCSV`.`StrDesc` = rtrim(`grower_crop-estimates`.`Str Desc`))) 
           and rtrim((`BULKRTCSV`.`BlockDesc` = rtrim(`grower_crop-estimates`.`BlockDesc`))) 
           and rtrim((`BULKRTCSV`.`FarmDesc` = rtrim(`grower_crop-estimates`.`FarmDesc`))) 
           and rtrim((`BULKRTCSV`.`Grower` = rtrim(`crop-estimates`.`Grower`)))))) 
   where isnull(`crop-estimates`.`PK`) 
     and `Crop Year`= substr(YEAR(CURDATE()), 4,1)
   group by `BULKRTCSV`.`Grower`,`BULKRTCSV`.`Comm Desc`,`BULKRTCSV`.`FarmDesc`,`BULKRTCSV`.`BlockDesc`,`BULKRTCSV`.`VarDesc`,`BULKRTCSV`.`StrDesc`)") or error_log(mysqli_error($mysqli));
    }
}

