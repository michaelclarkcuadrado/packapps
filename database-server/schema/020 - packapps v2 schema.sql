-- This is the schema of Packapps v2.0, which features containerization and the maintenance and storage apps.
-- As the v1.0 database was not designed through migrations, this is the first migration and must maintain compatability with all data in 1.0.
-- Any change from 1.0 must migrate the existing data first. v3.0 will do the same from 2.0's schema as well, and so on.

USE operationsData;

/* SYSTEM RECORD KEEPING */

/* Create systeminfo table for versioning */
CREATE TABLE `operationsData`.`packapps_system_info` ( `packapps_version` INT NOT NULL , `systemInstalled` TINYINT(1) NOT NULL , `dateInstalled` DATETIME NOT NULL , `company_slug` VARCHAR(255), `Notes` VARCHAR(1023) NULL ) ENGINE = InnoDB;
INSERT INTO `operationsData`.`packapps_system_info` (packapps_version, systemInstalled, dateInstalled) VALUES (2, 0, '0000-00-00 00:00:00');

/* Create new packapp_appProperties table to support more standardized modules */
CREATE TABLE `operationsData`.`packapps_appProperties` ( `app_id` INT NOT NULL AUTO_INCREMENT, `short_app_name` VARCHAR(255) NOT NULL , `long_app_name` VARCHAR(255) NOT NULL , `material_icon_name` VARCHAR(255) NOT NULL , `isEnabled` TINYINT(1) NOT NULL DEFAULT '1' , `Notes` VARCHAR(255) NOT NULL, PRIMARY KEY (app_id) ) ENGINE = InnoDB;
ALTER TABLE `operationsData`.`packapps_appProperties` ADD UNIQUE `unique_app_short_names` (`short_app_name`);

/* Add existing packapps to above table */
INSERT INTO packapps_appProperties (short_app_name, long_app_name, material_icon_name, isEnabled, Notes) VALUES ('quality', 'Quality Assurance Panel', 'check_circle', 1, '');
INSERT INTO packapps_appProperties (short_app_name, long_app_name, material_icon_name, isEnabled, Notes) VALUES ('production', 'Production Coordinator', 'list', 1, '');
INSERT INTO packapps_appProperties (short_app_name, long_app_name, material_icon_name, isEnabled, Notes) VALUES ('purchasing', 'Purchasing Dashboard', 'dashboard', 1, '');
INSERT INTO packapps_appProperties (short_app_name, long_app_name, material_icon_name, isEnabled, Notes) VALUES ('maintenance', 'Maintenance Dashboard', 'build', 1, '');
INSERT INTO packapps_appProperties (short_app_name, long_app_name, material_icon_name, isEnabled, Notes) VALUES ('storage', 'Storage Insights', 'track_changes', 1, '');
INSERT INTO packapps_appProperties (short_app_name, long_app_name, material_icon_name, isEnabled, Notes) VALUES ('grower', 'Grower Portal', 'public', 1, '');

/* END SYSTEM RECORD KEEPING */

/* PERMISSIONS */

/* Remove deprecated isSectionManager columns that only cause misery */
ALTER TABLE `quality_UserData` DROP `isSectionManager`;
ALTER TABLE `production_UserData` DROP `isSectionManager`;
ALTER TABLE `purchasing_UserData` DROP `isSectionManager`;

/* Update purchasing permissions */
ALTER TABLE `purchasing_UserData` ADD `Role` INT NOT NULL DEFAULT '1' AFTER `isAuthorizedForPurchases`;
UPDATE `purchasing_UserData` SET Role=isAuthorizedForPurchases+1;
ALTER TABLE `purchasing_UserData` DROP `isAuthorizedForPurchases`;

/* Add maintenance, storage columns to master_users */
ALTER TABLE master_users ADD COLUMN allowedStorage TINYINT(1) DEFAULT 0 NOT NULL AFTER allowedProduction;
ALTER TABLE master_users ADD COLUMN allowedMaintenance TINYINT(1) DEFAULT 0 NOT NULL AFTER allowedStorage;
ALTER TABLE master_users ADD COLUMN allowedGrower TINYINT(1) DEFAULT 0 NOT NULL AFTER allowedMaintenance;

