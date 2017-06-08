-- This is the schema of Packapps v2.0, which features containerization and the maintenance and storage apps.
-- As the v1.0 database was not designed through migrations, this is the first migration and must maintain compatability with all data in 1.0.
-- Any change from 1.0 must migrate the existing data first. v3.0 will do the same from 2.0's schema as well, and so on.

USE operationsData;

/* Create systeminfo table for versioning */
CREATE TABLE `operationsData`.`packapps_system_info` ( `packapps_version` INT NOT NULL , `systemInstalled` TINYINT(1) NOT NULL , `dateInstalled` DATETIME NOT NULL , `Notes` VARCHAR(1023) NULL ) ENGINE = InnoDB;
INSERT INTO `operationsData`.`packapps_system_info` (packapps_version, systemInstalled, dateInstalled) VALUES (2, 0, '0000-00-00 00:00:00');

/* Add permissions columns to master_users */
ALTER TABLE master_users ADD COLUMN allowedStorage TINYINT(1) DEFAULT 0 NOT NULL AFTER allowedProduction;
ALTER TABLE master_users ADD COLUMN allowedMaintenance TINYINT(1) DEFAULT 0 NOT NULL AFTER allowedStorage;

/* Create new UserData tables for new packapps and pre-populate with usernames */
CREATE TABLE `operationsData`.`storage_UserData` ( `username` VARCHAR(255) NOT NULL , `Role` ENUM('readonly','full') NOT NULL ) ENGINE = InnoDB;
ALTER TABLE `operationsData`.`storage_UserData` ADD PRIMARY KEY (`username`);
ALTER TABLE `storage_UserData` ADD CONSTRAINT `storageuserdata2masterusers` FOREIGN KEY (`username`) REFERENCES `master_users`(`username`) ON DELETE CASCADE ON UPDATE CASCADE;
INSERT INTO storage_UserData (username) SELECT username FROM master_users;

CREATE TABLE `operationsData`.`maintenance_UserData` ( `username` VARCHAR(255) NOT NULL , `Role` ENUM('readonly','worker','readwrite') NOT NULL ) ENGINE = InnoDB;
ALTER TABLE `operationsData`.`maintenance_UserData` ADD PRIMARY KEY (`username`);
ALTER TABLE `maintenance_UserData` ADD CONSTRAINT `maintenanceuserdata2masterusers` FOREIGN KEY (`username`) REFERENCES `master_users`(`username`) ON DELETE CASCADE ON UPDATE CASCADE;
INSERT INTO maintenance_UserData (username) SELECT username FROM master_users;

/* Create new packapp_appProperties table to support more standardized modules */
CREATE TABLE `operationsData`.`packapps_appProperties` ( `app_id` INT NOT NULL AUTO_INCREMENT, `short_app_name` VARCHAR(255) NOT NULL , `long_app_name` VARCHAR(255) NOT NULL , `material_icon_name` VARCHAR(255) NOT NULL , `isEnabled` TINYINT(1) NOT NULL DEFAULT '1' , `Notes` VARCHAR(255) NOT NULL, PRIMARY KEY (app_id) ) ENGINE = InnoDB;
ALTER TABLE `operationsData`.`packapps_appProperties` ADD UNIQUE `unique_app_short_names` (`short_app_name`);

/* Add existing packapps to above table */
INSERT INTO packapps_appProperties (short_app_name, long_app_name, material_icon_name, isEnabled, Notes) VALUES ('quality', 'Quality Assurance Panel', 'check_circle', 1, '');
INSERT INTO packapps_appProperties (short_app_name, long_app_name, material_icon_name, isEnabled, Notes) VALUES ('production', 'Production Coordinator', 'list', 1, '');
INSERT INTO packapps_appProperties (short_app_name, long_app_name, material_icon_name, isEnabled, Notes) VALUES ('purchasing', 'Purchasing Dashboard', 'dashboard', 1, '');
INSERT INTO packapps_appProperties (short_app_name, long_app_name, material_icon_name, isEnabled, Notes) VALUES ('maintenance', 'Maintenance Dashboard', 'build', 1, '');
INSERT INTO packapps_appProperties (short_app_name, long_app_name, material_icon_name, isEnabled, Notes) VALUES ('storage', 'Storage Insights', 'track_changes', 1, '');

