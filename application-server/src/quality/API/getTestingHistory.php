<?php
/**
 * Created by PhpStorm.
 * User: Michael Clark-Cuadrado
 * Date: 8/19/2016
 * Time: 9:05 AM
 */
//returns the last 20 tests, either runs or RTs, that the system received
include '../../config.php';
$historyQuery = mysqli_query($mysqli, "
(SELECT
   'RT'                               AS Type,
   quality_InspectedRTs.receiptNum    AS ID,
   `#Samples`                         AS Samples,
   FinalInspectionDate                AS `Last Change`,
   DAFinished                         AS DA,
   StarchFinished                     AS Starch,
   isFinalInspected                   AS Final,
   IFNULL(FinalUserName.`Real Name`, InspectedUserName.`Real Name`) AS Inspector
 FROM quality_InspectedRTs
   JOIN quality_AppleSamples ON quality_InspectedRTs.receiptNum = quality_AppleSamples.`receiptNum`
   LEFT JOIN quality_UserData FinalUser ON quality_AppleSamples.FinalTestedBy = FinalUser.UserName
   JOIN quality_UserData inspectedUser ON quality_InspectedRTs.InspectedBy = inspectedUser.UserName
    JOIN packapps_master_users InspectedUserName ON inspectedUser.UserName = InspectedUserName.username
   LEFT JOIN packapps_master_users FinalUserName ON FinalUser.UserName = FinalUserName.username
 GROUP BY quality_AppleSamples.receiptNum
 ORDER BY FinalInspectionDate DESC
 LIMIT 20)
UNION (SELECT
         CONCAT(CASE WHEN isPreInspection = 1
           THEN '(Pre) '
                ELSE '' END, 'Run') AS Type,
         RunNumber                  AS ID,
         CASE WHEN isPreInspection = 1
           THEN 10
         ELSE 5 END                 AS Samples,
         DateAdded                  AS `Last Change`,
         '0'                        AS DA,
         '0'                        AS Starch,
         1                          AS Final,
         'QA Team'                  AS Inspector
       FROM quality_run_inspections
         JOIN production_runs ON quality_run_inspections.RunID = production_runs.RunID
       GROUP BY RunNumber, isPreInspection
       ORDER BY quality_run_inspections.RunID DESC
       LIMIT 20)
ORDER BY `Last Change` DESC
LIMIT 20
");
header('Content-type: application/json');
echo json_encode(mysqli_fetch_all($historyQuery, MYSQLI_ASSOC));