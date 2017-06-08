<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 8/19/2016
 * Time: 9:05 AM
 */
//returns the last 20 tests, either runs or RTs, that the system received
include '../../config.php';
$historyQuery = mysqli_query($mysqli, "(SELECT 'RT' as Type, RTNum as ID, `#Samples` as Samples, FinalInspectionDate as `Last Change`, DAFinished as DA, StarchFinished as Starch, isFinalInspected as Final, IFNULL(FinalTestedBy, InspectedBy) as Inspector FROM quality_InspectedRTs JOIN quality_AppleSamples ON quality_InspectedRTs.RTNum = quality_AppleSamples.`RT#` GROUP BY RTNum ORDER BY FinalInspectionDate DESC LIMIT 20) UNION (SELECT CONCAT(CASE WHEN isPreInspection = 1 THEN '(Pre) ' ELSE '' END, 'Run') as Type, RunNumber as ID, CASE WHEN isPreInspection = 1 THEN 10 ELSE 5 END as Samples, DateAdded as `Last Change`, '0' as DA, '0' as Starch, 1 as Final, 'QA Team' as Inspector FROM quality_run_inspections JOIN production_runs ON quality_run_inspections.RunID=production_runs.RunID GROUP BY RunNumber, isPreInspection ORDER BY quality_run_inspections.RunID DESC LIMIT 20) ORDER BY `Last Change` DESC LIMIT 20");
echo json_encode(mysqli_fetch_all($historyQuery, MYSQLI_ASSOC));