-- phpMyAdmin SQL Dump
-- version 4.7.3
-- https://www.phpmyadmin.net/
--
-- Host: database-server
-- Generation Time: Aug 10, 2017 at 01:19 PM
-- Server version: 10.2.7-MariaDB-10.2.7+maria~jessie
-- PHP Version: 7.0.21
USE `operationsData`;

# UPDATE packapps_system_info SET systemInstalled = 1;
TRUNCATE quality_AlertEmails;
INSERT INTO quality_AlertEmails(FullName, EmailAddress) VALUES ('Michael Clark', 'michael@packercloud.com');

# Skip grower onboarding for RL
UPDATE grower_GrowerLogins SET email_confirmed = 1, confirm_email_sent = 1 WHERE GrowerCode = 'RL';

UPDATE packapps_master_users SET allowedStorage = 1 WHERE username = 'mike';

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `operationsData`
--

--
-- Dumping data for table `storage_buildings`
--

INSERT INTO `storage_buildings` (`building_id`, `building_name`) VALUES
(1, 'North Building'),
(2, 'South Building');

--
-- Dumping data for table `storage_rooms`
--

INSERT INTO `storage_rooms` (`building`, `room_id`, `isDisabled`, `isAvailable`, `lastAvailabilityChange`, `room_name`) VALUES
  (1, 1, 0, 1, '2017-05-08 00:00:00', 'Room 1'),
  (1, 2, 0, 1, '2017-02-14 00:00:00', 'Room 2'),
  (1, 4, 0, 1, '2016-02-24 00:00:00', 'Room 3'),
  (1, 5, 0, 1, '2016-12-06 00:00:00', 'Room 4'),
  (1, 6, 0, 1, '2017-07-12 00:00:00', 'Room 5'),
  (1, 7, 0, 1, '2017-07-21 00:00:00', 'Room 6'),
  (2, 8, 0, 0, '2015-12-15 00:00:00', 'Big Room'),
  (2, 9, 0, 0, '2016-09-13 00:00:00', 'Small Room'),
  (2, 10, 0, 1, '2017-05-09 00:00:00', 'Just Right Room');

--
-- Dumping data for table `storage_grower_receipts`
--

INSERT INTO `storage_grower_receipts` (`id`, `grower_block`, `external_reference_num`, `date`, `receivedBy`) VALUES
  (1, 1001, 1234, '2017-07-27 00:00:00', 'mike'),
  (2, 1893, 5678, '2017-07-27 00:00:00', 'mike'),
  (7, 2255, 12341, '2017-08-18 00:00:00', 'JMH'),
  (8, 3591, 5, '2017-08-18 00:00:00', 'barb'),
  (10, 1057, 12341234, '2017-08-04 00:00:00', 'alejandro');



--
-- Dumping data for table `storage_grower_fruit_bins`
--

INSERT INTO `storage_grower_fruit_bins` (`grower_receipt_id`, `isFinished`, `curRoom`, `bushelsInBin`) VALUES
(1, 0, 1, 23),
(2, 0, 1, 23),
(7, 0, 8, 23),
(8, 0, 8, 23),
(10, 0, 1, 23),
(1, 0, 1, 23),
(2, 0, 1, 23);

COMMIT;


/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