/* Create new UserData tables for new packapps and pre-populate with usernames */
CREATE TABLE `operationsData`.`storage_UserData` ( `UserName` VARCHAR(255) NOT NULL , `Role` ENUM('readonly','receiving','full') NOT NULL ) ENGINE = InnoDB;
ALTER TABLE `operationsData`.`storage_UserData` ADD PRIMARY KEY (`UserName`);
ALTER TABLE `storage_UserData` ADD CONSTRAINT `storageuserdata2masterusers` FOREIGN KEY (`UserName`) REFERENCES `master_users`(`username`) ON DELETE CASCADE ON UPDATE CASCADE;
INSERT INTO storage_UserData (UserName) SELECT username FROM master_users;

CREATE TABLE `operationsData`.`maintenance_UserData` ( `UserName` VARCHAR(255) NOT NULL , `Role` ENUM('readonly','worker','readwrite') NOT NULL ) ENGINE = InnoDB;
ALTER TABLE `operationsData`.`maintenance_UserData` ADD PRIMARY KEY (`UserName`);
ALTER TABLE `maintenance_UserData` ADD CONSTRAINT `maintenanceuserdata2masterusers` FOREIGN KEY (`UserName`) REFERENCES `master_users`(`username`) ON DELETE CASCADE ON UPDATE CASCADE;
INSERT INTO maintenance_UserData (UserName) SELECT username FROM master_users;

CREATE TABLE `operationsData`.`grower_UserData` ( `UserName` VARCHAR(255) NOT NULL , `Role` ENUM('readonly','full') NOT NULL ) ENGINE = InnoDB;
ALTER TABLE `operationsData`.`grower_UserData` ADD PRIMARY KEY (`UserName`);
ALTER TABLE `grower_UserData` ADD CONSTRAINT `growerserdata2masterusers` FOREIGN KEY (`UserName`) REFERENCES `master_users`(`username`) ON DELETE CASCADE ON UPDATE CASCADE;
INSERT INTO grower_UserData (UserName) SELECT username FROM master_users;

/* Create new permissions table, prepopulate with existing packapps */
CREATE TABLE `operationsData`.`packapps_app_permissions` ( `packapp` VARCHAR(255) NOT NULL , `permissionLevel` INT NOT NULL , `Meaning` VARCHAR(255) NOT NULL , `Color` VARCHAR(255) NOT NULL , `Notes` VARCHAR(1023) NULL) ENGINE = InnoDB;
ALTER TABLE `operationsData`.`packapps_app_permissions` ADD PRIMARY KEY (`packapp`, `permissionLevel`);
ALTER TABLE `packapps_app_permissions` ADD CONSTRAINT `app_permissions_2_appProperties` FOREIGN KEY (`packapp`) REFERENCES `packapps_appProperties`(`short_app_name`) ON DELETE CASCADE ON UPDATE CASCADE;
INSERT INTO packapps_app_permissions (packapp, permissionLevel, Meaning, Color, Notes) VALUES ('quality', '1', 'Weight Input Only', 'Red', 'Redirects immediately to phone-based RT weighing.');
INSERT INTO packapps_app_permissions (packapp, permissionLevel, Meaning, Color, Notes) VALUES ('quality', '2', 'Receipt Inspector', 'Orange', 'Redirects to phone-based RT inspection.');
INSERT INTO packapps_app_permissions (packapp, permissionLevel, Meaning, Color, Notes) VALUES ('quality', '3', 'Full', 'Green', 'Complete access to QA system functions');
INSERT INTO packapps_app_permissions (packapp, permissionLevel, Meaning, Color, Notes) VALUES ('production', '1', 'Read-Only', 'Orange', 'Access to schedule, inventory, and chat, but no edits.');
INSERT INTO packapps_app_permissions (packapp, permissionLevel, Meaning, Color, Notes) VALUES ('production', '2', 'Full', 'Green', 'Complete access to production system with edits.');
INSERT INTO packapps_app_permissions (packapp, permissionLevel, Meaning, Color, Notes) VALUES ('purchasing', '1', 'No Purchases', 'Orange', 'Can create items, take inventory, and receive inventory, but cannot register new purchases.');
INSERT INTO packapps_app_permissions (packapp, permissionLevel, Meaning, Color, Notes) VALUES ('purchasing', '2', 'Full', 'Green', 'Full access to all purchasing functions.');
INSERT INTO packapps_app_permissions (packapp, permissionLevel, Meaning, Color, Notes) VALUES ('maintenance', '1', 'Read-Only', 'Red', '');
INSERT INTO packapps_app_permissions (packapp, permissionLevel, Meaning, Color, Notes) VALUES ('maintenance', '2', 'Worker', 'Orange', '');
INSERT INTO packapps_app_permissions (packapp, permissionLevel, Meaning, Color, Notes) VALUES ('maintenance', '3', 'Full', 'Green', '');
INSERT INTO packapps_app_permissions (packapp, permissionLevel, Meaning, Color, Notes) VALUES ('storage', '1', 'Read-Only', 'Red', '');
INSERT INTO packapps_app_permissions (packapp, permissionLevel, Meaning, Color, Notes) VALUES ('storage', '2', 'Receiving', 'Orange', '');
INSERT INTO packapps_app_permissions (packapp, permissionLevel, Meaning, Color, Notes) VALUES ('storage', '3', 'Full', 'Green', '');
INSERT INTO packapps_app_permissions (packapp, permissionLevel, Meaning, Color, Notes) VALUES ('grower', '1', 'Read-Only', 'Red', '');
INSERT INTO packapps_app_permissions (packapp, permissionLevel, Meaning, Color, Notes) VALUES ('grower', '2', 'Full', 'Green', '');