/* Create new permissions table, prepopulate with existing packapps */
CREATE TABLE `operationsData`.`packapps_app_permissions` ( `packapp` VARCHAR(255) NOT NULL , `permissionLevel` INT NOT NULL , `Meaning` VARCHAR(255) NOT NULL , `Color` VARCHAR(255) NOT NULL ) ENGINE = InnoDB;
ALTER TABLE `operationsData`.`packapps_app_permissions` ADD PRIMARY KEY (`packapp`, `permissionLevel`);
ALTER TABLE `packapps_app_permissions` ADD CONSTRAINT `app_permissions_2_appProperties` FOREIGN KEY (`packapp`) REFERENCES `packapps_appProperties`(`short_app_name`) ON DELETE CASCADE ON UPDATE CASCADE;
INSERT INTO packapps_app_permissions (packapp, permissionLevel, Meaning, Color) VALUES ('quality', '0', 'Weight Input Only', 'Red');
INSERT INTO packapps_app_permissions (packapp, permissionLevel, Meaning, Color) VALUES ('quality', '1', 'Receipt Inspector', 'Orange');
INSERT INTO packapps_app_permissions (packapp, permissionLevel, Meaning, Color) VALUES ('quality', '2', 'Full', 'Green');
INSERT INTO packapps_app_permissions (packapp, permissionLevel, Meaning, Color) VALUES ('production', '0', 'Read-Only', 'Orange');
INSERT INTO packapps_app_permissions (packapp, permissionLevel, Meaning, Color) VALUES ('production', '1', 'Full', 'Green');
INSERT INTO packapps_app_permissions (packapp, permissionLevel, Meaning, Color) VALUES ('purchasing', '0', 'No Purchases', 'Orange');
INSERT INTO packapps_app_permissions (packapp, permissionLevel, Meaning, Color) VALUES ('purchasing', '1', 'Full', 'Green');
INSERT INTO packapps_app_permissions (packapp, permissionLevel, Meaning, Color) VALUES ('maintenance', '0', 'Read-Only', 'Red');
INSERT INTO packapps_app_permissions (packapp, permissionLevel, Meaning, Color) VALUES ('maintenance', '1', 'Worker', 'Orange');
INSERT INTO packapps_app_permissions (packapp, permissionLevel, Meaning, Color) VALUES ('maintenance', '2', 'Full', 'Green');
INSERT INTO packapps_app_permissions (packapp, permissionLevel, Meaning, Color) VALUES ('storage', '0', 'Read-Only', 'Red');
INSERT INTO packapps_app_permissions (packapp, permissionLevel, Meaning, Color) VALUES ('storage', '1', 'Receiving', 'Yellow');
INSERT INTO packapps_app_permissions (packapp, permissionLevel, Meaning, Color) VALUES ('storage', '2', 'Full', 'Green');

/* rename master_users table to reflect it is part of packapps framework */
RENAME TABLE master_users TO packapps_master_users;

/* Remove deprecated isSectionManager columns that only cause misery */
ALTER TABLE `quality_UserData` DROP `isSectionManager`;
ALTER TABLE `production_UserData` DROP `isSectionManager`;
ALTER TABLE `purchasing_UserData` DROP `isSectionManager`;

/* quality tables did not have prefixes as they predate packapps. Add prefixes to quality tables */
RENAME TABLE AggregateWeightSamples TO quality_AggregateWeightSamples;
RENAME TABLE AlertEmails TO quality_AlertEmails;
RENAME TABLE AppleSamples TO quality_AppleSamples;
RENAME TABLE InspectedRTs TO quality_InspectedRTs;
RENAME TABLE run_inspections TO quality_run_inspections;

/* Update views to match new table names */

