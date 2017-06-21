<?php

class curYearReceivedPercentArray
{
    public function returnArray($mysqli, $growerCode)
    {
        error_log($growerCode);
        //using this instead of a view since views don't support dynamic column names

        $query = mysqli_query($mysqli, "select `growerReporting`.`crop-estimates`.`PK` AS `PK`,
    `growerReporting`.`crop-estimates`.`Comm Desc` AS `Comm Desc`,
    `growerReporting`.`crop-estimates`.`VarDesc` AS `VarDesc`,
    `growerReporting`.`crop-estimates`.`Str Desc` AS `Str Desc`,
    `growerReporting`.`crop-estimates`.`FarmDesc` AS `FarmDesc`,
    `growerReporting`.`crop-estimates`.`BlockDesc` AS `BlockDesc`,
    ifnull(sum(`growerReporting`.`BULKRTCSV`.`Bu`), 0) AS `Total`,
    `growerReporting`.`crop-estimates`.`" . date('Y') . "est` AS Est,
    ifnull(round(((sum(`growerReporting`.`BULKRTCSV`.`Bu`) / `growerReporting`.`crop-estimates`.`".date('Y')."est`) * 100), 2), 0) AS `Percent`,
    `growerReporting`.`crop-estimates`.`isFinished` AS `isFinished` 
  from (`growerReporting`.`crop-estimates` 
    left join `growerReporting`.`BULKRTCSV` 
      on(((trim(`growerReporting`.`BULKRTCSV`.`Comm Desc`) = trim(`growerReporting`.`crop-estimates`.`Comm Desc`)) 
          and (trim(`growerReporting`.`BULKRTCSV`.`VarDesc`) = trim(`growerReporting`.`crop-estimates`.`VarDesc`)) 
          and (trim(`growerReporting`.`BULKRTCSV`.`StrDesc`) = trim(`growerReporting`.`crop-estimates`.`Str Desc`)) 
          and (trim(`growerReporting`.`BULKRTCSV`.`BlockDesc`) = trim(`growerReporting`.`crop-estimates`.`BlockDesc`)) 
          and (trim(`growerReporting`.`BULKRTCSV`.`FarmDesc`) = trim(`growerReporting`.`crop-estimates`.`FarmDesc`)) 
          and (trim(`growerReporting`.`BULKRTCSV`.`Grower`) = trim(`growerReporting`.`crop-estimates`.`Grower`))))) 
  where ((`growerReporting`.`crop-estimates`.`isDeleted` = '0') 
         and `crop-estimates`.Grower = '" . $growerCode . "'
         and (`Crop Year`= substr(YEAR(CURDATE()), 4,1)) || `Crop Year` IS NULL)
  group by `growerReporting`.`crop-estimates`.`PK`
        ORDER BY isFinished, Percent ASC") or error_log(mysqli_error($mysqli));
        return $query;
    }
}