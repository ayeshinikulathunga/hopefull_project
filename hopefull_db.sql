-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3308
-- Generation Time: Apr 21, 2025 at 09:49 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `hopefull_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `achievements`
--

CREATE TABLE `achievements` (
  `AchievementID` varchar(10) NOT NULL CHECK (`AchievementID` like 'ACH%'),
  `AchievementName` varchar(50) NOT NULL,
  `Description` text NOT NULL,
  `RequiredPoints` int(11) NOT NULL,
  `Category` enum('Donation','Volunteer','Community') NOT NULL,
  `CreatedDate` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `auth_moderators`
--

CREATE TABLE `auth_moderators` (
  `ModeratorID` varchar(10) NOT NULL CHECK (`ModeratorID` like 'AM%'),
  `UserID` varchar(10) NOT NULL,
  `FirstName` varchar(50) NOT NULL,
  `LastName` varchar(50) NOT NULL,
  `ContactNumber` varchar(15) DEFAULT NULL,
  `VerificationCount` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `auth_moderators`
--

INSERT INTO `auth_moderators` (`ModeratorID`, `UserID`, `FirstName`, `LastName`, `ContactNumber`, `VerificationCount`) VALUES
('AM10001', 'U20001', 'Auth', 'Moderator', '0777123456', 0);

-- --------------------------------------------------------

--
-- Table structure for table `badges`
--

CREATE TABLE `badges` (
  `BadgeID` varchar(10) NOT NULL CHECK (`BadgeID` like 'BG%'),
  `BadgeName` varchar(50) NOT NULL,
  `Description` text NOT NULL,
  `Criteria` text NOT NULL,
  `BadgeImage` varchar(255) DEFAULT NULL,
  `CreatedDate` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `deliveries`
--

CREATE TABLE `deliveries` (
  `DeliveryID` varchar(10) NOT NULL CHECK (`DeliveryID` like 'DEL%'),
  `OrderID` varchar(10) DEFAULT NULL,
  `NonMonetaryDonationID` varchar(10) DEFAULT NULL,
  `DeliveryOfficerID` varchar(10) NOT NULL,
  `AssignedBy` varchar(10) NOT NULL,
  `PickupLocation` text NOT NULL,
  `DeliveryLocation` text NOT NULL,
  `ScheduledDate` datetime NOT NULL,
  `Status` enum('Pending','InTransit','Delivered','Cancelled') DEFAULT 'Pending',
  `TrackingStatus` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `delivery_officers`
--

CREATE TABLE `delivery_officers` (
  `DeliveryOfficerID` varchar(10) NOT NULL CHECK (`DeliveryOfficerID` like 'DO%'),
  `UserID` varchar(10) NOT NULL,
  `FirstName` varchar(50) NOT NULL,
  `LastName` varchar(50) NOT NULL,
  `ContactNumber` varchar(15) NOT NULL,
  `VehicleType` varchar(50) DEFAULT NULL,
  `AvailabilityStatus` enum('Available','OnDelivery','Offline') DEFAULT 'Available',
  `CompletedDeliveries` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `donations`
--

CREATE TABLE `donations` (
  `DonationID` varchar(10) NOT NULL CHECK (`DonationID` like 'DON%'),
  `RequestID` varchar(10) NOT NULL,
  `DonorID` varchar(10) NOT NULL,
  `DonationType` enum('Monetary','NonMonetary') NOT NULL,
  `Amount` decimal(10,2) DEFAULT NULL,
  `QuantityDonated` int(11) DEFAULT NULL,
  `IsAnonymous` tinyint(1) DEFAULT 0,
  `Status` enum('Pending','Completed','Cancelled') DEFAULT 'Pending',
  `DonationDate` datetime DEFAULT current_timestamp(),
  `CancellationReason` text DEFAULT NULL,
  `CancellationDate` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `donations`
--

INSERT INTO `donations` (`DonationID`, `RequestID`, `DonorID`, `DonationType`, `Amount`, `QuantityDonated`, `IsAnonymous`, `Status`, `DonationDate`, `CancellationReason`, `CancellationDate`) VALUES
('DON09870', 'REQ00002', 'D92007', 'NonMonetary', NULL, 50, 1, 'Cancelled', '2025-04-19 11:50:32', NULL, NULL),
('DON12518', 'REQ00001', 'D92007', 'Monetary', 1000.00, NULL, 0, 'Completed', '2025-04-18 05:43:38', NULL, NULL),
('DON14034', 'REQ00001', 'D92007', 'Monetary', 10000.00, NULL, 0, 'Completed', '2025-04-18 10:58:38', NULL, NULL),
('DON15331', 'REQ00001', 'D92007', 'Monetary', 1000.00, NULL, 0, 'Completed', '2025-04-18 05:37:18', NULL, NULL),
('DON21857', 'REQ00002', 'D92007', 'NonMonetary', NULL, 70, 1, 'Completed', '2025-04-18 11:34:52', NULL, NULL),
('DON22045', 'REQ00002', 'D92007', 'NonMonetary', NULL, 50, 0, 'Cancelled', '2025-04-18 23:07:26', NULL, NULL),
('DON46932', 'REQ00001', 'D92007', 'Monetary', 1000.00, NULL, 0, 'Completed', '2025-04-18 05:37:51', NULL, NULL),
('DON52106', 'REQ00001', 'D92007', 'Monetary', 1000.00, NULL, 1, 'Completed', '2025-04-19 11:31:09', NULL, NULL),
('DON65812', 'REQ00002', 'D92007', 'NonMonetary', NULL, 500, 0, 'Cancelled', '2025-04-18 17:29:29', 'Changed mind', '2025-04-18 17:36:42'),
('DON68282', 'REQ00001', 'D92007', 'Monetary', 100000.00, NULL, 1, 'Completed', '2025-04-18 17:46:02', NULL, NULL),
('DON75621', 'REQ00002', 'D92007', 'NonMonetary', NULL, 50, 1, 'Pending', '2025-04-19 16:52:23', NULL, NULL),
('DON82527', 'REQ00002', 'D92007', 'NonMonetary', NULL, 500, 0, 'Completed', '2025-04-18 17:53:38', NULL, NULL),
('DON87951', 'REQ00002', 'D92007', 'NonMonetary', NULL, 500, 1, 'Completed', '2025-04-18 17:39:05', NULL, NULL),
('DON93933', 'REQ00001', 'D92007', 'Monetary', 1000.00, NULL, 1, 'Completed', '2025-04-18 11:05:17', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `donation_cancellations`
--

CREATE TABLE `donation_cancellations` (
  `ID` int(11) NOT NULL,
  `DonationID` varchar(10) NOT NULL,
  `CancellationReason` text NOT NULL,
  `CancellationDate` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `donation_cancellations`
--

INSERT INTO `donation_cancellations` (`ID`, `DonationID`, `CancellationReason`, `CancellationDate`) VALUES
(1, 'DON22045', 'Changed mind', '2025-04-19 11:49:27'),
(2, 'DON09870', 'Changed mind', '2025-04-19 11:50:58');

-- --------------------------------------------------------

--
-- Table structure for table `donation_requests`
--

CREATE TABLE `donation_requests` (
  `RequestID` varchar(10) NOT NULL CHECK (`RequestID` like 'REQ%'),
  `RecipientID` varchar(10) NOT NULL,
  `RequestType` enum('Monetary','NonMonetary') NOT NULL,
  `Category` enum('Healthcare','Education','Community','Sports','MakeAWish') NOT NULL,
  `Title` varchar(100) NOT NULL,
  `Description` text NOT NULL,
  `RequestImage` varchar(255) DEFAULT NULL,
  `ProofDocument` text NOT NULL,
  `Deadline` date NOT NULL,
  `VerificationStatus` enum('Pending','Approved','Rejected') DEFAULT 'Pending',
  `RequestStatus` enum('Pending','InProgress','Completed','Expired') DEFAULT 'Pending',
  `ModeratorID` varchar(10) DEFAULT NULL,
  `CreatedDate` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `donation_requests`
--

INSERT INTO `donation_requests` (`RequestID`, `RecipientID`, `RequestType`, `Category`, `Title`, `Description`, `RequestImage`, `ProofDocument`, `Deadline`, `VerificationStatus`, `RequestStatus`, `ModeratorID`, `CreatedDate`) VALUES
('REQ00001', 'R00001', 'Monetary', 'Healthcare', 'Medical Treatment Fund', 'Helping children with critical medical treatments. We aim to provide necessary medical support for underprivileged children who require urgent medical intervention.', NULL, 'medical_treatment_proof.pdf', '2025-12-31', 'Approved', 'InProgress', NULL, '2025-02-23 23:17:08'),
('REQ00002', 'R00002', 'NonMonetary', 'Education', 'School Supplies Drive', 'Collecting school supplies for underprivileged children. Help us provide essential educational materials to students who cannot afford them.', NULL, 'school_supplies_proof.pdf', '2025-08-31', 'Approved', 'InProgress', NULL, '2025-02-23 23:17:47');

-- --------------------------------------------------------

--
-- Table structure for table `donors`
--

CREATE TABLE `donors` (
  `DonorID` varchar(10) NOT NULL CHECK (`DonorID` like 'D%'),
  `UserID` varchar(10) NOT NULL,
  `FirstName` varchar(50) NOT NULL,
  `LastName` varchar(50) NOT NULL,
  `ContactNumber` varchar(15) DEFAULT NULL,
  `TotalDonations` decimal(10,2) DEFAULT 0.00,
  `DonationCount` int(11) DEFAULT 0,
  `AnonymousPreference` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `donors`
--

INSERT INTO `donors` (`DonorID`, `UserID`, `FirstName`, `LastName`, `ContactNumber`, `TotalDonations`, `DonationCount`, `AnonymousPreference`) VALUES
('D16659', 'U18909', 'Likitha', 'Chathu', '0761415788', 0.00, 0, 0),
('D17589', 'U21965', 'Roove', 'Chunkess', '0777675559', 0.00, 0, 0),
('D74445', 'U59360', 'meena', 'Muthuthanthrige', '0777675559', 0.00, 0, 0),
('D80031', 'U21095', 'Karuna', 'Kumari', '0777654448', 0.00, 0, 0),
('D92007', 'U54132', 'Dulmini', 'Nureka', '0761312397', 115000.00, 15, 0);

-- --------------------------------------------------------

--
-- Table structure for table `feedback_reports`
--

CREATE TABLE `feedback_reports` (
  `FeedbackID` varchar(10) NOT NULL CHECK (`FeedbackID` like 'FB%'),
  `DonationID` varchar(10) NOT NULL,
  `RecipientID` varchar(10) NOT NULL,
  `FeedbackType` enum('General','ImpactReport') NOT NULL,
  `Content` text NOT NULL,
  `Rating` int(1) DEFAULT NULL CHECK (`Rating` between 1 and 5),
  `CreatedDate` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `marketplace_inventory`
--

CREATE TABLE `marketplace_inventory` (
  `ProductID` varchar(10) NOT NULL CHECK (`ProductID` like 'P%'),
  `SellerID` varchar(10) NOT NULL,
  `ProductName` varchar(100) NOT NULL,
  `Description` text DEFAULT NULL,
  `Price` decimal(10,2) NOT NULL,
  `StockQuantity` int(11) NOT NULL DEFAULT 0,
  `Category` varchar(50) NOT NULL,
  `Status` enum('Available','OutOfStock','Discontinued') DEFAULT 'Available',
  `LastUpdated` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `ProductImage` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `marketplace_inventory`
--

INSERT INTO `marketplace_inventory` (`ProductID`, `SellerID`, `ProductName`, `Description`, `Price`, `StockQuantity`, `Category`, `Status`, `LastUpdated`, `ProductImage`) VALUES
('P07991', 'MS10002', 'Straw Coasters', 'Set of six handwoven straw coasters made from natural reed grass. Created by Lakmal, who found his calling in weaving small items after an accident affected his vision. The simple, repetitive patterns allow him to work by touch, creating practical items that help him support his family while working from home.', 750.00, 85, 'Handicrafts', 'Available', '2025-04-20 11:18:14', 'P10338.jpeg'),
('P16300', 'MS10002', 'Scented Soap', 'All-natural handmade soap created with virgin coconut oil and Ceylon cinnamon. Made by Samantha, who began creating soaps as physical therapy for her motor coordination challenges. Each bar represents her commitment to natural ingredients and traditional Sri Lankan wellness practices.', 500.00, 98, 'Home', 'Available', '2025-04-20 10:20:07', 'P94255.jpeg'),
('P18364', 'MS10002', 'Brass Oil Lamp', 'Authentic handcrafted brass oil lamp (pahana) made using traditional techniques. Malini, who has overcome visual impairment, creates these lamps through touch and memory, having learned the craft from her father. Her heightened sense of texture allows her to create intricate details that make each lamp unique.', 5000.00, 10, 'Home', 'Available', '2025-03-23 09:43:59', 'P67112.jpeg'),
('P26876', 'MS10002', 'Beaded Wrist Bracelets', 'Vibrant beaded bracelet made with locally sourced seeds and glass beads. Crafted by Tharini, a young woman with autism who finds peace and focus in the repetitive process of beadwork. Her color combinations are instinctively harmonious, creating wearable pieces that bring joy to both maker and wearer.', 250.00, 96, 'Jewelry', 'Available', '2025-04-21 10:25:41', 'P79766.jpeg'),
('P34885', 'MS10002', 'Dried Flower Art', 'dlkwmsklamkdwmallwA', 400.00, 98, 'Home', 'Available', '2025-04-21 13:17:54', 'P34885.jpeg'),
('P49297', 'MS10002', 'Batik Wall Hanging', 'Beautiful handmade batik wall hanging featuring traditional Sri Lankan motifs including peacocks and lotus flowers. Created by Kumari, an artisan with hearing impairment who has mastered the delicate wax-resist technique over 15 years of dedicated practice. Each piece reflects her artistic vision and precise attention to detail.', 2500.00, 6, 'Art', 'Available', '2025-04-20 10:49:07', 'P94941.jpeg'),
('P60859', 'MS10002', 'Handwoven Cane Basket', 'Expertly handwoven storage basket made from sustainable local cane. Crafted by Ranjith, who despite mobility challenges from polio, has become renowned in his village for his exceptional weaving skills. Each basket represents his commitment to preserving traditional Sri Lankan basketry techniques.', 2800.00, 12, 'Handicrafts', 'Available', '2025-04-21 10:25:41', 'P07741.jpeg'),
('P63909', 'MS10002', 'Handloom Saree', 'Elegantly crafted handloom cotton saree in vibrant colors with traditional Sri Lankan motifs. Woven by Jayanthi, an artisan with limited mobility who has adapted the loom to work with her strengths. Each saree takes her several days to complete, representing her resilience and creativity.', 8900.00, 6, 'Textiles', 'Available', '2025-04-20 11:18:14', 'P51459.jpeg'),
('P72622', 'MS10002', 'Crystal Stone Necklace', 'Elegant necklace featuring Sri Lankan moonstones, handcrafted by Amali, an artisan who turned to jewelry making as therapy following a spinal injury. Her designs combine traditional techniques with contemporary styles, allowing her to work from her home studio while creating beautiful pieces that reflect Sri Lanka&#39;s gemstone heritage.', 5000.00, 6, 'Jewelry', 'Available', '2025-04-20 10:15:06', 'P24699.jpeg'),
('P80064', 'MS10002', 'Greeting Cards', 'Set of five hand-painted greeting cards featuring native Sri Lankan wildlife. Each card is individually painted by Dilini, a young artist with Down syndrome who expresses her love of animals through her detailed artwork. Her joyful approach to painting brings these creatures to life with vibrant colors.', 200.00, 98, 'Art', 'Available', '2025-04-20 10:37:11', 'P69048.jpeg');

-- --------------------------------------------------------

--
-- Table structure for table `monetary_donation_details`
--

CREATE TABLE `monetary_donation_details` (
  `DetailID` varchar(10) NOT NULL CHECK (`DetailID` like 'MD%'),
  `RequestID` varchar(10) NOT NULL,
  `TargetAmount` decimal(10,2) NOT NULL,
  `CurrentAmount` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `monetary_donation_details`
--

INSERT INTO `monetary_donation_details` (`DetailID`, `RequestID`, `TargetAmount`, `CurrentAmount`) VALUES
('MD00001', 'REQ00001', 500000.00, 190000.00);

-- --------------------------------------------------------

--
-- Table structure for table `nonmonetary_donation_details`
--

CREATE TABLE `nonmonetary_donation_details` (
  `DetailID` varchar(10) NOT NULL CHECK (`DetailID` like 'NMD%'),
  `RequestID` varchar(10) NOT NULL,
  `ItemName` varchar(100) NOT NULL,
  `QuantityNeeded` int(11) NOT NULL,
  `QuantityReceived` int(11) DEFAULT 0,
  `Province` enum('Western','Central','Southern','Northern','Eastern','North-Western','North-Central','Uva','Sabaragamuwa') NOT NULL,
  `DropOffLocation` text NOT NULL,
  `DropOffTime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `nonmonetary_donation_details`
