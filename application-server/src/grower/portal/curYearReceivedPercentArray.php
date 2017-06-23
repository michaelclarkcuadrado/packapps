<?php

class curYearReceivedPercentArray
{
    public function returnArray($mysqli, $growerCode)
    {
        error_log($growerCode);
        //using this instead of a view since views don't support dynamic column names

        $query = mysqli_query($mysqli, "select `grower_crop-estimates`.`PK` AS `PK`,
    `grower_crop-estimates`.`Comm Desc` AS `Comm Desc`,
    `grower_crop-estimates`.`VarDesc` AS `VarDesc`,
    `grower_crop-estimates`.`Str Desc` AS `Str Desc`,
    `grower_crop-estimates`.`FarmDesc` AS `FarmDesc`,
    `grower_crop-estimates`.`BlockDesc` AS `BlockDesc`,
    ifnull(sum(`BULKRTCSV`.`Bu`), 0) AS `Total`,
    `grower_crop-estimates`.`" . date('Y') . "est` AS Est,
    ifnull(round(((sum(`BULKRTCSV`.`Bu`) / `grower_crop-estimates`.`".date('Y')."est`) * 100), 2), 0) AS `Percent`,
    `grower_crop-estimates`.`isFinished` AS `isFinished` 
  from (`grower_crop-estimates` 
    left join `BULKRTCSV` 
      on(((trim(`BULKRTCSV`.`Comm Desc`) = trim(`grower_crop-estimates`.`Comm Desc`)) 
          and (trim(`BULKRTCSV`.`VarDesc`) = trim(`grower_crop-estimates`.`VarDesc`)) 
          and (trim(`BULKRTCSV`.`StrDesc`) = trim(`grower_crop-estimates`.`Str Desc`)) 
          and (trim(`BULKRTCSV`.`BlockDesc`) = trim(`grower_crop-estimates`.`BlockDesc`)) 
          and (trim(`BULKRTCSV`.`FarmDesc`) = trim(`grower_crop-estimates`.`FarmDesc`)) 
          and (trim(`BULKRTCSV`.`Grower`) = trim(`crop-estimates`.`Grower`))))) 
  where ((`grower_crop-estimates`.`isDeleted` = '0') 
         and `grower_crop-estimates`.Grower = '" . $growerCode . "'
         and (`Crop Year`= substr(YEAR(CURDATE()), 4,1)) || `Crop Year` IS NULL)
  group by `crop-estimates`.`PK`
        ORDER BY isFinished, Percent ASC") or error_log(mysqli_error($mysqli));
        return $query;
    }
}