/*!50001 DROP TABLE IF EXISTS `AvgWeightByRT`*/;
/*!50001 DROP VIEW IF EXISTS `AvgWeightByRT`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `quality_AvgWeightByRT` AS select `quality_InspectedRTs`.`RTNum` AS `RTNum`,case when `quality_AggregateWeightSamples`.`Weight` is not null then (sum(`quality_InspectedRTs`.`#Samples`) * avg(`quality_AppleSamples`.`Weight`) + 20 * count(`quality_AggregateWeightSamples`.`RT#`) * avg(`quality_AggregateWeightSamples`.`Weight` / 20)) / (sum(`quality_InspectedRTs`.`#Samples`) + 20 * count(`quality_AggregateWeightSamples`.`RT#`)) else ifnull(avg(`quality_AppleSamples`.`Weight`),0) end AS `WeightAvg` from ((`quality_InspectedRTs` left join `quality_AppleSamples` on(`quality_InspectedRTs`.`RTNum` = `quality_AppleSamples`.`RT#`)) left join `quality_AggregateWeightSamples` on(`quality_InspectedRTs`.`RTNum` = `quality_AggregateWeightSamples`.`RT#`)) group by `quality_InspectedRTs`.`RTNum` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;


/*!50001 DROP TABLE IF EXISTS `Block_Receiving`*/;
/*!50001 DROP VIEW IF EXISTS `Block_Receiving`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `quality_Block_Receiving` AS select trim(`BULKOHCSV`.`Grower`) AS `Grower`,trim(`BULKOHCSV`.`CommDesc`) AS `CommDesc`,trim(`BULKOHCSV`.`FarmDesc`) AS `Farm`,trim(`BULKOHCSV`.`BlockDesc`) AS `Block`,trim(`BULKOHCSV`.`VarDesc`) AS `VarDesc`,trim(`BULKOHCSV`.`StrDesc`) AS `Strain`,round(avg(`quality_AppleSamples`.`Pressure1`),3) AS `Pressure1`,round(avg(`quality_AppleSamples`.`Pressure2`),2) AS `Pressure2`,round(avg(`quality_AppleSamples`.`Brix`),2) AS `Brix`,round(avg(`quality_AppleSamples`.`DA`),2) AS `DA`,round(avg(`quality_AppleSamples`.`DA2`),2) AS `DA2`,count(0) AS `Count`,round(avg(`quality_AppleSamples`.`Weight`),2) AS `Weight`,round(avg(`quality_AppleSamples`.`Starch`),2) AS `Starch` from (`quality_AppleSamples` join `BULKOHCSV` on(`quality_AppleSamples`.`RT#` = `BULKOHCSV`.`RT#`)) where year(`quality_AppleSamples`.`FinalInspectionDate`) = year(curdate()) group by `BULKOHCSV`.`Grower`,`BULKOHCSV`.`FarmDesc`,`BULKOHCSV`.`BlockDesc`,`BULKOHCSV`.`VarDesc`,`BULKOHCSV`.`StrDesc` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

/*!50001 DROP TABLE IF EXISTS `RTsWQuality`*/;
/*!50001 DROP VIEW IF EXISTS `RTsWQuality`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `quality_RTsWQuality` AS select `BULKOHCSV`.`RT#` AS `RT#`,`BULKOHCSV`.`SortCode` AS `SortCode`,`BULKOHCSV`.`CropYear` AS `Crop Year`,`BULKOHCSV`.`Grower` AS `Grower`,`BULKOHCSV`.`GrowerName` AS `Grower Name`,`BULKOHCSV`.`Class` AS `Class`,`BULKOHCSV`.`ClassDesc` AS `Class Desc`,`BULKOHCSV`.`Commodity` AS `Commodity`,`BULKOHCSV`.`CommDesc` AS `CommDesc`,`BULKOHCSV`.`Variety` AS `Variety`,`BULKOHCSV`.`VarDesc` AS `Var Desc`,`BULKOHCSV`.`Strain` AS `Strain`,`BULKOHCSV`.`StrDesc` AS `Str Desc`,`BULKOHCSV`.`Farm` AS `Farm`,case when `BULKOHCSV`.`FarmDesc` = '' then '[Farm Name]' else `BULKOHCSV`.`FarmDesc` end AS `Farm Desc`,`BULKOHCSV`.`Block` AS `Block`,case when `BULKOHCSV`.`BlockDesc` = '' then '[Block Name]' else `BULKOHCSV`.`BlockDesc` end AS `Block Desc`,`BULKOHCSV`.`Lot` AS `Lot`,`BULKOHCSV`.`Date` AS `Date`,`BULKOHCSV`.`Size` AS `Size`,`BULKOHCSV`.`Pack` AS `Pack`,`BULKOHCSV`.`QtyOnHand` AS `QtyOnHand`,`BULKOHCSV`.`BuOnHand` AS `BuOnHand`,`BULKOHCSV`.`RoomNum` AS `Location`,`BULKOHCSV`.`CoNum` AS `Co#`,`BULKOHCSV`.`Company Name` AS `Company Name`,case when `quality_InspectedRTs`.`Color Quality` is null then 'FALSE' else 'TRUE' end AS `isQA`,case when `quality_AppleSamples`.`PrAvg` is null then '' else round(avg(`quality_AppleSamples`.`PrAvg`),3) end AS `PressureAvg`,case when `quality_AppleSamples`.`DAAvg` is null then '' else round(avg(`quality_AppleSamples`.`DAAvg`),2) end AS `DAAvg`,ifnull(round(avg(`quality_AppleSamples`.`Brix`),2),'') AS `Brix`,ifnull(round(avg(`quality_AppleSamples`.`Starch`),1),'') AS `Starch`,ifnull(concat(`quality_InspectedRTs`.`Color Quality`,convert(case when `quality_InspectedRTs`.`Blush` <> 0 then ' With Blush' else '' end using latin1)),'') AS `Color`,ifnull(`quality_InspectedRTs`.`Bruise`,'') AS `Bruise`,case when `quality_InspectedRTs`.`BitterPit` is null then '' else case when `quality_InspectedRTs`.`BitterPit` <> 0 then 'Present' else 'Not Present' end end AS `BitterPit`,ifnull(`quality_InspectedRTs`.`Russet`,'') AS `Russet`,ifnull(`quality_InspectedRTs`.`SunBurn`,'') AS `Sunburn`,ifnull(`quality_InspectedRTs`.`SanJoseScale`,'') AS `San Jose Scale`,ifnull(`quality_InspectedRTs`.`Scab`,'') AS `Scab`,ifnull(`quality_InspectedRTs`.`StinkBug`,'') AS `StinkBug`,ifnull(round(`quality_AvgWeightByRT`.`WeightAvg`,2),'') AS `AverageWeight`,case when `quality_AvgWeightByRT`.`WeightAvg` is null then '' else case when `quality_AvgWeightByRT`.`WeightAvg` * 16 >= 13 then 48 when (`quality_AvgWeightByRT`.`WeightAvg` * 16 < 13 and `quality_AvgWeightByRT`.`WeightAvg` >= 11.15) then 56 when (`quality_AvgWeightByRT`.`WeightAvg` * 16 < 11.15 and `quality_AvgWeightByRT`.`WeightAvg` * 16 >= 9.9) then 64 when (`quality_AvgWeightByRT`.`WeightAvg` * 16 < 9.9 and `quality_AvgWeightByRT`.`WeightAvg` * 16 >= 8.85) then 72 when (`quality_AvgWeightByRT`.`WeightAvg` * 16 < 8.85 and `quality_AvgWeightByRT`.`WeightAvg` * 16 >= 8) then 80 when (`quality_AvgWeightByRT`.`WeightAvg` * 16 < 8 and `quality_AvgWeightByRT`.`WeightAvg` * 16 >= 7.15) then 88 when (`quality_AvgWeightByRT`.`WeightAvg` * 16 < 7.15 and `quality_AvgWeightByRT`.`WeightAvg` * 16 >= 6.3) then 100 when (`quality_AvgWeightByRT`.`WeightAvg` * 16 < 6.3 and `quality_AvgWeightByRT`.`WeightAvg` * 16 >= 5.65) then 113 when (`quality_AvgWeightByRT`.`WeightAvg` * 16 < 5.65 and `quality_AvgWeightByRT`.`WeightAvg` * 16 >= 5.1) then 125 when (`quality_AvgWeightByRT`.`WeightAvg` * 16 < 5.1 and `quality_AvgWeightByRT`.`WeightAvg` * 16 >= 4.65) then 138 when (`quality_AvgWeightByRT`.`WeightAvg` * 16 < 4.65 and `quality_AvgWeightByRT`.`WeightAvg` * 16 >= 4.3) then 150 when (`quality_AvgWeightByRT`.`WeightAvg` * 16 < 4.3 and `quality_AvgWeightByRT`.`WeightAvg` * 16 >= 3.95) then 163 when (`quality_AvgWeightByRT`.`WeightAvg` * 16 < 3.95 and `quality_AvgWeightByRT`.`WeightAvg` * 16 >= 3.6) then 175 when (`quality_AvgWeightByRT`.`WeightAvg` * 16 < 3.6 and `quality_AvgWeightByRT`.`WeightAvg` * 16 >= 3.25) then 198 else 216 end end AS `SizefromAverage`,ifnull(`quality_InspectedRTs`.`Note`,'') AS `Notes`,case when `quality_InspectedRTs`.`InspectedBy` is null then '' else concat('Field Inspector: ',`quality_InspectedRTs`.`InspectedBy`,'-- Final Inspector: ',ifnull(`quality_AppleSamples`.`FinalTestedBy`,'Not Final Inspected')) end AS `InspectedBy`,ifnull(`quality_AppleSamples`.`FinalInspectionDate`,ifnull(`quality_InspectedRTs`.`DateInspected`,'')) AS `DateTested` from (((`BULKOHCSV` left join `quality_InspectedRTs` on(`BULKOHCSV`.`RT#` = `quality_InspectedRTs`.`RTNum`)) left join `quality_AppleSamples` on(`quality_InspectedRTs`.`RTNum` = `quality_AppleSamples`.`RT#`)) left join `quality_AvgWeightByRT` on(`quality_AvgWeightByRT`.`RTNum` = `BULKOHCSV`.`RT#`)) group by `BULKOHCSV`.`RT#` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;