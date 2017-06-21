<?php

class Year
{
    public function isCurrent($mysqli)
    {
        $YearIsCurrent = mysqli_query($mysqli, "SHOW COLUMNS FROM `crop-estimates` LIKE '" . date('Y') . "est'");
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
            $notFinished = mysqli_query($mysqli, "UPDATE `crop-estimates` SET isFinished=0, isSameAsLastYear=0");

            //set (year - 1) received from bulkRT, also set the current year's suggested estimate to the same
            $setReceiptsToActual = mysqli_query($mysqli, "UPDATE `crop-estimates` JOIN
(SELECT
rtrim(
`growerReporting`.`crop-estimates`.`PK`)      AS `PK`,
sum(
`BULKRTCSV`.`Bu`)                          AS `Bushels`
FROM (`growerReporting`.`BULKRTCSV`
JOIN `growerReporting`.`crop-estimates`
ON (((rtrim(`BULKRTCSV`.`Comm Desc`) =
rtrim(
`growerReporting`.`crop-estimates`.`Comm Desc`))
AND rtrim((`BULKRTCSV`.`VarDesc` =
rtrim(
`growerReporting`.`crop-estimates`.`VarDesc`)))
AND rtrim((`BULKRTCSV`.`StrDesc` =
rtrim(
`growerReporting`.`crop-estimates`.`Str Desc`)))
AND rtrim((`BULKRTCSV`.`BlockDesc` =
rtrim(
`growerReporting`.`crop-estimates`.`BlockDesc`)))
AND rtrim((`BULKRTCSV`.`FarmDesc` =
rtrim(
`growerReporting`.`crop-estimates`.`FarmDesc`)))
AND rtrim((`BULKRTCSV`.`Grower` =
rtrim(
`growerReporting`.`crop-estimates`.`Grower`))))))
  WHERE `Crop Year`= substr(YEAR(CURDATE())-1, 4,1)
GROUP BY `growerReporting`.`crop-estimates`.`PK`) t1
ON t1.PK = `crop-estimates`.PK SET " . (date('Y') - 1) . "act = t1.Bushels, " . date('Y') . "est = t1.Bushels;");

            //set those new recommendations into the timeseries table
            mysqli_query($mysqli, "INSERT INTO `crop_estimates_changes_timeseries` (`block_PK`, `cropYear`, `belongs_to_Grower`, `changed_by`, `new_bushel_value`) SELECT `PK`, " . date('Y') . ", `Grower`, 'System', `" . (date('Y') - 1) . "est` FROM `crop-estimates`");

            //update the view with new column names for reporting to ReceivedAndEstimates.csv
            $replace_old_view = mysqli_query($mysqli,
                "CREATE OR REPLACE VIEW growerReporting.ReceivedandEstimates AS 
   (select rtrim(`growerReporting`.`crop-estimates`.`PK`) AS `BlockID`,
           rtrim(`crop-estimates`.`Grower`) AS `Code`,
           rtrim(`crop-estimates`.`Comm Desc`) AS `Commodity`,
           rtrim(`crop-estimates`.`FarmDesc`) AS `Farm`,
           ifnull(rtrim(`CurYearReceived`.`Farm`), '') AS `FarmCode`,
           rtrim(`crop-estimates`.`BlockDesc`) AS `Block`,
           ifnull(rtrim(`CurYearReceived`.`Block`),'') AS `BlockCode`,
           rtrim(`crop-estimates`.`VarDesc`) AS `Variety`,
           rtrim(`crop-estimates`.`Str Desc`) AS `Strain`,
           rtrim(`growerReporting`.`crop-estimates`.`" . (date('Y') - 3) . "act`) AS `" . (date('Y') - 3) . " Received`,
           rtrim(`growerReporting`.`crop-estimates`.`" . (date('Y') - 2) . "act`) AS `" . (date('Y') - 2) . " Received`,
           rtrim(`growerReporting`.`crop-estimates`.`" . (date('Y') - 1) . "act`) AS `" . (date('Y') - 1) . " Received`,
            rtrim(case when (`growerReporting`.`crop-estimates`.`isDeleted` = 0) then `growerReporting`.`crop-estimates`.`" . (date('Y') - 1) . "est` else 0 end) AS `" . (date('Y') - 1) . " Estimate`,
           ifnull(sum(`CurYearReceived`.`Bu`), '0') AS `" . date('Y') . " Received`,
           (case when (`growerReporting`.`crop-estimates`.`isDeleted` = 0) then 'false' else 'true' end) AS `isDeletedBlock`,
           (case when (`growerReporting`.`crop-estimates`.`isFinished` = 0) then 'false' else 'true' end) AS `isDonePicking`,
           (case when (`growerReporting`.`crop-estimates`.`" . date('Y') . "est` <> `growerReporting`.`crop-estimates`.`" . (date('Y') - 1) . "act` OR `isSameAsLastYear` = 1) then 'true' else 'false' end) AS `isUserConfirmedEstimate`
         from (`growerReporting`.`crop-estimates` 
           LEFT join CurYearReceived
             on(((rtrim(`CurYearReceived`.`Comm Desc`) = rtrim(`growerReporting`.`crop-estimates`.`Comm Desc`)) 
                 and rtrim((`CurYearReceived`.`VarDesc` = rtrim(`growerReporting`.`crop-estimates`.`VarDesc`))) 
                 and rtrim((`CurYearReceived`.`StrDesc` = rtrim(`growerReporting`.`crop-estimates`.`Str Desc`))) 
                 and rtrim((`CurYearReceived`.`BlockDesc` = rtrim(`growerReporting`.`crop-estimates`.`BlockDesc`))) 
                 and rtrim((`CurYearReceived`.`FarmDesc` = rtrim(`growerReporting`.`crop-estimates`.`FarmDesc`))) 
                 and rtrim((`CurYearReceived`.`Grower` = rtrim(`growerReporting`.`crop-estimates`.`Grower`)))))) 
         group by `growerReporting`.`crop-estimates`.`PK`)
  
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
   from (`growerReporting`.`BULKRTCSV` 
     left join `growerReporting`.`crop-estimates` 
       on(((rtrim(`BULKRTCSV`.`Comm Desc`) = rtrim(`growerReporting`.`crop-estimates`.`Comm Desc`)) 
           and rtrim((`BULKRTCSV`.`VarDesc` = rtrim(`growerReporting`.`crop-estimates`.`VarDesc`))) 
           and rtrim((`BULKRTCSV`.`StrDesc` = rtrim(`growerReporting`.`crop-estimates`.`Str Desc`))) 
           and rtrim((`BULKRTCSV`.`BlockDesc` = rtrim(`growerReporting`.`crop-estimates`.`BlockDesc`))) 
           and rtrim((`BULKRTCSV`.`FarmDesc` = rtrim(`growerReporting`.`crop-estimates`.`FarmDesc`))) 
           and rtrim((`BULKRTCSV`.`Grower` = rtrim(`growerReporting`.`crop-estimates`.`Grower`)))))) 
   where isnull(`growerReporting`.`crop-estimates`.`PK`) 
     and `Crop Year`= substr(YEAR(CURDATE()), 4,1)
   group by `BULKRTCSV`.`Grower`,`BULKRTCSV`.`Comm Desc`,`BULKRTCSV`.`FarmDesc`,`BULKRTCSV`.`BlockDesc`,`BULKRTCSV`.`VarDesc`,`BULKRTCSV`.`StrDesc`)") or error_log(mysqli_error($mysqli));
    }
}