/* rename master_users table to reflect it is part of packapps framework */
RENAME TABLE master_users TO packapps_master_users;

/* END PERMISSIONS */

/* MOVE CONFIG INFO TO DATABASE */

CREATE TABLE `production_lineNames` (
  `lineID` int(11) NOT NULL AUTO_INCREMENT,
  `lineName` varchar(255) NOT NULL,
  PRIMARY KEY (`lineID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `production_lineNames` (lineID, lineName) VALUES (1, 'Blue Line');
INSERT INTO `production_lineNames` (lineID, lineName) VALUES (2, 'Gray Line');
INSERT INTO `production_lineNames` (lineID, lineName) VALUES (3, 'Presizer');

/* END MOVE CONFIG INFO TO DATABASE */

/* MIGRATE OLD QUALITY TABLES */

/* quality tables did not have prefixes as they predate packapps. Add prefixes to quality tables */
RENAME TABLE AggregateWeightSamples TO quality_AggregateWeightSamples;
RENAME TABLE AlertEmails TO quality_AlertEmails;
RENAME TABLE AppleSamples TO quality_AppleSamples;
RENAME TABLE InspectedRTs TO quality_InspectedRTs;
RENAME TABLE run_inspections TO quality_run_inspections;
ALTER TABLE `quality_run_inspections` ADD CONSTRAINT `run_inspections_2_runs` FOREIGN KEY (`RunID`) REFERENCES `production_runs`(`RunID`) ON DELETE CASCADE ON UPDATE CASCADE;

/* Remove unused quality column */
ALTER TABLE `quality_UserData` DROP `DateCreated`;

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

/* END MIGRATE OLD QUALITY TABLES */

/* MAINTENANCE PACKAPP TABLES */

/* Add integration into purchasing */
INSERT INTO purchasing_ItemTypes (Type_Description, UnitOfMeasure, WeeksToResupply) VALUES ('Maintenance', 'Parts', 0);

CREATE TABLE `maintenance_purposes` (
  `purpose_id` int(11) NOT NULL AUTO_INCREMENT,
  `Purpose` varchar(255) NOT NULL,
  `Color` varchar(255) NOT NULL,
  PRIMARY KEY (`purpose_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/* Fill in seed data */
INSERT INTO maintenance_purposes (Purpose, Color) VALUES ('Replacement', 'yellow');
INSERT INTO maintenance_purposes (Purpose, Color) VALUES ('Repair', 'blue');
INSERT INTO maintenance_purposes (Purpose, Color) VALUES ('Improvement', 'green');
INSERT INTO maintenance_purposes (Purpose, Color) VALUES ('New System', 'orange');
INSERT INTO maintenance_purposes (Purpose, Color) VALUES ('R&D', 'red');


CREATE TABLE `maintenance_issues` (
  `issue_id` int(11) NOT NULL AUTO_INCREMENT,
  `purpose_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `issue_description` varchar(1023) NOT NULL,
  `createdBy` varchar(255) NOT NULL,
  `dateCreated` datetime NOT NULL,
  `isConfirmed` tinyint(1) NOT NULL,
  `confirmedBy` varchar(255) NOT NULL,
  `dateConfirmed` datetime NOT NULL,
  `isInProgress` tinyint(1) NOT NULL,
  `inProgressBy` varchar(255) NOT NULL,
  `dateInProgress` datetime NOT NULL,
  `isCompleted` tinyint(1) NOT NULL,
  `completedBy` varchar(255) NOT NULL,
  `solution_description` varchar(1023) NOT NULL,
  `dateCompleted` datetime NOT NULL,
  `assignedTo` varchar(255) NULL,
  `Location` int(11) NOT NULL,
  `hasPhotoAttached` tinyint(1) NOT NULL,
  `needsParts` tinyint(1) NOT NULL,
  PRIMARY KEY (`issue_id`),
  KEY `createdBy` (`createdBy`),
  KEY `title` (`title`),
  KEY `confirmedBy` (`confirmedBy`),
  KEY `inProgressBy` (`inProgressBy`),
  KEY `completedBy` (`completedBy`),
  KEY `Assignee` (`assignedTo`),
  KEY `Location` (`Location`),
  KEY `partsNeeded` (`needsParts`),
  KEY `purpose` (`purpose_id`),
  CONSTRAINT `maintenance_issues_ibfk_1` FOREIGN KEY (`assignedTo`) REFERENCES `packapps_master_users` (`username`),
  CONSTRAINT `maintenance_issues_ibfk_2` FOREIGN KEY (`completedBy`) REFERENCES `packapps_master_users` (`username`),
  CONSTRAINT `maintenance_issues_ibfk_3` FOREIGN KEY (`confirmedBy`) REFERENCES `packapps_master_users` (`username`),
  CONSTRAINT `maintenance_issues_ibfk_4` FOREIGN KEY (`createdBy`) REFERENCES `packapps_master_users` (`username`),
  CONSTRAINT `maintenance_issues_ibfk_5` FOREIGN KEY (`inProgressBy`) REFERENCES `packapps_master_users` (`username`),
  CONSTRAINT `purpose` FOREIGN KEY (`purpose_id`) REFERENCES `maintenance_purposes` (`purpose_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `maintenance_issues2purchasing_items` (
  `issue_id` int(11) NOT NULL,
  `part_id` int(11) NOT NULL,
  `numberNeeded` int(11) NOT NULL,
  KEY `issue_id` (`issue_id`),
  KEY `part_id` (`part_id`),
  CONSTRAINT `issueid` FOREIGN KEY (`issue_id`) REFERENCES `maintenance_issues` (`issue_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `itemid` FOREIGN KEY (`part_id`) REFERENCES `purchasing_Items` (`Item_ID`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `maintenance_systems` (
  `system_id` int(11) NOT NULL AUTO_INCREMENT,
  `system_name` varchar(255) NOT NULL,
  `location_id` int(11) NOT NULL,
  PRIMARY KEY (`system_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `maintenance_subsystems` (
  `system_id` int(11) NOT NULL,
  `subsystem_id` int(11) NOT NULL AUTO_INCREMENT,
  `subsystem_name` varchar(255) NOT NULL,
  PRIMARY KEY (`subsystem_id`),
  KEY `subsystem2systems` (`system_id`),
  CONSTRAINT `subsystem2systems` FOREIGN KEY (`system_id`) REFERENCES `maintenance_systems` (`system_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `maintenance_subsystemComponents` (
  `subsystem_id` int(11) NOT NULL,
  `component_id` int(11) NOT NULL AUTO_INCREMENT,
  `component_name` int(11) NOT NULL,
  PRIMARY KEY (`component_id`),
  KEY `components2subsystems` (`subsystem_id`),
  CONSTRAINT `components2subsystems` FOREIGN KEY (`subsystem_id`) REFERENCES `maintenance_subsystems` (`subsystem_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `maintenance_part_info` (
  `item_id` int(11) NOT NULL,
  `Manufacturer` varchar(255) NOT NULL,
  `Part_number` varchar(255) NOT NULL,
  `system_id` int(11) DEFAULT NULL,
  `subsystem_id` int(11) DEFAULT NULL,
  `component_id` int(11) DEFAULT NULL,
  KEY `part_id` (`item_id`),
  KEY `system` (`system_id`),
  KEY `subsystem` (`subsystem_id`),
  KEY `component` (`component_id`),
  CONSTRAINT `component` FOREIGN KEY (`component_id`) REFERENCES `maintenance_subsystemComponents` (`component_id`) ON DELETE SET NULL ON UPDATE SET NULL,
  CONSTRAINT `part_id` FOREIGN KEY (`item_id`) REFERENCES `purchasing_Items` (`Item_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `subsystem` FOREIGN KEY (`subsystem_id`) REFERENCES `maintenance_subsystems` (`subsystem_id`) ON DELETE SET NULL ON UPDATE SET NULL,
  CONSTRAINT `system` FOREIGN KEY (`system_id`) REFERENCES `maintenance_systems` (`system_id`) ON DELETE SET NULL ON UPDATE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


/* END MAINTENANCE PACKAPP TABLES */

/* STORAGE PACKAPP TABLES */



/* END STORAGE PACKAPP TABLES */

/* MOVE GROWER_PORTAL TABLES into operationsData, for packapp-erization of grower portal */

ALTER TABLE growerReporting.BULKRTCSV RENAME operationsData.BULKRTCSV;
ALTER TABLE growerReporting.`crop-estimates` RENAME operationsData.`grower_crop-estimates`;
ALTER TABLE growerReporting.crop_estimates_changes_timeseries RENAME operationsData.grower_crop_estimates_changes_timeseries;
ALTER TABLE growerReporting.growerCalendar RENAME operationsData.grower_growerCalendar;
ALTER TABLE growerReporting.GrowerData RENAME operationsData.grower_GrowerLogins;
ALTER TABLE growerReporting.GrowerGroups RENAME operationsData.grower_GrowerGroups;
ALTER TABLE growerReporting.Preharvest_Samples RENAME operationsData.grower_Preharvest_Samples;

DROP DATABASE growerReporting;

/* Table transformations and additions for new Grower Portal */
ALTER TABLE `grower_GrowerLogins` DROP PRIMARY KEY;
DELETE FROM `grower_GrowerLogins` WHERE isAdmin > 0;
/* Edge case: grower has crop-estimates tables but no login */
ALTER TABLE `grower_GrowerLogins` ADD `GrowerID` INT NOT NULL AUTO_INCREMENT FIRST, ADD PRIMARY KEY (`GrowerID`);
ALTER TABLE `grower_GrowerLogins` ADD `login_email` VARCHAR(255) NOT NULL AFTER `GrowerName`, ADD INDEX (`login_email`);
ALTER TABLE `grower_GrowerLogins` ADD `lastLogin` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `isMultiAccountUser`;
UPDATE `grower_GrowerLogins` SET `lastLogin`='0000-00-00';
INSERT INTO grower_GrowerLogins (GrowerCode, GrowerName, login_email, Password, isAdmin, isMultiAccountUser) (SELECT DISTINCT Grower, Grower, '', '', 0, 0 FROM `grower_crop-estimates` LEFT JOIN grower_GrowerLogins ON `grower_crop-estimates`.Grower=`grower_GrowerLogins`.GrowerCode WHERE GrowerCode IS NULL);
CREATE TABLE `grower_farms` (
  `growerID` int(11) NOT NULL,
  `farmID` int(11) NOT NULL AUTO_INCREMENT,
  `farmName` varchar(255) NOT NULL,
  PRIMARY KEY (`farmID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
CREATE TABLE `operationsData`.`grower_strains` ( `strain_ID` INT NOT NULL AUTO_INCREMENT , `variety_ID` INT NOT NULL , `strainName` VARCHAR(255) NOT NULL , PRIMARY KEY (`strain_ID`)) ENGINE = InnoDB;
CREATE TABLE `operationsData`.`grower_varieties` ( `commodityID` INT NOT NULL , `VarietyID` INT NOT NULL AUTO_INCREMENT , `VarietyName` VARCHAR(255) NOT NULL , PRIMARY KEY (`VarietyID`)) ENGINE = InnoDB;
CREATE TABLE `operationsData`.`grower_commodities` ( `commodity_ID` INT NOT NULL AUTO_INCREMENT , `commodity_name` VARCHAR(255) NOT NULL , PRIMARY KEY (`commodity_ID`)) ENGINE = InnoDB;
ALTER TABLE `grower_farms` ADD FOREIGN KEY (`growerID`) REFERENCES `grower_GrowerLogins`(`GrowerID`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `grower_varieties` ADD FOREIGN KEY (`commodityID`) REFERENCES `grower_commodities`(`commodity_ID`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `grower_strains` ADD FOREIGN KEY (`variety_ID`) REFERENCES `grower_varieties`(`VarietyID`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `grower_crop-estimates` ADD `farmID` INT NOT NULL AFTER `PK`;
ALTER TABLE `grower_crop-estimates` ADD `strainID` INT NOT NULL AFTER `farmID`;
/* create commodities */
INSERT INTO grower_commodities (commodity_name) (SELECT DISTINCT `Comm Desc` FROM `grower_crop-estimates`);
/* create farms */
INSERT INTO grower_farms (growerID, farmName) (SELECT DISTINCT grower_GrowerLogins.GrowerID, FarmDesc FROM `grower_crop-estimates` JOIN grower_GrowerLogins ON Grower=grower_GrowerLogins.GrowerCode);
/* Create Varieties */
INSERT INTO grower_varieties (commodityID, VarietyName) (SELECT DISTINCT commodity_ID, VarDesc FROM `grower_crop-estimates` JOIN `grower_commodities` ON `Comm Desc`=commodity_name);
/* create strains */
INSERT INTO grower_strains (variety_ID, strainName) (SELECT DISTINCT VarietyID, `Str Desc` FROM `grower_crop-estimates` JOIN grower_varieties ON VarDesc=VarietyName);
/* point blocks to strains and farms */
UPDATE `grower_crop-estimates`
  JOIN (SELECT farmID, grower_GrowerLogins.GrowerCode, farmname FROM grower_farms JOIN grower_GrowerLogins ON grower_farms.growerID = grower_GrowerLogins.GrowerID) t
    ON t.GrowerCode=`grower_crop-estimates`.Grower AND t.farmName=`grower_crop-estimates`.FarmDesc
SET `grower_crop-estimates`.farmID=t.farmID;
ALTER TABLE `grower_crop-estimates` ADD FOREIGN KEY (`farmID`) REFERENCES `grower_farms`(`farmID`) ON DELETE CASCADE ON UPDATE CASCADE;
UPDATE `grower_crop-estimates` JOIN grower_strains ON grower_strains.strainName=`Str Desc` SET strainID=strain_ID;
ALTER TABLE `grower_crop-estimates` ADD FOREIGN KEY (`strainID`) REFERENCES `grower_strains`(`strain_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

/* Remove redundant columns */
ALTER TABLE `grower_crop-estimates`
  DROP `Grower`,
  DROP `Comm Desc`,
  DROP `VarDesc`,
  DROP `FarmDesc`,
  DROP `Str Desc`,
  DROP `2016hail`;

/* Rename Views */
/*!50001 DROP TABLE IF EXISTS `CurYearReceived`*/;
/*!50001 DROP VIEW IF EXISTS `CurYearReceived`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
  /*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
  /*!50001 VIEW `grower_CurYearReceived` AS (select `BULKRTCSV`.`RT#` AS `RT#`,`BULKRTCSV`.`Sort Code` AS `Sort Code`,`BULKRTCSV`.`Crop Year` AS `Crop Year`,`BULKRTCSV`.`Grower` AS `Grower`,`BULKRTCSV`.`GrowerName` AS `GrowerName`,`BULKRTCSV`.`Class` AS `Class`,`BULKRTCSV`.`ClassDesc` AS `ClassDesc`,`BULKRTCSV`.`Commodity` AS `Commodity`,`BULKRTCSV`.`Comm Desc` AS `Comm Desc`,`BULKRTCSV`.`Variety` AS `Variety`,`BULKRTCSV`.`VarDesc` AS `VarDesc`,`BULKRTCSV`.`Strain` AS `Strain`,`BULKRTCSV`.`StrDesc` AS `StrDesc`,`BULKRTCSV`.`Farm` AS `Farm`,`BULKRTCSV`.`FarmDesc` AS `FarmDesc`,`BULKRTCSV`.`Block` AS `Block`,`BULKRTCSV`.`BlockDesc` AS `BlockDesc`,`BULKRTCSV`.`Lot` AS `Lot`,`BULKRTCSV`.`Date` AS `Date`,`BULKRTCSV`.`Pack` AS `Pack`,`BULKRTCSV`.`Size` AS `Size`,`BULKRTCSV`.`Qty` AS `Qty`,`BULKRTCSV`.`Bu` AS `Bu`,`BULKRTCSV`.`ItemNum` AS `ItemNum` from `BULKRTCSV` where `BULKRTCSV`.`Crop Year` = convert(substr(year(curdate()),4,1) using latin1)) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

/*!50001 DROP TABLE IF EXISTS `ReceivedandEstimates`*/;
/*!50001 DROP VIEW IF EXISTS `ReceivedandEstimates`*/;
# /*!50001 SET @saved_cs_client          = @@character_set_client */;
# /*!50001 SET @saved_cs_results         = @@character_set_results */;
# /*!50001 SET @saved_col_connection     = @@collation_connection */;
# /*!50001 SET character_set_client      = latin1 */;
# /*!50001 SET character_set_results     = latin1 */;
# /*!50001 SET collation_connection      = latin1_swedish_ci */;
# /*!50001 CREATE ALGORITHM=UNDEFINED */
#   /*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
#   /*!50001 VIEW `grower_ReceivedandEstimates` AS (select rtrim(`grower_crop-estimates`.`PK`) AS `BlockID`,rtrim(`grower_crop-estimates`.`Grower`) AS `Code`,rtrim(`grower_crop-estimates`.`Comm Desc`) AS `Commodity`,rtrim(`grower_crop-estimates`.`FarmDesc`) AS `Farm`,ifnull(rtrim(`grower_CurYearReceived`.`Farm`),'') AS `FarmCode`,rtrim(`grower_crop-estimates`.`BlockDesc`) AS `Block`,ifnull(rtrim(`grower_CurYearReceived`.`Block`),'') AS `BlockCode`,rtrim(`grower_crop-estimates`.`VarDesc`) AS `Variety`,rtrim(`grower_crop-estimates`.`Str Desc`) AS `Strain`,rtrim(`grower_crop-estimates`.`2014act`) AS `2014 Received`,rtrim(`grower_crop-estimates`.`2015act`) AS `2015 Received`,rtrim(`grower_crop-estimates`.`2016act`) AS `2016 Received`,rtrim(case when `grower_crop-estimates`.`isDeleted` = 0 then `grower_crop-estimates`.`2016est` else 0 end) AS `2016 Estimate`,ifnull(sum(`grower_CurYearReceived`.`Bu`),'0') AS `2017 Received`,case when `grower_crop-estimates`.`isDeleted` = 0 then 'false' else 'true' end AS `isDeletedBlock`,case when `grower_crop-estimates`.`isFinished` = 0 then 'false' else 'true' end AS `isDonePicking`,case when (`grower_crop-estimates`.`2017est` <> `grower_crop-estimates`.`2016act` or `grower_crop-estimates`.`isSameAsLastYear` = 1) then 'true' else 'false' end AS `isUserConfirmedEstimate` from (`grower_crop-estimates` left join `grower_CurYearReceived` on(rtrim(`grower_CurYearReceived`.`Comm Desc`) = rtrim(`grower_crop-estimates`.`Comm Desc`) and rtrim(`grower_CurYearReceived`.`VarDesc` = rtrim(`grower_crop-estimates`.`VarDesc`)) and rtrim(`grower_CurYearReceived`.`StrDesc` = rtrim(`grower_crop-estimates`.`Str Desc`)) and rtrim(`grower_CurYearReceived`.`BlockDesc` = rtrim(`grower_crop-estimates`.`BlockDesc`)) and rtrim(`grower_CurYearReceived`.`FarmDesc` = rtrim(`grower_crop-estimates`.`FarmDesc`)) and rtrim(`grower_CurYearReceived`.`Grower` = rtrim(`grower_crop-estimates`.`Grower`)))) group by `grower_crop-estimates`.`PK`) union (select 'Unmatched Block' AS `BlockID`,rtrim(`BULKRTCSV`.`Grower`) AS `Code`,rtrim(`BULKRTCSV`.`Comm Desc`) AS `Commodity`,rtrim(`BULKRTCSV`.`FarmDesc`) AS `Farm`,rtrim(`BULKRTCSV`.`Farm`) AS `FarmCode`,rtrim(`BULKRTCSV`.`BlockDesc`) AS `Block`,rtrim(`BULKRTCSV`.`Block`) AS `BlockCode`,rtrim(`BULKRTCSV`.`VarDesc`) AS `Variety`,rtrim(`BULKRTCSV`.`StrDesc`) AS `Strain`,'0' AS `2014 Received`,'0' AS `2015 Received`,'0' AS `2016 Received`,'0' AS `2017 Estimate`,sum(`BULKRTCSV`.`Bu`) AS `2017 Received`,'false' AS `isDeletedBlock`,'false' AS `isDonePicking`,'false' AS `isUserConfirmedEstimate` from (`BULKRTCSV` left join `grower_crop-estimates` on(rtrim(`BULKRTCSV`.`Comm Desc`) = rtrim(`grower_crop-estimates`.`Comm Desc`) and rtrim(`BULKRTCSV`.`VarDesc` = rtrim(`grower_crop-estimates`.`VarDesc`)) and rtrim(`BULKRTCSV`.`StrDesc` = rtrim(`grower_crop-estimates`.`Str Desc`)) and rtrim(`BULKRTCSV`.`BlockDesc` = rtrim(`grower_crop-estimates`.`BlockDesc`)) and rtrim(`BULKRTCSV`.`FarmDesc` = rtrim(`grower_crop-estimates`.`FarmDesc`)) and rtrim(`BULKRTCSV`.`Grower` = rtrim(`grower_crop-estimates`.`Grower`)))) where `grower_crop-estimates`.`PK` is null and `BULKRTCSV`.`Crop Year` = convert(substr(year(curdate()),4,1) using latin1) group by `BULKRTCSV`.`Grower`,`BULKRTCSV`.`Comm Desc`,`BULKRTCSV`.`FarmDesc`,`BULKRTCSV`.`BlockDesc`,`BULKRTCSV`.`VarDesc`,`BULKRTCSV`.`StrDesc`) */;
# /*!50001 SET character_set_client      = @saved_cs_client */;
# /*!50001 SET character_set_results     = @saved_cs_results */;
# /*!50001 SET collation_connection      = @saved_col_connection */;

/* END MOVE GROWER_PORTAL TABLES */