--

INSERT INTO `nonmonetary_donation_details` (`DetailID`, `RequestID`, `ItemName`, `QuantityNeeded`, `QuantityReceived`, `Province`, `DropOffLocation`, `DropOffTime`) VALUES
('NMD00001', 'REQ00002', 'Notebooks', 1000, 800, 'Western', 'Colombo Community Center', '2025-08-31 10:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `non_monetary_donation_scheduling`
--

CREATE TABLE `non_monetary_donation_scheduling` (
  `SchedulingID` varchar(10) NOT NULL CHECK (`SchedulingID` like 'NMDS%'),
  `DonationID` varchar(10) NOT NULL,
  `DropOffDate` date NOT NULL,
  `DropOffTime` time NOT NULL,
  `Notes` text DEFAULT NULL,
  `CreatedDate` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `non_monetary_donation_scheduling`
--

INSERT INTO `non_monetary_donation_scheduling` (`SchedulingID`, `DonationID`, `DropOffDate`, `DropOffTime`, `Notes`, `CreatedDate`) VALUES
('NMDS27389', 'DON21857', '2025-04-19', '11:00:00', '', '2025-04-18 11:34:52'),
('NMDS31567', 'DON87951', '2025-04-19', '10:00:00', '', '2025-04-18 17:39:05'),
('NMDS35029', 'DON09870', '2025-05-07', '10:00:00', '', '2025-04-19 11:50:32'),
('NMDS37557', 'DON65812', '2025-04-30', '10:00:00', '', '2025-04-18 17:29:29'),
('NMDS53798', 'DON22045', '2025-05-09', '15:00:00', '', '2025-04-18 23:07:26'),
('NMDS82370', 'DON82527', '2025-04-26', '12:00:00', '', '2025-04-18 17:53:38'),
('NMDS84777', 'DON75621', '2025-04-29', '11:00:00', '', '2025-04-19 16:52:23');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `OrderID` varchar(10) NOT NULL CHECK (`OrderID` like 'ORD%'),
  `UserID` varchar(10) NOT NULL,
  `OrderDate` datetime DEFAULT current_timestamp(),
  `TotalAmount` decimal(10,2) NOT NULL,
  `ShippingAddress` text DEFAULT NULL,
  `PaymentMethod` varchar(50) DEFAULT NULL,
  `Status` enum('Pending','Processing','Shipped','Delivered','Cancelled') DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`OrderID`, `UserID`, `OrderDate`, `TotalAmount`, `ShippingAddress`, `PaymentMethod`, `Status`) VALUES
('ORD17913', 'U54132', '2025-04-21 10:15:44', 750.00, NULL, NULL, 'Pending'),
('ORD29613', 'U54132', '2025-04-21 10:27:31', 750.00, NULL, NULL, 'Pending'),
('ORD99740', 'U54132', '2025-04-21 10:25:41', 3400.00, NULL, NULL, 'Pending');

-- --------------------------------------------------------

--
-- Table structure for table `orders_items`
--

CREATE TABLE `orders_items` (
  `ID` int(11) NOT NULL,
  `OrderID` varchar(10) NOT NULL,
  `ProductID` varchar(10) NOT NULL,
  `Quantity` int(11) NOT NULL,
  `Price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders_items`
--

INSERT INTO `orders_items` (`ID`, `OrderID`, `ProductID`, `Quantity`, `Price`) VALUES
(33, 'ORD17913', 'P34885', 1, 400.00),
(34, 'ORD99740', 'P60859', 1, 2800.00),
(35, 'ORD99740', 'P26876', 1, 250.00),
(36, 'ORD29613', 'P34885', 1, 400.00);

-- --------------------------------------------------------

--
-- Table structure for table `orders_items_backup`
--

CREATE TABLE `orders_items_backup` (
  `ID` int(11) NOT NULL,
  `OrderID` varchar(10) NOT NULL,
  `ProductID` varchar(10) NOT NULL,
  `Quantity` int(11) NOT NULL,
  `Price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders_items_backup`
--

INSERT INTO `orders_items_backup` (`ID`, `OrderID`, `ProductID`, `Quantity`, `Price`) VALUES
(14, 'ORD29359', 'P26876', 1, 250.00),
(15, 'ORD29359', 'P07991', 1, 750.00),
(16, 'ORD03025', 'P72622', 1, 5000.00),
(17, 'ORD41610', 'P07991', 1, 750.00),
(18, 'ORD91063', 'P80064', 1, 200.00),
(19, 'ORD10542', 'P07991', 1, 750.00),
(20, 'ORD75989', 'P72622', 1, 5000.00),
(21, 'ORD30820', 'P72622', 1, 5000.00),
(22, 'ORD30820', 'P16300', 1, 500.00),
(23, 'ORD04102', 'P16300', 1, 500.00),
(24, 'ORD97275', 'P07991', 1, 750.00),
(25, 'ORD65960', 'P80064', 1, 200.00),
(27, 'ORD44273', 'P07991', 1, 750.00),
(28, 'ORD44273', 'P63909', 1, 8900.00),
(29, 'ORD68620', 'P60859', 1, 2800.00),
(30, 'ORD82875', 'P26876', 1, 250.00);

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `ID` int(11) NOT NULL,
  `OrderID` varchar(10) NOT NULL,
  `PaymentAmount` decimal(10,2) NOT NULL,
  `PaymentMethod` varchar(50) NOT NULL,
  `Status` enum('Pending','Completed','Failed','Cancelled') DEFAULT 'Pending',
  `PaymentReference` varchar(50) DEFAULT NULL,
  `PaymentDetails` text DEFAULT NULL,
  `CreatedDate` datetime DEFAULT current_timestamp(),
  `ProcessedDate` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_inquiries`
--

CREATE TABLE `product_inquiries` (
  `InquiryID` varchar(10) NOT NULL CHECK (`InquiryID` like 'INQ%'),
  `UserID` varchar(10) NOT NULL,
  `ProductID` varchar(10) NOT NULL,
  `Message` text NOT NULL,
  `Response` text DEFAULT NULL,
  `Status` enum('Pending','Answered','Closed') DEFAULT 'Pending',
  `CreatedDate` datetime DEFAULT current_timestamp(),
  `ResponseDate` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_inquiries`
--

INSERT INTO `product_inquiries` (`InquiryID`, `UserID`, `ProductID`, `Message`, `Response`, `Status`, `CreatedDate`, `ResponseDate`) VALUES
('INQ03375', 'U54132', 'P18364', 'Is this made out of 100% brass', 'Dear Customer, \r\nIt is indeed made out of 100% Brass', 'Answered', '2025-03-20 00:10:51', '2025-03-20 00:13:13'),
('INQ16108', 'U54132', 'P26876', 'Hi Does this come in sets', 'Yeah it comes as a set of three bracelets', 'Answered', '2025-03-22 08:05:39', '2025-03-23 09:44:41');

-- --------------------------------------------------------

--
-- Table structure for table `recipients`
--

CREATE TABLE `recipients` (
  `RecipientID` varchar(10) NOT NULL CHECK (`RecipientID` like 'R%'),
  `UserID` varchar(10) NOT NULL,
  `FirstName` varchar(50) NOT NULL,
  `LastName` varchar(50) NOT NULL,
  `ContactNumber` varchar(15) NOT NULL,
  `Address` text NOT NULL,
  `OrganizationType` varchar(50) DEFAULT NULL,
  `VerificationStatus` enum('Pending','Approved','Rejected') DEFAULT 'Pending',
  `DocumentationURL` text NOT NULL,
  `ApprovalDate` datetime DEFAULT NULL,
  `ModeratorID` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `recipients`
--

INSERT INTO `recipients` (`RecipientID`, `UserID`, `FirstName`, `LastName`, `ContactNumber`, `Address`, `OrganizationType`, `VerificationStatus`, `DocumentationURL`, `ApprovalDate`, `ModeratorID`) VALUES
('R00001', 'U00001', 'Medical', 'Foundation', '0771234567', 'Colombo, Sri Lanka', 'NGO', 'Approved', 'medical_proof.pdf', NULL, NULL),
('R00002', 'U00002', 'Education', 'Support', '0772345678', 'Kandy, Sri Lanka', 'School', 'Approved', 'education_proof.pdf', NULL, NULL),
('R07287', 'U41094', 'Bianca', 'Miller', '0777564449', 'Mannar', 'Individual', 'Pending', '67c18e5152d97_processing.png', NULL, NULL),
('R12345', 'U12345', 'Test', 'Recipient', '1234567890', 'Test Address', 'School', 'Approved', '/uploads/documents/test.pdf', NULL, NULL),
('R34912', 'U04665', 'Kamla', 'Malkumari', '0761312397', 'No 67 Anuradhapura', 'Individual', 'Pending', '6802983bdf247_cta.jpg', NULL, NULL),
('R94785', 'U97431', 'Iyeshini', 'Kulathinga', '0776123454', 'No 78/A Nittabuwa', 'Individual', 'Pending', '680341f868128_ChatGPT Image Apr 10, 2025, 07_30_37 AM.png', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `refunds`
--

CREATE TABLE `refunds` (
  `ID` int(11) NOT NULL,
  `PaymentID` int(11) NOT NULL,
  `RefundAmount` decimal(10,2) NOT NULL,
  `RefundReason` text NOT NULL,
  `Status` enum('Processing','Completed','Failed') DEFAULT 'Processing',
  `RefundDetails` text DEFAULT NULL,
  `CreatedDate` datetime DEFAULT current_timestamp(),
  `ProcessedDate` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `regional_inventory`
--

CREATE TABLE `regional_inventory` (
  `ItemID` varchar(10) NOT NULL CHECK (`ItemID` like 'RI%'),
  `ItemName` varchar(100) NOT NULL,
  `Category` varchar(50) NOT NULL,
  `Quantity` int(11) NOT NULL DEFAULT 0,
  `Status` enum('Available','Reserved','Distributed') DEFAULT 'Available',
  `LastUpdated` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `RegionalOfficerID` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `regional_officers`
--

CREATE TABLE `regional_officers` (
  `RegionalOfficerID` varchar(10) NOT NULL CHECK (`RegionalOfficerID` like 'RO%'),
  `UserID` varchar(10) NOT NULL,
  `FirstName` varchar(50) NOT NULL,
  `LastName` varchar(50) NOT NULL,
  `ContactNumber` varchar(15) NOT NULL,
  `WarehouseLocation` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `regional_officers`
--

INSERT INTO `regional_officers` (`RegionalOfficerID`, `UserID`, `FirstName`, `LastName`, `ContactNumber`, `WarehouseLocation`) VALUES
('RO10001', 'U30001', 'Regional', 'Officer', '0777654321', 'Colombo Regional Warehouse, 123 Main St, Colombo');

-- --------------------------------------------------------

--
-- Table structure for table `sellers`
--

CREATE TABLE `sellers` (
  `SellerID` varchar(10) NOT NULL CHECK (`SellerID` like 'MS%'),
  `UserID` varchar(10) NOT NULL,
  `FirstName` varchar(50) NOT NULL,
  `LastName` varchar(50) NOT NULL,
  `ContactNumber` varchar(15) NOT NULL,
  `Address` text NOT NULL,
  `VerificationStatus` enum('Pending','Approved','Rejected') DEFAULT 'Pending',
  `TotalSales` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sellers`
--

INSERT INTO `sellers` (`SellerID`, `UserID`, `FirstName`, `LastName`, `ContactNumber`, `Address`, `VerificationStatus`, `TotalSales`) VALUES
('MS10001', 'U40000', 'Hopefull', 'Marketplace', '0777123456', 'Colombo Central Hub, Sri Lanka', 'Approved', 0.00),
('MS10002', 'U40001', 'Marketplace', 'Seller', '0777654321', 'Colombo Central Hub, Sri Lanka', 'Approved', 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `shipping_details`
--

CREATE TABLE `shipping_details` (
  `ShippingID` varchar(10) NOT NULL CHECK (`ShippingID` like 'SHP%'),
  `OrderID` varchar(10) NOT NULL,
  `ShippingAddress` text NOT NULL,
  `ContactPhone` varchar(15) NOT NULL,
  `PaymentMethod` enum('cash_on_delivery','bank_transfer','online_payment') NOT NULL,
  `ShippingNotes` text DEFAULT NULL,
  `TrackingNumber` varchar(50) DEFAULT NULL,
  `EstimatedDelivery` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shipping_details`
--

INSERT INTO `shipping_details` (`ShippingID`, `OrderID`, `ShippingAddress`, `ContactPhone`, `PaymentMethod`, `ShippingNotes`, `TrackingNumber`, `EstimatedDelivery`) VALUES
('SHP21535', 'ORD17913', '316 D/1 Wellahena 3rd Lane\r\nRagama, 11010\r\nWestern Province', '0761312397', 'cash_on_delivery', '', NULL, NULL),
('SHP33141', 'ORD99740', '316 D/1 Wellahena 3rd Lane\r\nRagama, 11010\r\nWestern Province', '0761312397', 'bank_transfer', '', NULL, NULL),
('SHP47222', 'ORD29613', 'no 316 D/1 Wellahena 3rd Lane , Welisara\r\nRagama, 11010\r\nWestern Province', '0761312397', 'cash_on_delivery', '', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `system_admins`
--

CREATE TABLE `system_admins` (
  `SystemAdminID` varchar(10) NOT NULL CHECK (`SystemAdminID` like 'SA%'),
  `UserID` varchar(10) NOT NULL,
  `FirstName` varchar(50) NOT NULL,
  `LastName` varchar(50) NOT NULL,
  `ContactNumber` varchar(15) DEFAULT NULL,
  `LastLoginTime` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `system_admins`
--

INSERT INTO `system_admins` (`SystemAdminID`, `UserID`, `FirstName`, `LastName`, `ContactNumber`, `LastLoginTime`) VALUES
('SA99999', 'U99999', 'System', 'Administrator', '1234567890', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `UserID` varchar(10) NOT NULL CHECK (`UserID` like 'U%'),
  `Email` varchar(100) NOT NULL,
  `Username` varchar(50) NOT NULL,
  `PasswordHash` varchar(255) NOT NULL,
  `UserType` enum('Donor','Recipient','SystemAdmin','AuthModerator','RegionalOfficer','DeliveryOfficer','Seller') NOT NULL,
  `UserStatus` enum('Active','Inactive','Banned') DEFAULT 'Active',
  `LastLogin` datetime DEFAULT NULL,
  `RegisteredDate` datetime DEFAULT current_timestamp(),
  `LastPasswordChange` datetime DEFAULT NULL,
  `PasswordResetToken` varchar(255) DEFAULT NULL,
  `PasswordResetExpiry` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`UserID`, `Email`, `Username`, `PasswordHash`, `UserType`, `UserStatus`, `LastLogin`, `RegisteredDate`, `LastPasswordChange`, `PasswordResetToken`, `PasswordResetExpiry`) VALUES
('U00001', 'healthcare_recipient@example.com', 'healthcare_rec', '$2y$10$someHashedPasswordHere', 'Recipient', 'Active', NULL, '2025-02-23 23:16:30', NULL, NULL, NULL),
('U00002', 'education_recipient@example.com', 'education_rec', '$2y$10$anotherHashedPasswordHere', 'Recipient', 'Active', NULL, '2025-02-23 23:16:30', NULL, NULL, NULL),
('U04665', 'kamala@gmail.com', 'kamala', '$2y$10$yoEhGUw5RjzCk/Tp9GIz3OKbpVDSSNx0ih9BAUGVk2aLPksKubVZ2', 'Recipient', 'Active', '2025-04-21 13:05:28', '2025-04-18 23:51:48', NULL, NULL, NULL),
('U12345', 'test@recipient.com', 'testrecipient', '$2y$10$dxsetFR/5LGnDiXy4nAZfeHByQNUfJr4mguF5ldH9kNizQyyjbEw.', 'Recipient', 'Active', '2025-03-02 14:41:03', '2025-02-21 21:38:41', NULL, NULL, NULL),
('U18909', 'likithachathu@gmail.com', 'likithachathu', '$2y$10$psQ.Ka8jAoMs7.lgTRBr0ORzqwWOnBBKWW2jroWA0livF7jZ0OgBK', 'Donor', 'Active', '2025-02-28 09:06:15', '2025-02-28 09:06:01', NULL, NULL, NULL),
('U20001', 'moderator@hopefull.com', 'moderator', '$2y$10$FKFPgxVkVYf5FWNI/dM8kOtYbrC2vvWDDt0IrSHy/iRiYiBeXsOGK', 'AuthModerator', 'Active', '2025-04-19 15:26:22', '2025-03-02 06:14:03', NULL, NULL, NULL),
('U21095', 'karuna@gmail.com', 'karuna', '$2y$10$/SodvJJo1TgtAEHmXXMOluTVo1ViKUNqXRWVril9ZvN3rqPi5HdSq', 'Donor', 'Active', NULL, '2025-02-24 22:37:24', NULL, NULL, NULL),
('U21965', 'roove@gmail.com', 'roove', '$2y$10$8uEBwh.c.UH.nGMsOmELbOgy8xu7c2nQNeO.7jlk662a5dly3S6cC', 'Donor', 'Active', '2025-02-27 23:00:24', '2025-02-23 13:01:26', NULL, NULL, NULL),
('U30001', 'officer@hopefull.com', 'officer', '$2y$10$VKwviW53ULmTKcLTgj6LBuJoGBg8jpP5opgN5GclZ09s6XtNf022e', 'RegionalOfficer', 'Active', '2025-04-21 12:32:02', '2025-03-02 12:44:27', NULL, NULL, NULL),
('U40000', 'marketplace@hopefull.com', 'marketplace', '$2y$10$someHashedPasswordHere', 'Seller', 'Active', NULL, '2025-03-07 10:59:12', NULL, NULL, NULL),
('U40001', 'seller@hopefull.com', 'seller', '$2y$10$6M5mO/k.QkplXbL5ftJiDeELUMluR.7gP.sLt2o9dwYkvvk0pbzl2', 'Seller', 'Active', '2025-04-21 13:17:22', '2025-03-17 15:59:48', NULL, NULL, NULL),
('U41094', 'miller@gmail.com', 'miller', '$2y$10$9YjcdPPVTxP2m7FQwYg1CO9Y.kJS.klI7mAakD.Ix90Qui8lWg48S', 'Recipient', 'Active', '2025-02-28 15:52:35', '2025-02-28 15:52:09', NULL, NULL, NULL),
('U54132', 'dulnm123@gmail.com', 'dulnm123', '$2y$10$0eYwP3hmn9C6e49V5fovAuVeR7JqX74m.rcNAWEkBAhDVcD0MNW9K', 'Donor', 'Active', '2025-04-21 13:16:09', '2025-02-24 03:33:02', NULL, NULL, NULL),
('U59360', 'meena@gmail.com', 'meena', '$2y$10$xOgEI3dS3vff9i4YViwis.Gu4vD697QYpeBcKvyU4vDzI3Zs9DImG', 'Donor', 'Active', '2025-02-28 15:39:59', '2025-02-28 15:39:48', NULL, NULL, NULL),
('U97431', 'Iyeshini@gmail.com', 'Iyeshini', '$2y$10$pKBT7DrPN6tPmLSWFpoG2uwCaA84ZXXy3fQ5WYsYdcGMSLYwLHKXO', 'Recipient', 'Active', '2025-04-19 11:56:53', '2025-04-19 11:56:00', NULL, NULL, NULL),
('U99999', 'admin@hopefull.org', 'admin', '$2y$10$fAc.CTCuDDl2wro9OUO0FuOyShYGvYYpjlAcvtLZhKPNvIaAvtAfC', 'SystemAdmin', 'Active', '2025-03-20 07:54:00', '2025-02-28 14:09:16', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_achievements`
--

CREATE TABLE `user_achievements` (
  `UserAchievementID` varchar(10) NOT NULL CHECK (`UserAchievementID` like 'UA%'),
  `DonorID` varchar(10) NOT NULL,
  `AchievementID` varchar(10) NOT NULL,
  `Progress` int(11) DEFAULT 0,
  `Completed` tinyint(1) DEFAULT 0,
  `CompletionDate` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_badges`
--

CREATE TABLE `user_badges` (
  `UserBadgeID` varchar(10) NOT NULL CHECK (`UserBadgeID` like 'UB%'),
  `DonorID` varchar(10) NOT NULL,
  `BadgeID` varchar(10) NOT NULL,
  `AwardedDate` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `achievements`
--
ALTER TABLE `achievements`
  ADD PRIMARY KEY (`AchievementID`);

--
-- Indexes for table `auth_moderators`
--
ALTER TABLE `auth_moderators`
  ADD PRIMARY KEY (`ModeratorID`),
  ADD KEY `UserID` (`UserID`);

--
-- Indexes for table `badges`
--
ALTER TABLE `badges`
  ADD PRIMARY KEY (`BadgeID`);

--
-- Indexes for table `deliveries`
--
ALTER TABLE `deliveries`
  ADD PRIMARY KEY (`DeliveryID`),
  ADD KEY `OrderID` (`OrderID`),
  ADD KEY `NonMonetaryDonationID` (`NonMonetaryDonationID`),
  ADD KEY `DeliveryOfficerID` (`DeliveryOfficerID`),
  ADD KEY `AssignedBy` (`AssignedBy`),
  ADD KEY `idx_delivery_status` (`Status`);

--
-- Indexes for table `delivery_officers`
--
ALTER TABLE `delivery_officers`
  ADD PRIMARY KEY (`DeliveryOfficerID`),
  ADD KEY `UserID` (`UserID`);

--
-- Indexes for table `donations`
--
ALTER TABLE `donations`
  ADD PRIMARY KEY (`DonationID`),
  ADD KEY `RequestID` (`RequestID`),
  ADD KEY `DonorID` (`DonorID`),
  ADD KEY `idx_donation_date` (`DonationDate`);

--
-- Indexes for table `donation_cancellations`
--
ALTER TABLE `donation_cancellations`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `DonationID` (`DonationID`);

--
-- Indexes for table `donation_requests`
--
ALTER TABLE `donation_requests`
  ADD PRIMARY KEY (`RequestID`),
  ADD KEY `RecipientID` (`RecipientID`),
  ADD KEY `ModeratorID` (`ModeratorID`),
  ADD KEY `idx_request_status` (`RequestStatus`),
  ADD KEY `idx_verification_status` (`VerificationStatus`);

--
-- Indexes for table `donors`
--
ALTER TABLE `donors`
  ADD PRIMARY KEY (`DonorID`),
  ADD KEY `UserID` (`UserID`);

--
-- Indexes for table `feedback_reports`
--
ALTER TABLE `feedback_reports`
  ADD PRIMARY KEY (`FeedbackID`),
  ADD KEY `DonationID` (`DonationID`),
  ADD KEY `RecipientID` (`RecipientID`);

--
-- Indexes for table `marketplace_inventory`
--
ALTER TABLE `marketplace_inventory`
  ADD PRIMARY KEY (`ProductID`),
  ADD KEY `SellerID` (`SellerID`);

--
-- Indexes for table `monetary_donation_details`
--
ALTER TABLE `monetary_donation_details`
  ADD PRIMARY KEY (`DetailID`),
  ADD KEY `RequestID` (`RequestID`);

--
-- Indexes for table `nonmonetary_donation_details`
--
ALTER TABLE `nonmonetary_donation_details`
  ADD PRIMARY KEY (`DetailID`),
  ADD KEY `RequestID` (`RequestID`);

--
-- Indexes for table `non_monetary_donation_scheduling`
--
ALTER TABLE `non_monetary_donation_scheduling`
  ADD PRIMARY KEY (`SchedulingID`),
  ADD KEY `DonationID` (`DonationID`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`OrderID`),
  ADD KEY `UserID` (`UserID`),
  ADD KEY `idx_order_status` (`Status`);

--
-- Indexes for table `orders_items`
--
ALTER TABLE `orders_items`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `orders_items_ibfk_1` (`OrderID`),
  ADD KEY `orders_items_ibfk_2` (`ProductID`);

--
-- Indexes for table `orders_items_backup`
--
ALTER TABLE `orders_items_backup`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `OrderID` (`OrderID`),
  ADD KEY `ProductID` (`ProductID`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `OrderID` (`OrderID`),
  ADD KEY `PaymentReference` (`PaymentReference`);

--
-- Indexes for table `product_inquiries`
--
ALTER TABLE `product_inquiries`
  ADD PRIMARY KEY (`InquiryID`),
  ADD KEY `UserID` (`UserID`),
  ADD KEY `ProductID` (`ProductID`);

--
-- Indexes for table `recipients`
--
ALTER TABLE `recipients`
  ADD PRIMARY KEY (`RecipientID`),
  ADD KEY `UserID` (`UserID`),
  ADD KEY `ModeratorID` (`ModeratorID`);

--
-- Indexes for table `refunds`
--
ALTER TABLE `refunds`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `PaymentID` (`PaymentID`);

--
-- Indexes for table `regional_inventory`
--
ALTER TABLE `regional_inventory`
  ADD PRIMARY KEY (`ItemID`),
  ADD KEY `RegionalOfficerID` (`RegionalOfficerID`);

--
-- Indexes for table `regional_officers`
--
ALTER TABLE `regional_officers`
  ADD PRIMARY KEY (`RegionalOfficerID`),
  ADD KEY `UserID` (`UserID`);

--
-- Indexes for table `sellers`
--
ALTER TABLE `sellers`
  ADD PRIMARY KEY (`SellerID`),
  ADD KEY `UserID` (`UserID`);

--
-- Indexes for table `shipping_details`
--
ALTER TABLE `shipping_details`
  ADD PRIMARY KEY (`ShippingID`),
  ADD KEY `shipping_details_ibfk_1` (`OrderID`);

--
-- Indexes for table `system_admins`
--
ALTER TABLE `system_admins`
  ADD PRIMARY KEY (`SystemAdminID`),
  ADD KEY `UserID` (`UserID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`UserID`),
  ADD UNIQUE KEY `Email` (`Email`),
  ADD UNIQUE KEY `Username` (`Username`),
  ADD KEY `idx_email` (`Email`),
  ADD KEY `idx_user_type` (`UserType`);

--
-- Indexes for table `user_achievements`
--
ALTER TABLE `user_achievements`
  ADD PRIMARY KEY (`UserAchievementID`),
  ADD KEY `AchievementID` (`AchievementID`),
  ADD KEY `DonorID` (`DonorID`);

--
-- Indexes for table `user_badges`
--
ALTER TABLE `user_badges`
  ADD PRIMARY KEY (`UserBadgeID`),
  ADD KEY `BadgeID` (`BadgeID`),
  ADD KEY `DonorID` (`DonorID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `donation_cancellations`
--
ALTER TABLE `donation_cancellations`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `orders_items`
--
ALTER TABLE `orders_items`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `orders_items_backup`
--
ALTER TABLE `orders_items_backup`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `refunds`
--
ALTER TABLE `refunds`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `auth_moderators`
--
ALTER TABLE `auth_moderators`
  ADD CONSTRAINT `auth_moderators_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`) ON DELETE CASCADE;

--
-- Constraints for table `deliveries`
--
ALTER TABLE `deliveries`
  ADD CONSTRAINT `deliveries_ibfk_1` FOREIGN KEY (`OrderID`) REFERENCES `orders` (`OrderID`),
  ADD CONSTRAINT `deliveries_ibfk_2` FOREIGN KEY (`NonMonetaryDonationID`) REFERENCES `nonmonetary_donation_details` (`DetailID`),
  ADD CONSTRAINT `deliveries_ibfk_3` FOREIGN KEY (`DeliveryOfficerID`) REFERENCES `delivery_officers` (`DeliveryOfficerID`),
  ADD CONSTRAINT `deliveries_ibfk_4` FOREIGN KEY (`AssignedBy`) REFERENCES `users` (`UserID`);

--
-- Constraints for table `delivery_officers`
--
ALTER TABLE `delivery_officers`
  ADD CONSTRAINT `delivery_officers_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`) ON DELETE CASCADE;

--
-- Constraints for table `donations`
--
ALTER TABLE `donations`
  ADD CONSTRAINT `donations_ibfk_1` FOREIGN KEY (`RequestID`) REFERENCES `donation_requests` (`RequestID`),
  ADD CONSTRAINT `donations_ibfk_2` FOREIGN KEY (`DonorID`) REFERENCES `donors` (`DonorID`);

--
-- Constraints for table `donation_requests`
--
ALTER TABLE `donation_requests`
  ADD CONSTRAINT `donation_requests_ibfk_1` FOREIGN KEY (`RecipientID`) REFERENCES `recipients` (`RecipientID`),
  ADD CONSTRAINT `donation_requests_ibfk_2` FOREIGN KEY (`ModeratorID`) REFERENCES `auth_moderators` (`ModeratorID`);

--
-- Constraints for table `donors`
--
ALTER TABLE `donors`
  ADD CONSTRAINT `donors_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`) ON DELETE CASCADE;

--
-- Constraints for table `feedback_reports`
--
ALTER TABLE `feedback_reports`
  ADD CONSTRAINT `feedback_reports_ibfk_1` FOREIGN KEY (`DonationID`) REFERENCES `donations` (`DonationID`),
  ADD CONSTRAINT `feedback_reports_ibfk_2` FOREIGN KEY (`RecipientID`) REFERENCES `recipients` (`RecipientID`);

--
-- Constraints for table `marketplace_inventory`
--
ALTER TABLE `marketplace_inventory`
  ADD CONSTRAINT `marketplace_inventory_ibfk_1` FOREIGN KEY (`SellerID`) REFERENCES `sellers` (`SellerID`);

--
-- Constraints for table `monetary_donation_details`
--
ALTER TABLE `monetary_donation_details`
  ADD CONSTRAINT `monetary_donation_details_ibfk_1` FOREIGN KEY (`RequestID`) REFERENCES `donation_requests` (`RequestID`) ON DELETE CASCADE;

--
-- Constraints for table `nonmonetary_donation_details`
--
ALTER TABLE `nonmonetary_donation_details`
  ADD CONSTRAINT `nonmonetary_donation_details_ibfk_1` FOREIGN KEY (`RequestID`) REFERENCES `donation_requests` (`RequestID`) ON DELETE CASCADE;

--
-- Constraints for table `non_monetary_donation_scheduling`
--
ALTER TABLE `non_monetary_donation_scheduling`
  ADD CONSTRAINT `non_monetary_donation_scheduling_ibfk_1` FOREIGN KEY (`DonationID`) REFERENCES `donations` (`DonationID`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`);

--
-- Constraints for table `orders_items`
--
ALTER TABLE `orders_items`
  ADD CONSTRAINT `orders_items_ibfk_1` FOREIGN KEY (`OrderID`) REFERENCES `orders` (`OrderID`) ON DELETE CASCADE,
  ADD CONSTRAINT `orders_items_ibfk_2` FOREIGN KEY (`ProductID`) REFERENCES `marketplace_inventory` (`ProductID`);

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`OrderID`) REFERENCES `orders` (`OrderID`) ON DELETE CASCADE;

--
-- Constraints for table `product_inquiries`
--
ALTER TABLE `product_inquiries`
  ADD CONSTRAINT `product_inquiries_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_inquiries_ibfk_2` FOREIGN KEY (`ProductID`) REFERENCES `marketplace_inventory` (`ProductID`);

--
-- Constraints for table `recipients`
--
ALTER TABLE `recipients`
  ADD CONSTRAINT `recipients_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`) ON DELETE CASCADE,
  ADD CONSTRAINT `recipients_ibfk_2` FOREIGN KEY (`ModeratorID`) REFERENCES `auth_moderators` (`ModeratorID`);

--
-- Constraints for table `refunds`
--
ALTER TABLE `refunds`
  ADD CONSTRAINT `refunds_ibfk_1` FOREIGN KEY (`PaymentID`) REFERENCES `payments` (`ID`) ON DELETE CASCADE;

--
-- Constraints for table `regional_inventory`
--
ALTER TABLE `regional_inventory`
  ADD CONSTRAINT `regional_inventory_ibfk_1` FOREIGN KEY (`RegionalOfficerID`) REFERENCES `regional_officers` (`RegionalOfficerID`);

--
-- Constraints for table `regional_officers`
--
ALTER TABLE `regional_officers`
  ADD CONSTRAINT `regional_officers_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`) ON DELETE CASCADE;

--
-- Constraints for table `sellers`
--
ALTER TABLE `sellers`
  ADD CONSTRAINT `sellers_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`) ON DELETE CASCADE;

--
-- Constraints for table `shipping_details`
--
ALTER TABLE `shipping_details`
  ADD CONSTRAINT `shipping_details_ibfk_1` FOREIGN KEY (`OrderID`) REFERENCES `orders` (`OrderID`) ON DELETE CASCADE;

--
-- Constraints for table `system_admins`
--
ALTER TABLE `system_admins`
  ADD CONSTRAINT `system_admins_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`) ON DELETE CASCADE;

--
-- Constraints for table `user_achievements`
--
ALTER TABLE `user_achievements`
  ADD CONSTRAINT `user_achievements_ibfk_1` FOREIGN KEY (`DonorID`) REFERENCES `donors` (`DonorID`),
  ADD CONSTRAINT `user_achievements_ibfk_2` FOREIGN KEY (`AchievementID`) REFERENCES `achievements` (`AchievementID`);

--
-- Constraints for table `user_badges`
--
ALTER TABLE `user_badges`
  ADD CONSTRAINT `user_badges_ibfk_1` FOREIGN KEY (`DonorID`) REFERENCES `donors` (`DonorID`),
  ADD CONSTRAINT `user_badges_ibfk_2` FOREIGN KEY (`BadgeID`) REFERENCES `badges` (`BadgeID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
