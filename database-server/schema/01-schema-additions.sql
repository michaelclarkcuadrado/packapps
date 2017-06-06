/* Database Migrations */
USE operationsData;

/*Add permissions columns to master_users */
ALTER TABLE master_users ADD COLUMN allowedStorage TINYINT(1) DEFAULT 0 NOT NULL AFTER allowedProduction;
ALTER TABLE master_users ADD COLUMN allowedMaintenance TINYINT(1) DEFAULT 0 NOT NULL AFTER allowedStorage;

/*Create new UserData tables for new packapps and pre-populate with usernames*/
CREATE TABLE `operationsData`.`storage_UserData` ( `username` VARCHAR(255) NOT NULL , `privlegeLevel` ENUM('readonly','full') NOT NULL ) ENGINE = InnoDB;
ALTER TABLE `operationsData`.`storage_UserData` ADD PRIMARY KEY (`username`);
ALTER TABLE `storage_UserData` ADD CONSTRAINT `storageuserdata2masterusers` FOREIGN KEY (`username`) REFERENCES `master_users`(`username`) ON DELETE CASCADE ON UPDATE CASCADE;
INSERT INTO storage_UserData (username) SELECT username FROM master_users;

/*Create new UserData tables for new packapps and pre-populate with usernames*/
CREATE TABLE `operationsData`.`maintenance_UserData` ( `username` VARCHAR(255) NOT NULL , `privlegeLevel` ENUM('readonly','worker','readwrite') NOT NULL ) ENGINE = InnoDB;
ALTER TABLE `operationsData`.`maintenance_UserData` ADD PRIMARY KEY (`username`);
ALTER TABLE `maintenance_UserData` ADD CONSTRAINT `maintenanceuserdata2masterusers` FOREIGN KEY (`username`) REFERENCES `master_users`(`username`) ON DELETE CASCADE ON UPDATE CASCADE;
INSERT INTO maintenance_UserData (username) SELECT username FROM master_users;

/*Create new packapp_appProperties table to support more standardized modules*/
CREATE TABLE `operationsData`.`packapps_appProperties` ( `app_id` INT NOT NULL AUTO_INCREMENT, `short_app_name` VARCHAR(255) NOT NULL , `long_app_name` VARCHAR(255) NOT NULL , `material_icon_name` VARCHAR(255) NOT NULL , `isEnabled` TINYINT(1) NOT NULL DEFAULT '1' , `Notes` VARCHAR(255) NOT NULL, PRIMARY KEY (app_id) ) ENGINE = InnoDB;
ALTER TABLE `operationsData`.`packapps_appProperties` ADD UNIQUE `unique_app_short_names` (`short_app_name`);

/*Add existing packapps to above table*/
INSERT INTO packapps_appProperties (short_app_name, long_app_name, material_icon_name, isEnabled, Notes) VALUES ('quality', 'Quality Assurance Panel', 'check_circle', 1, '');
INSERT INTO packapps_appProperties (short_app_name, long_app_name, material_icon_name, isEnabled, Notes) VALUES ('production', 'Production Coordinator', 'list', 1, '');
INSERT INTO packapps_appProperties (short_app_name, long_app_name, material_icon_name, isEnabled, Notes) VALUES ('purchasing', 'Purchasing Dashboard', 'dashboard', 1, '');
INSERT INTO packapps_appProperties (short_app_name, long_app_name, material_icon_name, isEnabled, Notes) VALUES ('maintenance', 'Maintenance Dashboard', 'build', 1, '');
INSERT INTO packapps_appProperties (short_app_name, long_app_name, material_icon_name, isEnabled, Notes) VALUES ('storage', 'Storage Insights', 'track_changes', 1, '');
