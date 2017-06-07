-- This defines the schema as it existed in v1.0 of packapps. For compatability, it may not be changed.
-- To change the database, find the version number you are targeting and migrate from the one below it.
-- Tp upgrade an existing server, delete the schema version it uses from this folder, and any older schemas, before building. 

-- MySQL dump 10.16  Distrib 10.2.6-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: operationsData
-- ------------------------------------------------------
-- Server version	10.2.6-MariaDB-10.2.6+maria~xenial-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Current Database: `operationsData`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `operationsData` /*!40100 DEFAULT CHARACTER SET latin1 */;

USE `operationsData`;

--
-- Table structure for table `AggregateWeightSamples`
--

DROP TABLE IF EXISTS `AggregateWeightSamples`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `AggregateWeightSamples` (
  `RT#` int(11) NOT NULL,
  `Weight` float NOT NULL,
  `InspectorName` varchar(255) NOT NULL,
  KEY `RT#` (`RT#`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Weight in lbs';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `AlertEmails`
--

DROP TABLE IF EXISTS `AlertEmails`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `AlertEmails` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `FullName` varchar(255) NOT NULL,
  `EmailAddress` varchar(255) NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `FullName` (`FullName`,`EmailAddress`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `AppleSamples`
--

DROP TABLE IF EXISTS `AppleSamples`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `AppleSamples` (
  `RT#` int(11) NOT NULL,
  `SampleNum` tinyint(11) NOT NULL,
  `Pressure1` varchar(50) DEFAULT NULL,
  `Pressure2` varchar(50) DEFAULT NULL,
  `DA` varchar(50) DEFAULT NULL,
  `DA2` varchar(50) DEFAULT NULL,
  `Brix` varchar(50) DEFAULT NULL,
  `Weight` varchar(50) DEFAULT NULL,
  `Starch` varchar(3) DEFAULT NULL,
  `FinalTestedBy` varchar(255) DEFAULT NULL,
  `FinalInspectionDate` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `PrAvg` varchar(50) GENERATED ALWAYS AS (round((`Pressure1` + `Pressure2`) / 2,3)) VIRTUAL,
  `DAAvg` varchar(50) GENERATED ALWAYS AS (round((`DA` + `DA2`) / 2,2)) VIRTUAL,
  PRIMARY KEY (`RT#`,`SampleNum`),
  KEY `RT#` (`RT#`),
  KEY `FinalInspectionDate` (`FinalInspectionDate`),
  CONSTRAINT `AppleSamples_ibfk_1` FOREIGN KEY (`RT#`) REFERENCES `InspectedRTs` (`RTNum`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary table structure for view `AvgWeightByRT`
--

DROP TABLE IF EXISTS `AvgWeightByRT`;
/*!50001 DROP VIEW IF EXISTS `AvgWeightByRT`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `AvgWeightByRT` (
  `RTNum` tinyint NOT NULL,
  `WeightAvg` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `BULKOHCSV`
--

DROP TABLE IF EXISTS `BULKOHCSV`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `BULKOHCSV` (
  `RT#` int(5) NOT NULL DEFAULT 0,
  `SortCode` varchar(15) DEFAULT NULL,
  `CropYear` int(1) DEFAULT NULL,
  `Grower` varchar(2) DEFAULT NULL,
  `GrowerName` varchar(18) DEFAULT NULL,
  `Class` int(1) DEFAULT NULL,
  `ClassDesc` varchar(20) DEFAULT NULL,
  `Commodity` int(2) DEFAULT NULL,
  `CommDesc` varchar(16) DEFAULT NULL,
  `Variety` int(2) DEFAULT NULL,
  `VarDesc` varchar(16) DEFAULT NULL,
  `Strain` varchar(2) DEFAULT NULL,
  `StrDesc` varchar(16) DEFAULT NULL,
  `Farm` varchar(2) DEFAULT NULL,
  `FarmDesc` varchar(18) DEFAULT NULL,
  `Block` varchar(2) DEFAULT NULL,
  `BlockDesc` varchar(18) DEFAULT NULL,
  `Lot` varchar(5) DEFAULT NULL,
  `Date` varchar(10) DEFAULT NULL,
  `Size` varchar(5) DEFAULT NULL,
  `Pack` varchar(3) DEFAULT NULL,
  `QtyOnHand` varchar(7) DEFAULT NULL,
  `BuOnHand` varchar(7) DEFAULT NULL,
  `Location` varchar(1) DEFAULT NULL,
  `LocationDesc` varchar(20) DEFAULT NULL,
  `RoomNum` int(2) DEFAULT NULL,
  `CoNum` int(1) DEFAULT NULL,
  `Company Name` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Updated every minute';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `BULKTKCSV`
--

DROP TABLE IF EXISTS `BULKTKCSV`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `BULKTKCSV` (
  `RT#` int(5) DEFAULT NULL,
  `Sort Code` varchar(15) DEFAULT NULL,
  `Crop Year` int(1) DEFAULT NULL,
  `Grower` varchar(2) DEFAULT NULL,
  `Grower Name` varchar(18) DEFAULT NULL,
  `Class` int(1) DEFAULT NULL,
  `Class Desc` varchar(20) DEFAULT NULL,
  `Commodity` int(2) DEFAULT NULL,
  `Comm Desc` varchar(16) DEFAULT NULL,
  `Variety` int(2) DEFAULT NULL,
  `Var Desc` varchar(16) DEFAULT NULL,
  `Strain` varchar(2) DEFAULT NULL,
  `Str Desc` varchar(16) DEFAULT NULL,
  `Farm` varchar(2) DEFAULT NULL,
  `Farm Desc` varchar(18) DEFAULT NULL,
  `Block` varchar(2) DEFAULT NULL,
  `Block Desc` varchar(18) DEFAULT NULL,
  `Lot` varchar(5) DEFAULT NULL,
  `Date` varchar(10) DEFAULT NULL,
  `Size` varchar(5) DEFAULT NULL,
  `Pack` varchar(3) DEFAULT NULL,
  `Ticket#` varchar(8) DEFAULT NULL,
  `Qty In` varchar(7) DEFAULT NULL,
  ` Qty Out` varchar(7) DEFAULT NULL,
  `Qty On Hand` varchar(7) DEFAULT NULL,
  `Location` int(1) DEFAULT NULL,
  `Location Desc` varchar(20) DEFAULT NULL,
  ` Room#` int(2) DEFAULT NULL,
  `Pack Wt` varchar(7) DEFAULT NULL,
  `Bu Wt` varchar(4) DEFAULT NULL,
  `Bu On Hand` varchar(13) DEFAULT NULL,
  `Co#` int(1) DEFAULT NULL,
  ` Company Name` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary table structure for view `Block_Receiving`
--

DROP TABLE IF EXISTS `Block_Receiving`;
/*!50001 DROP VIEW IF EXISTS `Block_Receiving`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `Block_Receiving` (
  `Grower` tinyint NOT NULL,
  `CommDesc` tinyint NOT NULL,
  `Farm` tinyint NOT NULL,
  `Block` tinyint NOT NULL,
  `VarDesc` tinyint NOT NULL,
  `Strain` tinyint NOT NULL,
  `Pressure1` tinyint NOT NULL,
  `Pressure2` tinyint NOT NULL,
  `Brix` tinyint NOT NULL,
  `DA` tinyint NOT NULL,
  `DA2` tinyint NOT NULL,
  `Count` tinyint NOT NULL,
  `Weight` tinyint NOT NULL,
  `Starch` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `InspectedRTs`
--

DROP TABLE IF EXISTS `InspectedRTs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `InspectedRTs` (
  `RTNum` int(11) NOT NULL,
  `#Samples` int(11) NOT NULL,
  `Color Quality` enum('Good','Fair','Poor','Green','Yellow') NOT NULL,
  `Blush` tinyint(1) NOT NULL,
  `Bruise` enum('Light','Severe','None','Heavy') NOT NULL,
  `BitterPit` tinyint(1) NOT NULL,
  `Russet` enum('None','Light','Moderate','Heavy','Severe') NOT NULL,
  `Scab` enum('None','Light','Moderate','Heavy','Severe') NOT NULL,
  `StinkBug` enum('None','Light','Moderate','Heavy','Severe') NOT NULL,
  `SanJoseScale` enum('None','Light','Moderate','Heavy','Severe') NOT NULL,
  `SunBurn` enum('None','Light','Moderate','Heavy','Severe') NOT NULL,
  `Note` varchar(255) NOT NULL,
  `DateInspected` timestamp NOT NULL DEFAULT current_timestamp(),
  `InspectedBy` varchar(255) NOT NULL,
  `isFinalInspected` tinyint(1) NOT NULL,
  `FTAup` tinyint(1) NOT NULL DEFAULT 0,
  `DAFinished` tinyint(1) NOT NULL,
  `StarchFinished` tinyint(1) NOT NULL,
  PRIMARY KEY (`RTNum`),
  KEY `isFinalInspected` (`isFinalInspected`),
  KEY `DateInspected` (`DateInspected`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `PSOHCSV`
--

DROP TABLE IF EXISTS `PSOHCSV`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `PSOHCSV` (
  `Ticket#` varchar(255) NOT NULL,
  `Crop Year` varchar(255) NOT NULL,
  `Run#` int(11) NOT NULL,
  `Commodity` varchar(255) NOT NULL,
  `Commodity Desc` varchar(255) NOT NULL,
  `Variety` varchar(255) NOT NULL,
  `Var Desc` varchar(255) NOT NULL,
  `Grade` varchar(255) NOT NULL,
  `Grade Desc` varchar(255) NOT NULL,
  `Size` varchar(255) NOT NULL,
  `Size Desc` varchar(255) NOT NULL,
  `LotA` varchar(255) NOT NULL,
  `LotB` varchar(255) NOT NULL,
  `LotC` varchar(255) NOT NULL,
  `Grower` varchar(255) NOT NULL,
  `Grower Desc` varchar(255) NOT NULL,
  `Bin Size` varchar(255) NOT NULL,
  `Trtmt` varchar(255) NOT NULL,
  `Location` varchar(255) NOT NULL,
  `Location Desc` varchar(255) NOT NULL,
  `Room#` varchar(255) NOT NULL,
  `Origin` varchar(255) NOT NULL,
  `Date` varchar(255) NOT NULL,
  `In_PSOHCSV` varchar(255) NOT NULL,
  `Out_PSOHCSV` varchar(255) NOT NULL,
  `On Hand` varchar(255) NOT NULL,
  `Co#` varchar(255) NOT NULL,
  `Company Name` varchar(255) NOT NULL,
  KEY `Var Desc` (`Var Desc`),
  KEY `Grade Desc` (`Grade Desc`),
  KEY `Size` (`Size`),
  KEY `LotB` (`LotB`),
  KEY `Grower Desc` (`Grower Desc`),
  KEY `Run#` (`Run#`),
  KEY `Size Desc` (`Size Desc`),
  KEY `Grower` (`Grower`),
  KEY `Ticket#` (`Ticket#`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `PSOHCSV_flagged_bad_runs`
--

DROP TABLE IF EXISTS `PSOHCSV_flagged_bad_runs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `PSOHCSV_flagged_bad_runs` (
  `Run` int(11) NOT NULL,
  `Date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `isBad` tinyint(1) NOT NULL DEFAULT 1,
  UNIQUE KEY `Run_2` (`Run`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary table structure for view `RTsWQuality`
--

DROP TABLE IF EXISTS `RTsWQuality`;
/*!50001 DROP VIEW IF EXISTS `RTsWQuality`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `RTsWQuality` (
  `RT#` tinyint NOT NULL,
  `SortCode` tinyint NOT NULL,
  `Crop Year` tinyint NOT NULL,
  `Grower` tinyint NOT NULL,
  `Grower Name` tinyint NOT NULL,
  `Class` tinyint NOT NULL,
  `Class Desc` tinyint NOT NULL,
  `Commodity` tinyint NOT NULL,
  `CommDesc` tinyint NOT NULL,
  `Variety` tinyint NOT NULL,
  `Var Desc` tinyint NOT NULL,
  `Strain` tinyint NOT NULL,
  `Str Desc` tinyint NOT NULL,
  `Farm` tinyint NOT NULL,
  `Farm Desc` tinyint NOT NULL,
  `Block` tinyint NOT NULL,
  `Block Desc` tinyint NOT NULL,
  `Lot` tinyint NOT NULL,
  `Date` tinyint NOT NULL,
  `Size` tinyint NOT NULL,
  `Pack` tinyint NOT NULL,
  `QtyOnHand` tinyint NOT NULL,
  `BuOnHand` tinyint NOT NULL,
  `Location` tinyint NOT NULL,
  `Co#` tinyint NOT NULL,
  `Company Name` tinyint NOT NULL,
  `isQA` tinyint NOT NULL,
  `PressureAvg` tinyint NOT NULL,
  `DAAvg` tinyint NOT NULL,
  `Brix` tinyint NOT NULL,
  `Starch` tinyint NOT NULL,
  `Color` tinyint NOT NULL,
  `Bruise` tinyint NOT NULL,
  `BitterPit` tinyint NOT NULL,
  `Russet` tinyint NOT NULL,
  `Sunburn` tinyint NOT NULL,
  `San Jose Scale` tinyint NOT NULL,
  `Scab` tinyint NOT NULL,
  `StinkBug` tinyint NOT NULL,
  `AverageWeight` tinyint NOT NULL,
  `SizefromAverage` tinyint NOT NULL,
  `Notes` tinyint NOT NULL,
  `InspectedBy` tinyint NOT NULL,
  `DateTested` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `master_users`
--

DROP TABLE IF EXISTS `master_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `master_users` (
  `username` varchar(255) NOT NULL,
  `Real Name` varchar(255) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `lastLogin` datetime NOT NULL,
  `isDisabled` tinyint(1) NOT NULL DEFAULT 0,
  `allowedQuality` tinyint(1) NOT NULL DEFAULT 0,
  `allowedPurchasing` tinyint(1) NOT NULL DEFAULT 0,
  `allowedProduction` tinyint(1) NOT NULL DEFAULT 0,
  `isSystemAdministrator` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `production_ConnectedDisplays`
--

DROP TABLE IF EXISTS `production_ConnectedDisplays`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `production_ConnectedDisplays` (
  `IP_addr` varchar(45) NOT NULL,
  `connected_line` varchar(10) NOT NULL,
  `last_seen` datetime NOT NULL,
  `User_agent` varchar(1000) NOT NULL,
  PRIMARY KEY (`IP_addr`),
  KEY `User_agent` (`User_agent`(767))
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `production_UserData`
--

DROP TABLE IF EXISTS `production_UserData`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `production_UserData` (
  `UserName` varchar(255) NOT NULL,
  `Role` enum('ReadOnly','Production') NOT NULL,
  `DateCreated` datetime NOT NULL DEFAULT current_timestamp(),
  `isSectionManager` tinyint(1) NOT NULL,
  `chatLastOnline_blue` datetime NOT NULL DEFAULT current_timestamp(),
  `chatLastOnline_gray` datetime NOT NULL DEFAULT current_timestamp(),
  `chatLastOnline_presizer` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`UserName`),
  KEY `UserName` (`UserName`),
  CONSTRAINT `production_UserData_ibfk_1` FOREIGN KEY (`UserName`) REFERENCES `master_users` (`username`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `production_chat`
--

DROP TABLE IF EXISTS `production_chat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `production_chat` (
  `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `Line` enum('blue','gray','presizer') NOT NULL,
  `User` varchar(255) NOT NULL,
  `Message` varchar(255) NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `Line` (`Line`),
  KEY `User` (`User`)
) ENGINE=InnoDB AUTO_INCREMENT=4990 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `production_dumped_fruit`
--

DROP TABLE IF EXISTS `production_dumped_fruit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `production_dumped_fruit` (
  `isNOT` tinyint(1) NOT NULL,
  `RunID` int(11) NOT NULL,
  `Grower` varchar(14) NOT NULL,
  `Variety` varchar(255) NOT NULL,
  `Quality` varchar(255) NOT NULL,
  `Size` varchar(255) NOT NULL,
  `Lot` varchar(10) NOT NULL,
  `Location` varchar(255) NOT NULL,
  `AmountToDump` int(11) NOT NULL,
  KEY `RunID` (`RunID`),
  CONSTRAINT `RunID` FOREIGN KEY (`RunID`) REFERENCES `production_runs` (`RunID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `production_product_needed`
--

DROP TABLE IF EXISTS `production_product_needed`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `production_product_needed` (
  `RunID` int(11) NOT NULL,
  `ProductNeededName` varchar(255) NOT NULL,
  `PackSizeNeeded` varchar(255) NOT NULL,
  `AmountNeeded` int(11) DEFAULT NULL,
  `amountIsInBoxes` tinyint(1) NOT NULL,
  KEY `RunID` (`RunID`),
  CONSTRAINT `RunID2` FOREIGN KEY (`RunID`) REFERENCES `production_runs` (`RunID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `production_runs`
--

DROP TABLE IF EXISTS `production_runs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `production_runs` (
  `RunID` int(11) NOT NULL AUTO_INCREMENT,
  `RunNumber` int(11) DEFAULT NULL,
  `Line` enum('Blue','Gray','Presizer','') NOT NULL,
  `isCompleted` tinyint(1) NOT NULL,
  `isPreInspected` tinyint(1) NOT NULL,
  `isQA` tinyint(1) NOT NULL,
  `lastEdited` timestamp NOT NULL DEFAULT current_timestamp(),
  `lastEditedBy` varchar(255) NOT NULL,
  PRIMARY KEY (`RunID`),
  KEY `isCompleted` (`isCompleted`),
  KEY `Line` (`Line`),
  KEY `lastEdited` (`lastEdited`),
  KEY `RunNumber` (`RunNumber`)
) ENGINE=InnoDB AUTO_INCREMENT=2329 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `production_tempRunData`
--

DROP TABLE IF EXISTS `production_tempRunData`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `production_tempRunData` (
  `PK` int(11) NOT NULL,
  `Variety` varchar(255) NOT NULL,
  `Grade` varchar(255) NOT NULL,
  `Size` varchar(255) NOT NULL,
  `Grower` varchar(255) NOT NULL,
  `Lot` varchar(10) NOT NULL,
  `Location` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Holds run info while it is processed to and from json';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `purchasing_EnvioAddon_EnvioAssets2purchasingItems`
--

DROP TABLE IF EXISTS `purchasing_EnvioAddon_EnvioAssets2purchasingItems`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `purchasing_EnvioAddon_EnvioAssets2purchasingItems` (
  `AssetID` int(11) NOT NULL,
  `ItemID` int(11) NOT NULL,
  `numItemAtomsInAsset` int(11) NOT NULL,
  PRIMARY KEY (`AssetID`,`ItemID`),
  KEY `ItemID` (`ItemID`),
  KEY `AssetID` (`AssetID`),
  CONSTRAINT `purchasing_EnvioAddon_EnvioAssets2purchasingItems_ibfk_1` FOREIGN KEY (`AssetID`) REFERENCES `purchasing_EnvioAddon_envioAssets` (`SKU_ID`),
  CONSTRAINT `purchasing_EnvioAddon_EnvioAssets2purchasingItems_ibfk_2` FOREIGN KEY (`ItemID`) REFERENCES `purchasing_Items` (`Item_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `purchasing_EnvioAddon_ItemInventoryFractions`
--

DROP TABLE IF EXISTS `purchasing_EnvioAddon_ItemInventoryFractions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `purchasing_EnvioAddon_ItemInventoryFractions` (
  `ItemID` int(11) NOT NULL,
  `Current_units_in_Part` int(11) NOT NULL,
  PRIMARY KEY (`ItemID`),
  CONSTRAINT `purchasing_EnvioAddon_ItemInventoryFractions_ibfk_1` FOREIGN KEY (`ItemID`) REFERENCES `purchasing_Items` (`Item_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `purchasing_EnvioAddon_envioAssets`
--

DROP TABLE IF EXISTS `purchasing_EnvioAddon_envioAssets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `purchasing_EnvioAddon_envioAssets` (
  `SKU_ID` int(11) NOT NULL,
  `SKU_desc` varchar(255) NOT NULL,
  `lastChecked_Date` datetime NOT NULL,
  PRIMARY KEY (`SKU_ID`),
  UNIQUE KEY `AssetDesc` (`SKU_desc`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Individual SKUs from Envio';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `purchasing_Inventory_TimeSeries`
--

DROP TABLE IF EXISTS `purchasing_Inventory_TimeSeries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `purchasing_Inventory_TimeSeries` (
  `TimeReceived` datetime NOT NULL,
  `ItemID` int(11) NOT NULL,
  `Quantity` int(11) NOT NULL,
  `Type` enum('ManualInventoryChange','PackageReceived','BOMchange','') NOT NULL,
  PRIMARY KEY (`TimeReceived`,`ItemID`),
  KEY `ItemID` (`ItemID`),
  KEY `TimeReceived` (`TimeReceived`),
  CONSTRAINT `purchasing_Inventory_TimeSeries_ibfk_1` FOREIGN KEY (`ItemID`) REFERENCES `purchasing_Items` (`Item_ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `purchasing_ItemTypes`
--

DROP TABLE IF EXISTS `purchasing_ItemTypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `purchasing_ItemTypes` (
  `Type_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Type_Description` varchar(55) NOT NULL,
  `UnitOfMeasure` varchar(255) NOT NULL DEFAULT 'Units',
  `WeeksToResupply` int(11) NOT NULL,
  PRIMARY KEY (`Type_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `purchasing_Items`
--

DROP TABLE IF EXISTS `purchasing_Items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `purchasing_Items` (
  `Item_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Type_ID` int(11) NOT NULL,
  `ItemDesc` varchar(255) NOT NULL,
  `isDisabled` tinyint(1) NOT NULL DEFAULT 0,
  `AmountInStock` int(11) NOT NULL DEFAULT 0,
  `QtyPerUnit` int(11) NOT NULL,
  PRIMARY KEY (`Item_ID`),
  KEY `Type_ID` (`Type_ID`),
  CONSTRAINT `purchasing_Items_ibfk_1` FOREIGN KEY (`Type_ID`) REFERENCES `purchasing_ItemTypes` (`Type_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=224 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `purchasing_Suppliers`
--

DROP TABLE IF EXISTS `purchasing_Suppliers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `purchasing_Suppliers` (
  `SupplierID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(255) NOT NULL,
  `ContactName` varchar(255) NOT NULL,
  `ContactPhone` varchar(20) NOT NULL,
  `ContactEmail` varchar(255) NOT NULL,
  `InternalContact` varchar(255) NOT NULL,
  `hasFoodSafetyDocuments` tinyint(1) NOT NULL DEFAULT 0,
  `preferredContactMethod` enum('Phone','Email','Website','') NOT NULL,
  `lastInteracted` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`SupplierID`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `purchasing_UserData`
--

DROP TABLE IF EXISTS `purchasing_UserData`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `purchasing_UserData` (
  `UserName` varchar(255) NOT NULL,
  `isAuthorizedForPurchases` tinyint(1) NOT NULL,
  `isSectionManager` tinyint(1) NOT NULL,
  PRIMARY KEY (`UserName`),
  CONSTRAINT `purchasing_UserData_ibfk_1` FOREIGN KEY (`UserName`) REFERENCES `master_users` (`username`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `purchasing_items2suppliers`
--

DROP TABLE IF EXISTS `purchasing_items2suppliers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `purchasing_items2suppliers` (
  `ItemID` int(11) NOT NULL,
  `SupplierID` int(11) NOT NULL,
  `quotedPricePerUnit` float NOT NULL,
  UNIQUE KEY `Normalization` (`ItemID`,`SupplierID`),
  KEY `SupplierID` (`SupplierID`),
  KEY `ItemID` (`ItemID`),
  CONSTRAINT `purchasing_items2suppliers_ibfk_3` FOREIGN KEY (`SupplierID`) REFERENCES `purchasing_Suppliers` (`SupplierID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `purchasing_items2suppliers_ibfk_4` FOREIGN KEY (`ItemID`) REFERENCES `purchasing_Items` (`Item_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `purchasing_purchase_history`
--

DROP TABLE IF EXISTS `purchasing_purchase_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `purchasing_purchase_history` (
  `Purchase_ID` int(11) NOT NULL AUTO_INCREMENT,
  `DateOrdered` datetime DEFAULT current_timestamp(),
  `InitiatedBy` varchar(255) NOT NULL,
  `isReceived` tinyint(1) NOT NULL DEFAULT 0,
  `DateReceived` datetime NOT NULL DEFAULT current_timestamp(),
  `invoice_attached` tinyint(1) NOT NULL DEFAULT 0,
  `pack_slip_attached` tinyint(1) NOT NULL DEFAULT 0,
  `SupplierID` int(11) DEFAULT NULL,
  PRIMARY KEY (`Purchase_ID`),
  KEY `SupplierID` (`SupplierID`),
  CONSTRAINT `purchasing_purchase_history_ibfk_2` FOREIGN KEY (`SupplierID`) REFERENCES `purchasing_Suppliers` (`SupplierID`) ON DELETE SET NULL ON UPDATE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `purchasing_purchases2items`
--

DROP TABLE IF EXISTS `purchasing_purchases2items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `purchasing_purchases2items` (
  `Purchase_ID` int(11) NOT NULL,
  `Item_ID` int(11) NOT NULL,
  `QuantityOrdered` float NOT NULL,
  `PricePerUnit` double NOT NULL,
  `actualReceivedQuantity` float NOT NULL DEFAULT 0,
  KEY `Purchase_ID` (`Purchase_ID`),
  KEY `Item_ID` (`Item_ID`),
  CONSTRAINT `purchasing_purchases2items_ibfk_1` FOREIGN KEY (`Purchase_ID`) REFERENCES `purchasing_purchase_history` (`Purchase_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `purchasing_purchases2items_ibfk_2` FOREIGN KEY (`Item_ID`) REFERENCES `purchasing_Items` (`Item_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `quality_UserData`
--

DROP TABLE IF EXISTS `quality_UserData`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `quality_UserData` (
  `UserName` varchar(255) NOT NULL,
  `Role` enum('Weight','INS','QA') NOT NULL,
  `isSectionManager` tinyint(1) NOT NULL DEFAULT 0,
  `DateCreated` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`UserName`),
  CONSTRAINT `quality_UserData_ibfk_1` FOREIGN KEY (`UserName`) REFERENCES `master_users` (`username`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `run_inspections`
--

DROP TABLE IF EXISTS `run_inspections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `run_inspections` (
  `RunID` int(11) NOT NULL,
  `Weight` double NOT NULL,
  `Pressure1` double NOT NULL,
  `Pressure2` double NOT NULL,
  `Brix` double DEFAULT NULL,
  `Note` varchar(255) NOT NULL,
  `isPreInspection` tinyint(1) NOT NULL,
  `isPhotographed` tinyint(1) NOT NULL,
  `DateAdded` datetime NOT NULL DEFAULT current_timestamp(),
  KEY `RunID` (`RunID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping routines for database 'operationsData'
--

--
-- Current Database: `growerReporting`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `growerReporting` /*!40100 DEFAULT CHARACTER SET latin1 */;

USE `growerReporting`;

--
-- Table structure for table `BULKRTCSV`
--

DROP TABLE IF EXISTS `BULKRTCSV`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `BULKRTCSV` (
  `RT#` varchar(255) NOT NULL,
  `Sort Code` varchar(255) NOT NULL,
  `Crop Year` varchar(255) NOT NULL,
  `Grower` varchar(255) NOT NULL,
  `GrowerName` varchar(255) NOT NULL,
  `Class` varchar(255) NOT NULL,
  `ClassDesc` varchar(255) NOT NULL,
  `Commodity` varchar(255) NOT NULL,
  `Comm Desc` varchar(255) NOT NULL,
  `Variety` varchar(255) NOT NULL,
  `VarDesc` varchar(255) NOT NULL,
  `Strain` varchar(255) NOT NULL,
  `StrDesc` varchar(255) NOT NULL,
  `Farm` varchar(255) NOT NULL,
  `FarmDesc` varchar(255) NOT NULL,
  `Block` varchar(255) NOT NULL,
  `BlockDesc` varchar(255) NOT NULL,
  `Lot` varchar(255) NOT NULL,
  `Date` date NOT NULL,
  `Pack` varchar(255) NOT NULL,
  `Size` varchar(255) NOT NULL,
  `Qty` varchar(255) NOT NULL,
  `Bu` varchar(255) NOT NULL,
  `ItemNum` varchar(255) NOT NULL,
  PRIMARY KEY (`RT#`,`Pack`),
  KEY `Crop Year` (`Crop Year`),
  KEY `Grower` (`Grower`),
  KEY `GrowerName` (`GrowerName`),
  KEY `VarDesc` (`VarDesc`),
  KEY `StrDesc` (`StrDesc`),
  KEY `FarmDesc` (`FarmDesc`),
  KEY `BlockDesc` (`BlockDesc`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Block_QA`
--

DROP TABLE IF EXISTS `Block_QA`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Block_QA` (
  `Grower` varchar(4) DEFAULT NULL,
  `Commodity` varchar(15) DEFAULT NULL,
  `Farm` varchar(35) DEFAULT NULL,
  `Block` varchar(35) DEFAULT NULL,
  `Variety` varchar(35) DEFAULT NULL,
  `Strain` varchar(35) DEFAULT NULL,
  `Pressure1` decimal(5,3) DEFAULT NULL,
  `Pressure2` decimal(4,2) DEFAULT NULL,
  `Brix` decimal(4,2) DEFAULT NULL,
  `DA` varchar(3) DEFAULT NULL,
  `DA2` varchar(3) DEFAULT NULL,
  `Count` int(3) DEFAULT NULL,
  `Weight` decimal(3,2) DEFAULT NULL,
  `Starch` varchar(3) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary table structure for view `CurYearReceived`
--

DROP TABLE IF EXISTS `CurYearReceived`;
/*!50001 DROP VIEW IF EXISTS `CurYearReceived`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `CurYearReceived` (
  `RT#` tinyint NOT NULL,
  `Sort Code` tinyint NOT NULL,
  `Crop Year` tinyint NOT NULL,
  `Grower` tinyint NOT NULL,
  `GrowerName` tinyint NOT NULL,
  `Class` tinyint NOT NULL,
  `ClassDesc` tinyint NOT NULL,
  `Commodity` tinyint NOT NULL,
  `Comm Desc` tinyint NOT NULL,
  `Variety` tinyint NOT NULL,
  `VarDesc` tinyint NOT NULL,
  `Strain` tinyint NOT NULL,
  `StrDesc` tinyint NOT NULL,
  `Farm` tinyint NOT NULL,
  `FarmDesc` tinyint NOT NULL,
  `Block` tinyint NOT NULL,
  `BlockDesc` tinyint NOT NULL,
  `Lot` tinyint NOT NULL,
  `Date` tinyint NOT NULL,
  `Pack` tinyint NOT NULL,
  `Size` tinyint NOT NULL,
  `Qty` tinyint NOT NULL,
  `Bu` tinyint NOT NULL,
  `ItemNum` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `EstChngs_DateOfLastManual_byBlock`
--

DROP TABLE IF EXISTS `EstChngs_DateOfLastManual_byBlock`;
/*!50001 DROP VIEW IF EXISTS `EstChngs_DateOfLastManual_byBlock`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `EstChngs_DateOfLastManual_byBlock` (
  `block_PK` tinyint NOT NULL,
  `Last_Date_Change` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `EstChngs_UserUpdated_ThisYear`
--

DROP TABLE IF EXISTS `EstChngs_UserUpdated_ThisYear`;
/*!50001 DROP VIEW IF EXISTS `EstChngs_UserUpdated_ThisYear`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `EstChngs_UserUpdated_ThisYear` (
  `block_PK` tinyint NOT NULL,
  `Comm Desc` tinyint NOT NULL,
  `VarDesc` tinyint NOT NULL,
  `Str Desc` tinyint NOT NULL,
  `Grower` tinyint NOT NULL,
  `FarmDesc` tinyint NOT NULL,
  `BlockDesc` tinyint NOT NULL,
  `cropYear` tinyint NOT NULL,
  `date_Changed` tinyint NOT NULL,
  `changed_by` tinyint NOT NULL,
  `new_bushel_value` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `Estimates_AllBlocks_ThisYear`
--

DROP TABLE IF EXISTS `Estimates_AllBlocks_ThisYear`;
/*!50001 DROP VIEW IF EXISTS `Estimates_AllBlocks_ThisYear`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `Estimates_AllBlocks_ThisYear` (
  `block_PK` tinyint NOT NULL,
  `Comm Desc` tinyint NOT NULL,
  `VarDesc` tinyint NOT NULL,
  `Str Desc` tinyint NOT NULL,
  `Grower` tinyint NOT NULL,
  `FarmDesc` tinyint NOT NULL,
  `BlockDesc` tinyint NOT NULL,
  `2016Act` tinyint NOT NULL,
  `2017Est` tinyint NOT NULL,
  `changed_by` tinyint NOT NULL,
  `date_Changed` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `GrowerData`
--

DROP TABLE IF EXISTS `GrowerData`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `GrowerData` (
  `GrowerCode` varchar(10) NOT NULL,
  `GrowerName` varchar(255) NOT NULL,
  `Password` varchar(255) DEFAULT NULL,
  `isAdmin` tinyint(1) NOT NULL,
  `isMultiAccountUser` tinyint(1) NOT NULL,
  PRIMARY KEY (`GrowerCode`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `GrowerGroups`
--

DROP TABLE IF EXISTS `GrowerGroups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `GrowerGroups` (
  `PK` int(11) NOT NULL AUTO_INCREMENT,
  `GroupID` int(11) NOT NULL,
  `GrowerCode` varchar(2) NOT NULL,
  PRIMARY KEY (`PK`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Preharvest_Samples`
--

DROP TABLE IF EXISTS `Preharvest_Samples`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Preharvest_Samples` (
  `Grower` varchar(4) NOT NULL,
  `PK` int(11) NOT NULL,
  `Retain` tinyint(1) NOT NULL,
  `SampleNum` tinyint(4) NOT NULL,
  `Pressure1` varchar(6) NOT NULL,
  `Pressure2` varchar(6) NOT NULL,
  `Brix` varchar(4) DEFAULT NULL,
  `Weight` varchar(5) NOT NULL,
  `Starch` tinyint(4) DEFAULT NULL,
  `DA` varchar(6) NOT NULL,
  `DA2` varchar(6) NOT NULL,
  `Date` timestamp NOT NULL DEFAULT current_timestamp(),
  `Inspector` varchar(100) NOT NULL,
  `Notes` varchar(255) NOT NULL,
  `isStarchInspected` tinyint(1) NOT NULL,
  `NumSamples` tinyint(11) NOT NULL,
  `DAAverage` varchar(255) GENERATED ALWAYS AS (round((`DA` + `DA2`) / 2,2)) VIRTUAL,
  PRIMARY KEY (`PK`,`SampleNum`,`Date`),
  KEY `Grower` (`Grower`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary table structure for view `ReceivedandEstimates`
--

DROP TABLE IF EXISTS `ReceivedandEstimates`;
/*!50001 DROP VIEW IF EXISTS `ReceivedandEstimates`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `ReceivedandEstimates` (
  `BlockID` tinyint NOT NULL,
  `Code` tinyint NOT NULL,
  `Commodity` tinyint NOT NULL,
  `Farm` tinyint NOT NULL,
  `FarmCode` tinyint NOT NULL,
  `Block` tinyint NOT NULL,
  `BlockCode` tinyint NOT NULL,
  `Variety` tinyint NOT NULL,
  `Strain` tinyint NOT NULL,
  `2014 Received` tinyint NOT NULL,
  `2015 Received` tinyint NOT NULL,
  `2016 Received` tinyint NOT NULL,
  `2016 Estimate` tinyint NOT NULL,
  `2017 Received` tinyint NOT NULL,
  `isDeletedBlock` tinyint NOT NULL,
  `isDonePicking` tinyint NOT NULL,
  `isUserConfirmedEstimate` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `changes_this_year`
--

DROP TABLE IF EXISTS `changes_this_year`;
/*!50001 DROP VIEW IF EXISTS `changes_this_year`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `changes_this_year` (
  `block_PK` tinyint NOT NULL,
  `Comm Desc` tinyint NOT NULL,
  `VarDesc` tinyint NOT NULL,
  `FarmDesc` tinyint NOT NULL,
  `BlockDesc` tinyint NOT NULL,
  `Str Desc` tinyint NOT NULL,
  `date_Changed` tinyint NOT NULL,
  `cropYear` tinyint NOT NULL,
  `belongs_to_Grower` tinyint NOT NULL,
  `changed_by` tinyint NOT NULL,
  `new_bushel_value` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `crop-estimates`
--

DROP TABLE IF EXISTS `crop-estimates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `crop-estimates` (
  `PK` int(11) NOT NULL AUTO_INCREMENT,
  `Grower` varchar(255) NOT NULL,
  `Comm Desc` varchar(255) NOT NULL,
  `VarDesc` varchar(255) NOT NULL,
  `FarmDesc` varchar(255) NOT NULL,
  `BlockDesc` varchar(255) NOT NULL,
  `Str Desc` varchar(255) NOT NULL,
  `isDeleted` tinyint(1) NOT NULL,
  `isSameAsLastYear` tinyint(1) NOT NULL,
  `isFinished` tinyint(1) NOT NULL,
  `2010act` int(11) NOT NULL,
  `2011act` int(11) NOT NULL,
  `2012act` int(11) NOT NULL,
  `2013act` int(11) NOT NULL,
  `2014est` int(11) NOT NULL,
  `2014act` int(11) NOT NULL,
  `2015est` int(11) NOT NULL,
  `2015act` int(11) NOT NULL,
  `2016est` int(11) NOT NULL,
  `2016act` int(11) NOT NULL,
  `2017est` int(11) NOT NULL,
  `2016hail` int(11) NOT NULL,
  PRIMARY KEY (`PK`),
  KEY `Grower` (`Grower`)
) ENGINE=InnoDB AUTO_INCREMENT=3744 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `crop_estimates_changes_timeseries`
--

DROP TABLE IF EXISTS `crop_estimates_changes_timeseries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `crop_estimates_changes_timeseries` (
  `block_PK` int(11) NOT NULL,
  `date_Changed` datetime NOT NULL DEFAULT current_timestamp(),
  `cropYear` smallint(6) NOT NULL,
  `belongs_to_Grower` varchar(50) NOT NULL,
  `changed_by` varchar(50) NOT NULL,
  `new_bushel_value` int(11) NOT NULL,
  KEY `block_PK` (`block_PK`),
  KEY `cropYear` (`cropYear`),
  KEY `belongs_to_Grower` (`belongs_to_Grower`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `growerCalendar`
--

DROP TABLE IF EXISTS `growerCalendar`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `growerCalendar` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Grower` varchar(255) NOT NULL,
  `Variety` varchar(255) NOT NULL,
  `Strain` varchar(255) NOT NULL,
  `Start` date NOT NULL,
  `EndDate` date DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `Grower` (`Grower`)
) ENGINE=InnoDB AUTO_INCREMENT=144 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping routines for database 'growerReporting'
--

--
-- Current Database: `operationsData`
--

USE `operationsData`;

--
-- Final view structure for view `AvgWeightByRT`
--

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
/*!50001 VIEW `AvgWeightByRT` AS select `InspectedRTs`.`RTNum` AS `RTNum`,case when `AggregateWeightSamples`.`Weight` is not null then (sum(`InspectedRTs`.`#Samples`) * avg(`AppleSamples`.`Weight`) + 20 * count(`AggregateWeightSamples`.`RT#`) * avg(`AggregateWeightSamples`.`Weight` / 20)) / (sum(`InspectedRTs`.`#Samples`) + 20 * count(`AggregateWeightSamples`.`RT#`)) else ifnull(avg(`AppleSamples`.`Weight`),0) end AS `WeightAvg` from ((`InspectedRTs` left join `AppleSamples` on(`InspectedRTs`.`RTNum` = `AppleSamples`.`RT#`)) left join `AggregateWeightSamples` on(`InspectedRTs`.`RTNum` = `AggregateWeightSamples`.`RT#`)) group by `InspectedRTs`.`RTNum` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `Block_Receiving`
--

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
/*!50001 VIEW `Block_Receiving` AS select trim(`BULKOHCSV`.`Grower`) AS `Grower`,trim(`BULKOHCSV`.`CommDesc`) AS `CommDesc`,trim(`BULKOHCSV`.`FarmDesc`) AS `Farm`,trim(`BULKOHCSV`.`BlockDesc`) AS `Block`,trim(`BULKOHCSV`.`VarDesc`) AS `VarDesc`,trim(`BULKOHCSV`.`StrDesc`) AS `Strain`,round(avg(`AppleSamples`.`Pressure1`),3) AS `Pressure1`,round(avg(`AppleSamples`.`Pressure2`),2) AS `Pressure2`,round(avg(`AppleSamples`.`Brix`),2) AS `Brix`,round(avg(`AppleSamples`.`DA`),2) AS `DA`,round(avg(`AppleSamples`.`DA2`),2) AS `DA2`,count(0) AS `Count`,round(avg(`AppleSamples`.`Weight`),2) AS `Weight`,round(avg(`AppleSamples`.`Starch`),2) AS `Starch` from (`AppleSamples` join `BULKOHCSV` on(`AppleSamples`.`RT#` = `BULKOHCSV`.`RT#`)) where year(`AppleSamples`.`FinalInspectionDate`) = year(curdate()) group by `BULKOHCSV`.`Grower`,`BULKOHCSV`.`FarmDesc`,`BULKOHCSV`.`BlockDesc`,`BULKOHCSV`.`VarDesc`,`BULKOHCSV`.`StrDesc` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `RTsWQuality`
--

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
/*!50001 VIEW `RTsWQuality` AS select `BULKOHCSV`.`RT#` AS `RT#`,`BULKOHCSV`.`SortCode` AS `SortCode`,`BULKOHCSV`.`CropYear` AS `Crop Year`,`BULKOHCSV`.`Grower` AS `Grower`,`BULKOHCSV`.`GrowerName` AS `Grower Name`,`BULKOHCSV`.`Class` AS `Class`,`BULKOHCSV`.`ClassDesc` AS `Class Desc`,`BULKOHCSV`.`Commodity` AS `Commodity`,`BULKOHCSV`.`CommDesc` AS `CommDesc`,`BULKOHCSV`.`Variety` AS `Variety`,`BULKOHCSV`.`VarDesc` AS `Var Desc`,`BULKOHCSV`.`Strain` AS `Strain`,`BULKOHCSV`.`StrDesc` AS `Str Desc`,`BULKOHCSV`.`Farm` AS `Farm`,case when `BULKOHCSV`.`FarmDesc` = '' then '[Farm Name]' else `BULKOHCSV`.`FarmDesc` end AS `Farm Desc`,`BULKOHCSV`.`Block` AS `Block`,case when `BULKOHCSV`.`BlockDesc` = '' then '[Block Name]' else `BULKOHCSV`.`BlockDesc` end AS `Block Desc`,`BULKOHCSV`.`Lot` AS `Lot`,`BULKOHCSV`.`Date` AS `Date`,`BULKOHCSV`.`Size` AS `Size`,`BULKOHCSV`.`Pack` AS `Pack`,`BULKOHCSV`.`QtyOnHand` AS `QtyOnHand`,`BULKOHCSV`.`BuOnHand` AS `BuOnHand`,`BULKOHCSV`.`RoomNum` AS `Location`,`BULKOHCSV`.`CoNum` AS `Co#`,`BULKOHCSV`.`Company Name` AS `Company Name`,case when `InspectedRTs`.`Color Quality` is null then 'FALSE' else 'TRUE' end AS `isQA`,case when `AppleSamples`.`PrAvg` is null then '' else round(avg(`AppleSamples`.`PrAvg`),3) end AS `PressureAvg`,case when `AppleSamples`.`DAAvg` is null then '' else round(avg(`AppleSamples`.`DAAvg`),2) end AS `DAAvg`,ifnull(round(avg(`AppleSamples`.`Brix`),2),'') AS `Brix`,ifnull(round(avg(`AppleSamples`.`Starch`),1),'') AS `Starch`,ifnull(concat(`InspectedRTs`.`Color Quality`,convert(case when `InspectedRTs`.`Blush` <> 0 then ' With Blush' else '' end using latin1)),'') AS `Color`,ifnull(`InspectedRTs`.`Bruise`,'') AS `Bruise`,case when `InspectedRTs`.`BitterPit` is null then '' else case when `InspectedRTs`.`BitterPit` <> 0 then 'Present' else 'Not Present' end end AS `BitterPit`,ifnull(`InspectedRTs`.`Russet`,'') AS `Russet`,ifnull(`InspectedRTs`.`SunBurn`,'') AS `Sunburn`,ifnull(`InspectedRTs`.`SanJoseScale`,'') AS `San Jose Scale`,ifnull(`InspectedRTs`.`Scab`,'') AS `Scab`,ifnull(`InspectedRTs`.`StinkBug`,'') AS `StinkBug`,ifnull(round(`AvgWeightByRT`.`WeightAvg`,2),'') AS `AverageWeight`,case when `AvgWeightByRT`.`WeightAvg` is null then '' else case when `AvgWeightByRT`.`WeightAvg` * 16 >= 13 then 48 when (`AvgWeightByRT`.`WeightAvg` * 16 < 13 and `AvgWeightByRT`.`WeightAvg` >= 11.15) then 56 when (`AvgWeightByRT`.`WeightAvg` * 16 < 11.15 and `AvgWeightByRT`.`WeightAvg` * 16 >= 9.9) then 64 when (`AvgWeightByRT`.`WeightAvg` * 16 < 9.9 and `AvgWeightByRT`.`WeightAvg` * 16 >= 8.85) then 72 when (`AvgWeightByRT`.`WeightAvg` * 16 < 8.85 and `AvgWeightByRT`.`WeightAvg` * 16 >= 8) then 80 when (`AvgWeightByRT`.`WeightAvg` * 16 < 8 and `AvgWeightByRT`.`WeightAvg` * 16 >= 7.15) then 88 when (`AvgWeightByRT`.`WeightAvg` * 16 < 7.15 and `AvgWeightByRT`.`WeightAvg` * 16 >= 6.3) then 100 when (`AvgWeightByRT`.`WeightAvg` * 16 < 6.3 and `AvgWeightByRT`.`WeightAvg` * 16 >= 5.65) then 113 when (`AvgWeightByRT`.`WeightAvg` * 16 < 5.65 and `AvgWeightByRT`.`WeightAvg` * 16 >= 5.1) then 125 when (`AvgWeightByRT`.`WeightAvg` * 16 < 5.1 and `AvgWeightByRT`.`WeightAvg` * 16 >= 4.65) then 138 when (`AvgWeightByRT`.`WeightAvg` * 16 < 4.65 and `AvgWeightByRT`.`WeightAvg` * 16 >= 4.3) then 150 when (`AvgWeightByRT`.`WeightAvg` * 16 < 4.3 and `AvgWeightByRT`.`WeightAvg` * 16 >= 3.95) then 163 when (`AvgWeightByRT`.`WeightAvg` * 16 < 3.95 and `AvgWeightByRT`.`WeightAvg` * 16 >= 3.6) then 175 when (`AvgWeightByRT`.`WeightAvg` * 16 < 3.6 and `AvgWeightByRT`.`WeightAvg` * 16 >= 3.25) then 198 else 216 end end AS `SizefromAverage`,ifnull(`InspectedRTs`.`Note`,'') AS `Notes`,case when `InspectedRTs`.`InspectedBy` is null then '' else concat('Field Inspector: ',`InspectedRTs`.`InspectedBy`,'-- Final Inspector: ',ifnull(`AppleSamples`.`FinalTestedBy`,'Not Final Inspected')) end AS `InspectedBy`,ifnull(`AppleSamples`.`FinalInspectionDate`,ifnull(`InspectedRTs`.`DateInspected`,'')) AS `DateTested` from (((`BULKOHCSV` left join `InspectedRTs` on(`BULKOHCSV`.`RT#` = `InspectedRTs`.`RTNum`)) left join `AppleSamples` on(`InspectedRTs`.`RTNum` = `AppleSamples`.`RT#`)) left join `AvgWeightByRT` on(`AvgWeightByRT`.`RTNum` = `BULKOHCSV`.`RT#`)) group by `BULKOHCSV`.`RT#` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Current Database: `growerReporting`
--

USE `growerReporting`;

--
-- Final view structure for view `CurYearReceived`
--

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
/*!50001 VIEW `CurYearReceived` AS (select `BULKRTCSV`.`RT#` AS `RT#`,`BULKRTCSV`.`Sort Code` AS `Sort Code`,`BULKRTCSV`.`Crop Year` AS `Crop Year`,`BULKRTCSV`.`Grower` AS `Grower`,`BULKRTCSV`.`GrowerName` AS `GrowerName`,`BULKRTCSV`.`Class` AS `Class`,`BULKRTCSV`.`ClassDesc` AS `ClassDesc`,`BULKRTCSV`.`Commodity` AS `Commodity`,`BULKRTCSV`.`Comm Desc` AS `Comm Desc`,`BULKRTCSV`.`Variety` AS `Variety`,`BULKRTCSV`.`VarDesc` AS `VarDesc`,`BULKRTCSV`.`Strain` AS `Strain`,`BULKRTCSV`.`StrDesc` AS `StrDesc`,`BULKRTCSV`.`Farm` AS `Farm`,`BULKRTCSV`.`FarmDesc` AS `FarmDesc`,`BULKRTCSV`.`Block` AS `Block`,`BULKRTCSV`.`BlockDesc` AS `BlockDesc`,`BULKRTCSV`.`Lot` AS `Lot`,`BULKRTCSV`.`Date` AS `Date`,`BULKRTCSV`.`Pack` AS `Pack`,`BULKRTCSV`.`Size` AS `Size`,`BULKRTCSV`.`Qty` AS `Qty`,`BULKRTCSV`.`Bu` AS `Bu`,`BULKRTCSV`.`ItemNum` AS `ItemNum` from `BULKRTCSV` where `BULKRTCSV`.`Crop Year` = convert(substr(year(curdate()),4,1) using latin1)) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `EstChngs_DateOfLastManual_byBlock`
--

/*!50001 DROP TABLE IF EXISTS `EstChngs_DateOfLastManual_byBlock`*/;
/*!50001 DROP VIEW IF EXISTS `EstChngs_DateOfLastManual_byBlock`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `EstChngs_DateOfLastManual_byBlock` AS select `A1`.`block_PK` AS `block_PK`,max(`A1`.`date_Changed`) AS `Last_Date_Change` from `changes_this_year` `A1` where `A1`.`changed_by` <> 'System' group by `A1`.`block_PK` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `EstChngs_UserUpdated_ThisYear`
--

/*!50001 DROP TABLE IF EXISTS `EstChngs_UserUpdated_ThisYear`*/;
/*!50001 DROP VIEW IF EXISTS `EstChngs_UserUpdated_ThisYear`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `EstChngs_UserUpdated_ThisYear` AS select `A`.`block_PK` AS `block_PK`,`A`.`Comm Desc` AS `Comm Desc`,`A`.`VarDesc` AS `VarDesc`,`A`.`Str Desc` AS `Str Desc`,`A`.`belongs_to_Grower` AS `Grower`,`A`.`FarmDesc` AS `FarmDesc`,`A`.`BlockDesc` AS `BlockDesc`,`A`.`cropYear` AS `cropYear`,`A`.`date_Changed` AS `date_Changed`,`A`.`changed_by` AS `changed_by`,`A`.`new_bushel_value` AS `new_bushel_value` from (`changes_this_year` `A` join `EstChngs_DateOfLastManual_byBlock` `B` on(`A`.`block_PK` = `B`.`block_PK` and `A`.`date_Changed` = `B`.`Last_Date_Change`)) union select `A`.`PK` AS `block_PK`,`A`.`Comm Desc` AS `Comm Desc`,`A`.`VarDesc` AS `VarDesc`,`A`.`Str Desc` AS `Str Desc`,`A`.`Grower` AS `Grower`,`A`.`FarmDesc` AS `FarmDesc`,`A`.`BlockDesc` AS `BlockDesc`,2017 AS `cropYear`,NULL AS `date_Changed`,NULL AS `changed_by`,`A`.`2017est` AS `new_bushel_value` from `crop-estimates` `A` where `A`.`isDeleted` = 0 and `A`.`isSameAsLastYear` = 1 */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `Estimates_AllBlocks_ThisYear`
--

/*!50001 DROP TABLE IF EXISTS `Estimates_AllBlocks_ThisYear`*/;
/*!50001 DROP VIEW IF EXISTS `Estimates_AllBlocks_ThisYear`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `Estimates_AllBlocks_ThisYear` AS select `A`.`PK` AS `block_PK`,`A`.`Comm Desc` AS `Comm Desc`,`A`.`VarDesc` AS `VarDesc`,`A`.`Str Desc` AS `Str Desc`,`A`.`Grower` AS `Grower`,`A`.`FarmDesc` AS `FarmDesc`,`A`.`BlockDesc` AS `BlockDesc`,`A`.`2016act` AS `2016Act`,`B`.`new_bushel_value` AS `2017Est`,`B`.`changed_by` AS `changed_by`,`B`.`date_Changed` AS `date_Changed` from (`crop-estimates` `A` left join `EstChngs_UserUpdated_ThisYear` `B` on(`A`.`PK` = `B`.`block_PK`)) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `ReceivedandEstimates`
--

/*!50001 DROP TABLE IF EXISTS `ReceivedandEstimates`*/;
/*!50001 DROP VIEW IF EXISTS `ReceivedandEstimates`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = latin1 */;
/*!50001 SET character_set_results     = latin1 */;
/*!50001 SET collation_connection      = latin1_swedish_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`ricefruit`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `ReceivedandEstimates` AS (select rtrim(`crop-estimates`.`PK`) AS `BlockID`,rtrim(`crop-estimates`.`Grower`) AS `Code`,rtrim(`crop-estimates`.`Comm Desc`) AS `Commodity`,rtrim(`crop-estimates`.`FarmDesc`) AS `Farm`,ifnull(rtrim(`CurYearReceived`.`Farm`),'') AS `FarmCode`,rtrim(`crop-estimates`.`BlockDesc`) AS `Block`,ifnull(rtrim(`CurYearReceived`.`Block`),'') AS `BlockCode`,rtrim(`crop-estimates`.`VarDesc`) AS `Variety`,rtrim(`crop-estimates`.`Str Desc`) AS `Strain`,rtrim(`crop-estimates`.`2014act`) AS `2014 Received`,rtrim(`crop-estimates`.`2015act`) AS `2015 Received`,rtrim(`crop-estimates`.`2016act`) AS `2016 Received`,rtrim(case when `crop-estimates`.`isDeleted` = 0 then `crop-estimates`.`2016est` else 0 end) AS `2016 Estimate`,ifnull(sum(`CurYearReceived`.`Bu`),'0') AS `2017 Received`,case when `crop-estimates`.`isDeleted` = 0 then 'false' else 'true' end AS `isDeletedBlock`,case when `crop-estimates`.`isFinished` = 0 then 'false' else 'true' end AS `isDonePicking`,case when (`crop-estimates`.`2017est` <> `crop-estimates`.`2016act` or `crop-estimates`.`isSameAsLastYear` = 1) then 'true' else 'false' end AS `isUserConfirmedEstimate` from (`crop-estimates` left join `CurYearReceived` on(rtrim(`CurYearReceived`.`Comm Desc`) = rtrim(`crop-estimates`.`Comm Desc`) and rtrim(`CurYearReceived`.`VarDesc` = rtrim(`crop-estimates`.`VarDesc`)) and rtrim(`CurYearReceived`.`StrDesc` = rtrim(`crop-estimates`.`Str Desc`)) and rtrim(`CurYearReceived`.`BlockDesc` = rtrim(`crop-estimates`.`BlockDesc`)) and rtrim(`CurYearReceived`.`FarmDesc` = rtrim(`crop-estimates`.`FarmDesc`)) and rtrim(`CurYearReceived`.`Grower` = rtrim(`crop-estimates`.`Grower`)))) group by `crop-estimates`.`PK`) union (select 'Unmatched Block' AS `BlockID`,rtrim(`BULKRTCSV`.`Grower`) AS `Code`,rtrim(`BULKRTCSV`.`Comm Desc`) AS `Commodity`,rtrim(`BULKRTCSV`.`FarmDesc`) AS `Farm`,rtrim(`BULKRTCSV`.`Farm`) AS `FarmCode`,rtrim(`BULKRTCSV`.`BlockDesc`) AS `Block`,rtrim(`BULKRTCSV`.`Block`) AS `BlockCode`,rtrim(`BULKRTCSV`.`VarDesc`) AS `Variety`,rtrim(`BULKRTCSV`.`StrDesc`) AS `Strain`,'0' AS `2014 Received`,'0' AS `2015 Received`,'0' AS `2016 Received`,'0' AS `2017 Estimate`,sum(`BULKRTCSV`.`Bu`) AS `2017 Received`,'false' AS `isDeletedBlock`,'false' AS `isDonePicking`,'false' AS `isUserConfirmedEstimate` from (`BULKRTCSV` left join `crop-estimates` on(rtrim(`BULKRTCSV`.`Comm Desc`) = rtrim(`crop-estimates`.`Comm Desc`) and rtrim(`BULKRTCSV`.`VarDesc` = rtrim(`crop-estimates`.`VarDesc`)) and rtrim(`BULKRTCSV`.`StrDesc` = rtrim(`crop-estimates`.`Str Desc`)) and rtrim(`BULKRTCSV`.`BlockDesc` = rtrim(`crop-estimates`.`BlockDesc`)) and rtrim(`BULKRTCSV`.`FarmDesc` = rtrim(`crop-estimates`.`FarmDesc`)) and rtrim(`BULKRTCSV`.`Grower` = rtrim(`crop-estimates`.`Grower`)))) where `crop-estimates`.`PK` is null and `BULKRTCSV`.`Crop Year` = convert(substr(year(curdate()),4,1) using latin1) group by `BULKRTCSV`.`Grower`,`BULKRTCSV`.`Comm Desc`,`BULKRTCSV`.`FarmDesc`,`BULKRTCSV`.`BlockDesc`,`BULKRTCSV`.`VarDesc`,`BULKRTCSV`.`StrDesc`) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `changes_this_year`
--

/*!50001 DROP TABLE IF EXISTS `changes_this_year`*/;
/*!50001 DROP VIEW IF EXISTS `changes_this_year`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `changes_this_year` AS select `crop_estimates_changes_timeseries`.`block_PK` AS `block_PK`,`crop-estimates`.`Comm Desc` AS `Comm Desc`,`crop-estimates`.`VarDesc` AS `VarDesc`,`crop-estimates`.`FarmDesc` AS `FarmDesc`,`crop-estimates`.`BlockDesc` AS `BlockDesc`,`crop-estimates`.`Str Desc` AS `Str Desc`,`crop_estimates_changes_timeseries`.`date_Changed` AS `date_Changed`,`crop_estimates_changes_timeseries`.`cropYear` AS `cropYear`,`crop_estimates_changes_timeseries`.`belongs_to_Grower` AS `belongs_to_Grower`,`crop_estimates_changes_timeseries`.`changed_by` AS `changed_by`,`crop_estimates_changes_timeseries`.`new_bushel_value` AS `new_bushel_value` from (`crop_estimates_changes_timeseries` join `crop-estimates` on(`crop_estimates_changes_timeseries`.`block_PK` = `crop-estimates`.`PK`)) where year(`crop_estimates_changes_timeseries`.`date_Changed`) = year(current_timestamp()) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-06-05 10:01:51
