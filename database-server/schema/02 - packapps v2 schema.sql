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

/* Create new UserData tables for new packapps and pre-populate with usernames */
CREATE TABLE `operationsData`.`storage_UserData` ( `UserName` VARCHAR(255) NOT NULL , `Role` ENUM('readonly','receiving','full') NOT NULL ) ENGINE = InnoDB;
ALTER TABLE `operationsData`.`storage_UserData` ADD PRIMARY KEY (`UserName`);
ALTER TABLE `storage_UserData` ADD CONSTRAINT `storageuserdata2masterusers` FOREIGN KEY (`UserName`) REFERENCES `master_users`(`username`) ON DELETE CASCADE ON UPDATE CASCADE;
INSERT INTO storage_UserData (UserName) SELECT username FROM master_users;

CREATE TABLE `operationsData`.`maintenance_UserData` ( `UserName` VARCHAR(255) NOT NULL , `Role` ENUM('readonly','worker','readwrite') NOT NULL ) ENGINE = InnoDB;
ALTER TABLE `operationsData`.`maintenance_UserData` ADD PRIMARY KEY (`UserName`);
ALTER TABLE `maintenance_UserData` ADD CONSTRAINT `maintenanceuserdata2masterusers` FOREIGN KEY (`UserName`) REFERENCES `master_users`(`username`) ON DELETE CASCADE ON UPDATE CASCADE;
INSERT INTO maintenance_UserData (UserName) SELECT username FROM master_users;

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

CREATE TABLE `maintenance_issues` (
  `issue_id` int(11) NOT NULL AUTO_INCREMENT,
  `purpose_id` int(11) NOT NULL,
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
  `assignedTo` varchar(255) NOT NULL,
  `Location` int(11) NOT NULL,
  `hasPhotoAttached` tinyint(1) NOT NULL,
  `needsParts` tinyint(1) NOT NULL,
  PRIMARY KEY (`issue_id`),
  KEY `createdBy` (`createdBy`),
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
  CONSTRAINT `itemid` FOREIGN KEY (`part_id`) REFERENCES `purchasing_Items` (`Item_ID`) ON DELETE CASCADE ON UPDATE CASCADE
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
