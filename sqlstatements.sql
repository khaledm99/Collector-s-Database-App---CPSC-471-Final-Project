-- All implemented SQL Code
-- all code is implemented via 
-- parameterization in the php files
-- for SQL injection security:

-- Executed upon registration:
-- Adds a client with a new super-colleciton,
-- one sub-collection,
-- one wishlist, and one collection report entity.
INSERT INTO CLIENT (Username, Date_Joined, Email) 
    VALUES (@Username, curdate(), @Email);
INSERT INTO SUPER_COLLECTION (Name, Owner_username, no_of_subcollections) 
    VALUES (@Username, @Username, 1);
INSERT INTO SUB_COLLECTION (Name, Super_collection_name) 
    VALUES (@sub_col_name, @super_col_name);
INSERT INTO WISHLIST (Owner_username) 
    VALUES (@Username);
INSERT INTO REPORT (Sub_collection_name, Super_collection_name) 
    VALUES (@sub_col_name, @super_col_name);

-- executed upon login:
SELECT Username FROM CLIENT WHERE CLIENT.Username = @Username;  -- First checks login against Client table, 
SELECT Username FROM ADMIN WHERE ADMIN.Username = @Username;    -- if no user is found, checks against Admin table, otherwise shows error

-- admin dashboard button for generating system report:
INSERT INTO SYSTEM_REPORT (Timestamp) 
    VALUES (@Timestamp);
INSERT INTO GENERATES_SYS_REPORT (Admin_username, Report_timestamp, Super_collection_name) 
    VALUES (@Username, @Timestamp, @super_col_name);

-- Queries for sub-collections:
-- Selects this user's sub-collections:
SELECT SUB_COLLECTION.Name FROM SUB_COLLECTION, SUPER_COLLECTION            
    WHERE SUB_COLLECTION.Super_collection_name = SUPER_COLLECTION.Name AND Owner_username = @Username;  
-- Selects this user's sub-collections using a search term:
SELECT SUB_COLLECTION.Name FROM SUB_COLLECTION, SUPER_COLLECTION
    WHERE SUB_COLLECTION.Super_collection_name = SUPER_COLLECTION.Name 
    AND Owner_username = @Username AND SUB_COLLECTION.Name like @Search_term;   
INSERT INTO SUB_COLLECTION (Name, Super_collection_name) -- Adds a new sub-collection
    VALUES (@sub_col_name, @super_col_name);

-- Query for viewing this user's wishlist:
SELECT Name,ITEM.ITEM_ID FROM ITEM, TITLE  WHERE TITLE.ITEM_ID = ITEM.ITEM_ID AND ITEM.Wishlist_name = @Username 
    UNION select Name,ITEM.ITEM_ID  FROM ITEM, CONSOLE WHERE ITEM.ITEM_ID = CONSOLE.ITEM_ID AND ITEM.Wishlist_name = @Username
    UNION select Name,ITEM.ITEM_ID  FROM ITEM, CONTROLLER WHERE ITEM.ITEM_ID = CONTROLLER.ITEM_ID AND ITEM.Wishlist_name = @Username
    UNION select Name,ITEM.ITEM_ID  FROM ITEM, STORAGE_DEVICE WHERE ITEM.ITEM_ID = STORAGE_DEVICE.ITEM_ID AND ITEM.Wishlist_name = @Username
    UNION select Name,ITEM.ITEM_ID  FROM ITEM, MISC_PERIPHERAL WHERE ITEM.ITEM_ID = MISC_PERIPHERAL.ITEM_ID AND ITEM.Wishlist_name = @Username
    UNION select Name,ITEM.ITEM_ID  FROM ITEM, SUBSCRIPTION WHERE ITEM.ITEM_ID = SUBSCRIPTION.ITEM_ID AND ITEM.Wishlist_name = @Username;

-- Queries for viewing/adding/editing items in a collection:
-- Selects all items (titles and console) in this collection:
SELECT in_col.item_id AS ID, t.name AS Name, 'title' AS Type FROM IN_COLLECTION AS in_col, TITLE AS t 
    WHERE in_col.Collection_name = @sub_col_name AND in_col.Item_ID = t.Item_ID
    UNION
SELECT in_col.item_id AS ID, c.name AS Name, 'console' as Type FROM IN_COLLECTION AS in_col, CONSOLE AS c 
    WHERE in_col.Collection_name = @sub_col_name AND in_col.Item_ID = c.Item_ID;

-- php code to create a new item by incrementing the item ID 
<?php
function addItem($conn){
    $sql = "SELECT MAX(Item_ID) FROM ITEM";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_row($result);
    $itemID = $row[0] + 1;
    $sql = "INSERT INTO ITEM (Item_ID, Is_owned) VALUES (".$itemID.",TRUE)";
    $result = mysqli_query($conn, $sql);
    return $itemID;
}
?>
-- Inserts the basic item into this sub-collection:
INSERT INTO IN_COLLECTION (Item_ID, Collection_name) 
    VALUES (@ItemID, @sub_col_name)
-- Specifies the new item as a title
INSERT INTO TITLE (Name, Play_status, Release_year, Playtime, Game_type, Edition, Rating, Item_ID) 
    VALUES (?,?,?,?,?,?,?,?) -- all values inserted via paramterization
-- Specifies the new item as a console
INSERT INTO CONSOLE (Name, Serial_no, Internal_storage_capacity, Type, Edition, Item_ID) 
    VALUES (?,?,?,?,?,?) -- all values inserted via parameterization

-- Delete an item
DELETE FROM ITEM where ITEM.Item_ID = ?
-- View an item
SELECT * FROM {@itemtype} WHERE {@itemtype}.ITEM_ID = @ItemID
-- Edit an item (only implemented console/title)
-- all values updated via paramterization
UPDATE CONSOLE  SET  Name = ?, Serial_no = ?, 
    CONSOLE.Condition = ?, 
    Internal_storage_capacity = ?, Type = ?,
    Edition = ? ,Quantity = ?,Date_acquired = ? , 
    Company_made_by = ? WHERE Item_ID = ?;
UPDATE TITLE  SET  Name = ?, Play_status = ?, 
    Release_year = ?, Playtime = ?,Game_type = ?,
    Edition = ? ,Rating = ? WHERE Item_ID = ?;


-- queries for super-collection report:
-- Selects all items in all sub-collections in this super-collection (do be counted for report):
SELECT TITLE.Name FROM ITEM, TITLE, IN_COLLECTION, SUB_COLLECTION, SUPER_COLLECTION 
    WHERE TITLE.ITEM_ID = ITEM.ITEM_ID AND ITEM.ITEM_ID = IN_COLLECTION.ITEM_ID 
        AND IN_COLLECTION.Collection_name = SUB_COLLECTION.Name 
        AND SUB_COLLECTION.Super_collection_name = SUPER_COLLECTION.Name AND SUPER_COLLECTION.Owner_username = @Username 
    UNION select CONSOLE.Name FROM ITEM, CONSOLE, IN_COLLECTION, SUB_COLLECTION, SUPER_COLLECTION 
    WHERE ITEM.ITEM_ID = CONSOLE.ITEM_ID AND ITEM.ITEM_ID = IN_COLLECTION.ITEM_ID 
        AND IN_COLLECTION.Collection_name = SUB_COLLECTION.Name 
        AND SUB_COLLECTION.Super_collection_name = SUPER_COLLECTION.Name AND SUPER_COLLECTION.Owner_username = @Username 
    UNION select CONTROLLER.Name FROM ITEM, CONTROLLER, IN_COLLECTION, SUB_COLLECTION, SUPER_COLLECTION 
    WHERE ITEM.ITEM_ID = CONTROLLER.ITEM_ID AND ITEM.ITEM_ID = IN_COLLECTION.ITEM_ID 
        AND IN_COLLECTION.Collection_name = SUB_COLLECTION.Name 
        AND SUB_COLLECTION.Super_collection_name = SUPER_COLLECTION.Name AND SUPER_COLLECTION.Owner_username = @Username 
    UNION select STORAGE_DEVICE.Name FROM ITEM, STORAGE_DEVICE, IN_COLLECTION, SUB_COLLECTION, SUPER_COLLECTION 
    WHERE ITEM.ITEM_ID = STORAGE_DEVICE.ITEM_ID AND ITEM.ITEM_ID = IN_COLLECTION.ITEM_ID 
        AND IN_COLLECTION.Collection_name = SUB_COLLECTION.Name 
        AND SUB_COLLECTION.Super_collection_name = SUPER_COLLECTION.Name AND SUPER_COLLECTION.Owner_username = @Username 
    UNION select MISC_PERIPHERAL.Name FROM ITEM, MISC_PERIPHERAL, IN_COLLECTION, SUB_COLLECTION, SUPER_COLLECTION 
    WHERE ITEM.ITEM_ID = MISC_PERIPHERAL.ITEM_ID AND ITEM.ITEM_ID = IN_COLLECTION.ITEM_ID 
        AND IN_COLLECTION.Collection_name = SUB_COLLECTION.Name 
        AND SUB_COLLECTION.Super_collection_name = SUPER_COLLECTION.Name AND SUPER_COLLECTION.Owner_username = @Username 
    UNION select SUBSCRIPTION.Name FROM ITEM, SUBSCRIPTION, IN_COLLECTION, SUB_COLLECTION, SUPER_COLLECTION 
    WHERE ITEM.ITEM_ID = SUBSCRIPTION.ITEM_ID AND ITEM.ITEM_ID = IN_COLLECTION.ITEM_ID 
        AND IN_COLLECTION.Collection_name = SUB_COLLECTION.Name 
        AND SUB_COLLECTION.Super_collection_name = SUPER_COLLECTION.Name AND SUPER_COLLECTION.Owner_username = @Username;
-- Selects all items in this user's wishlist (to be counted for report):
SELECT Name FROM ITEM, TITLE  WHERE TITLE.ITEM_ID = ITEM.ITEM_ID AND ITEM.Wishlist_name = @Username 
    UNION select Name FROM ITEM, CONSOLE WHERE ITEM.ITEM_ID = CONSOLE.ITEM_ID AND ITEM.Wishlist_name = @Username
    UNION select Name FROM ITEM, CONTROLLER WHERE ITEM.ITEM_ID = CONTROLLER.ITEM_ID AND ITEM.Wishlist_name = @Username
    UNION select Name FROM ITEM, STORAGE_DEVICE WHERE ITEM.ITEM_ID = STORAGE_DEVICE.ITEM_ID AND ITEM.Wishlist_name = @Username
    UNION select Name FROM ITEM, MISC_PERIPHERAL WHERE ITEM.ITEM_ID = MISC_PERIPHERAL.ITEM_ID AND ITEM.Wishlist_name = @Username
    UNION select Name FROM ITEM, SUBSCRIPTION WHERE ITEM.ITEM_ID = SUBSCRIPTION.ITEM_ID AND ITEM.Wishlist_name = @Username
-- Selects all sub-collections in this super-collection (to be counted for report):
SELECT Sub_collection.Name from Sub_collection, Super_collection 
    WHERE Sub_collection.Super_collection_name = Super_collection.Name and Super_collection.Owner_username = @Username;

-- Statement to view all system reports generated by admins:
SELECT SYSTEM_REPORT.Timestamp,GENERATES_SYS_REPORT.Admin_username FROM SYSTEM_REPORT,GENERATES_SYS_REPORT WHERE 
SYSTEM_REPORT.Timestamp = GENERATES_SYS_REPORT.Report_timestamp;

-- Statement for admins to view all users:
SELECT * FROM CLIENT;


-- The following is all the statmements necessary to 
-- create the database, as dumped by phpmyadmin
--
-- Database: `main`
--
CREATE DATABASE IF NOT EXISTS `main` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
USE `main`;

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

DROP TABLE IF EXISTS `admin`;
CREATE TABLE `admin` (
  `Username` varchar(24) NOT NULL,
  `Email` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `client`
--

DROP TABLE IF EXISTS `client`;
CREATE TABLE `client` (
  `Username` varchar(24) NOT NULL,
  `Date_Joined` date NOT NULL,
  `Email` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `company`
--

DROP TABLE IF EXISTS `company`;
CREATE TABLE `company` (
  `NAME` varchar(255) NOT NULL,
  `Rating` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `company_link`
--

DROP TABLE IF EXISTS `company_link`;
CREATE TABLE `company_link` (
  `Company_name` varchar(255) NOT NULL,
  `Link` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `console`
--

DROP TABLE IF EXISTS `console`;
CREATE TABLE `console` (
  `Name` varchar(255) NOT NULL,
  `Serial_no` varchar(64) NOT NULL,
  `Condition` varchar(255) DEFAULT NULL,
  `Internal_storage_capacity` float DEFAULT NULL,
  `Type` varchar(255) DEFAULT NULL,
  `Edition` varchar(255) DEFAULT NULL,
  `Quantity` int(11) DEFAULT NULL,
  `Date_acquired` date DEFAULT NULL,
  `Company_made_by` varchar(255) DEFAULT NULL,
  `Item_ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `console_color`
--

DROP TABLE IF EXISTS `console_color`;
CREATE TABLE `console_color` (
  `Console_name` varchar(255) NOT NULL,
  `Console_serial_no` varchar(64) NOT NULL,
  `Color` varchar(16) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `controller`
--

DROP TABLE IF EXISTS `controller`;
CREATE TABLE `controller` (
  `Name` varchar(255) NOT NULL,
  `Serial_no` varchar(64) NOT NULL,
  `Value` float DEFAULT NULL,
  `Date_acquired` date DEFAULT NULL,
  `Quantity` int(11) DEFAULT NULL,
  `Condition` varchar(255) DEFAULT NULL,
  `Connection_type` varchar(16) DEFAULT NULL,
  `Battery_type` varchar(8) DEFAULT NULL,
  `Item_ID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `developed_by`
--

DROP TABLE IF EXISTS `developed_by`;
CREATE TABLE `developed_by` (
  `Title_name` varchar(255) NOT NULL,
  `Developer_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `developer`
--

DROP TABLE IF EXISTS `developer`;
CREATE TABLE `developer` (
  `Name` varchar(255) NOT NULL,
  `Type` varchar(32) DEFAULT NULL,
  `no_of_titles_by_dev` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `generates_collection_report`
--

DROP TABLE IF EXISTS `generates_collection_report`;
CREATE TABLE `generates_collection_report` (
  `Sub_collection_name` varchar(64) NOT NULL,
  `Super_collection_name` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `generates_sys_report`
--

DROP TABLE IF EXISTS `generates_sys_report`;
CREATE TABLE `generates_sys_report` (
  `Admin_username` varchar(24) NOT NULL,
  `Report_timestamp` timestamp NOT NULL,
  `Super_collection_name` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `in_collection`
--

DROP TABLE IF EXISTS `in_collection`;
CREATE TABLE `in_collection` (
  `Item_ID` int(11) NOT NULL,
  `Collection_name` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `item`
--

DROP TABLE IF EXISTS `item`;
CREATE TABLE `item` (
  `Item_ID` int(11) NOT NULL,
  `Is_owned` tinyint(1) NOT NULL,
  `Wishlist_name` varchar(24) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `misc_peripheral`
--

DROP TABLE IF EXISTS `misc_peripheral`;
CREATE TABLE `misc_peripheral` (
  `Name` varchar(255) NOT NULL,
  `Serial_no` varchar(64) NOT NULL,
  `Value` float DEFAULT NULL,
  `Date_acquired` date DEFAULT NULL,
  `Quantity` int(11) DEFAULT NULL,
  `Condition` varchar(255) DEFAULT NULL,
  `Type` varchar(64) DEFAULT NULL,
  `Connection_type` varchar(16) DEFAULT NULL,
  `Item_ID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `offers`
--

DROP TABLE IF EXISTS `offers`;
CREATE TABLE `offers` (
  `Platform_name` varchar(255) NOT NULL,
  `Subscription_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pc`
--

DROP TABLE IF EXISTS `pc`;
CREATE TABLE `pc` (
  `Console_name` varchar(255) NOT NULL,
  `Console_serial_no` varchar(64) NOT NULL,
  `Type` varchar(255) DEFAULT NULL,
  `RAM` int(11) DEFAULT NULL,
  `Graphics_card` varchar(255) DEFAULT NULL,
  `Power_source` varchar(255) DEFAULT NULL,
  `Motherboard` varchar(255) DEFAULT NULL,
  `CPU` varchar(255) DEFAULT NULL,
  `Cooling_type` varchar(255) DEFAULT NULL,
  `no_of_usb_ports` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `platform`
--

DROP TABLE IF EXISTS `platform`;
CREATE TABLE `platform` (
  `Name` varchar(255) NOT NULL,
  `Company_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `plays_on`
--

DROP TABLE IF EXISTS `plays_on`;
CREATE TABLE `plays_on` (
  `Title_name` varchar(255) NOT NULL,
  `Platform_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `published_by`
--

DROP TABLE IF EXISTS `published_by`;
CREATE TABLE `published_by` (
  `Title_name` varchar(255) NOT NULL,
  `Publisher_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `publisher`
--

DROP TABLE IF EXISTS `publisher`;
CREATE TABLE `publisher` (
  `Name` varchar(255) NOT NULL,
  `no_of_titles_by_publisher` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `report`
--

DROP TABLE IF EXISTS `report`;
CREATE TABLE `report` (
  `Sub_collection_name` varchar(64) NOT NULL,
  `Super_collection_name` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `storage_device`
--

DROP TABLE IF EXISTS `storage_device`;
CREATE TABLE `storage_device` (
  `Name` varchar(255) NOT NULL,
  `Serial_no` varchar(64) NOT NULL,
  `Value` float DEFAULT NULL,
  `Date_acquired` date DEFAULT NULL,
  `Quantity` int(11) DEFAULT NULL,
  `Condition` varchar(255) DEFAULT NULL,
  `Size_of_storage` float DEFAULT NULL,
  `Item_ID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subscription`
--

DROP TABLE IF EXISTS `subscription`;
CREATE TABLE `subscription` (
  `Name` varchar(255) NOT NULL,
  `Price` float DEFAULT NULL,
  `Start_date` date DEFAULT NULL,
  `End_date` date DEFAULT NULL,
  `Type` varchar(255) DEFAULT NULL,
  `Item_ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sub_collection`
--

DROP TABLE IF EXISTS `sub_collection`;
CREATE TABLE `sub_collection` (
  `Name` varchar(64) NOT NULL,
  `Super_collection_name` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `super_collection`
--

DROP TABLE IF EXISTS `super_collection`;
CREATE TABLE `super_collection` (
  `Name` varchar(64) NOT NULL,
  `Owner_username` varchar(24) NOT NULL,
  `no_of_subcollections` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `supports`
--

DROP TABLE IF EXISTS `supports`;
CREATE TABLE `supports` (
  `Platform_name` varchar(255) NOT NULL,
  `Console_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `system_report`
--

DROP TABLE IF EXISTS `system_report`;
CREATE TABLE `system_report` (
  `Timestamp` timestamp NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `title`
--

DROP TABLE IF EXISTS `title`;
CREATE TABLE `title` (
  `Name` varchar(255) NOT NULL,
  `Play_status` varchar(24) DEFAULT NULL,
  `Release_year` year(4) DEFAULT NULL,
  `Playtime` float DEFAULT NULL,
  `Game_type` varchar(255) DEFAULT NULL,
  `Edition` varchar(255) DEFAULT NULL,
  `Rating` float DEFAULT NULL,
  `Item_ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `title_genre`
--

DROP TABLE IF EXISTS `title_genre`;
CREATE TABLE `title_genre` (
  `Title_name` varchar(255) NOT NULL,
  `Genre` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

DROP TABLE IF EXISTS `wishlist`;
CREATE TABLE `wishlist` (
  `Owner_username` varchar(24) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`Username`);

--
-- Indexes for table `client`
--
ALTER TABLE `client`
  ADD PRIMARY KEY (`Username`);

--
-- Indexes for table `company`
--
ALTER TABLE `company`
  ADD PRIMARY KEY (`NAME`);

--
-- Indexes for table `company_link`
--
ALTER TABLE `company_link`
  ADD PRIMARY KEY (`Company_name`,`Link`);

--
-- Indexes for table `console`
--
ALTER TABLE `console`
  ADD PRIMARY KEY (`Name`,`Serial_no`),
  ADD KEY `console_ibfk_2` (`Item_ID`),
  ADD KEY `console_ibfk_1` (`Company_made_by`);

--
-- Indexes for table `console_color`
--
ALTER TABLE `console_color`
  ADD PRIMARY KEY (`Console_name`,`Console_serial_no`,`Color`);

--
-- Indexes for table `controller`
--
ALTER TABLE `controller`
  ADD PRIMARY KEY (`Name`,`Serial_no`),
  ADD KEY `controller_ibfk_1` (`Item_ID`);

--
-- Indexes for table `developed_by`
--
ALTER TABLE `developed_by`
  ADD PRIMARY KEY (`Title_name`,`Developer_name`),
  ADD KEY `Developer_name` (`Developer_name`);

--
-- Indexes for table `developer`
--
ALTER TABLE `developer`
  ADD PRIMARY KEY (`Name`);

--
-- Indexes for table `generates_collection_report`
--
ALTER TABLE `generates_collection_report`
  ADD PRIMARY KEY (`Sub_collection_name`,`Super_collection_name`),
  ADD KEY `Super_collection_name` (`Super_collection_name`);

--
-- Indexes for table `generates_sys_report`
--
ALTER TABLE `generates_sys_report`
  ADD PRIMARY KEY (`Admin_username`,`Report_timestamp`,`Super_collection_name`),
  ADD KEY `Report_timestamp` (`Report_timestamp`),
  ADD KEY `Super_collection_name` (`Super_collection_name`);

--
-- Indexes for table `in_collection`
--
ALTER TABLE `in_collection`
  ADD PRIMARY KEY (`Item_ID`,`Collection_name`),
  ADD KEY `Collection_name` (`Collection_name`);

--
-- Indexes for table `item`
--
ALTER TABLE `item`
  ADD PRIMARY KEY (`Item_ID`),
  ADD KEY `item_ibfk_1` (`Wishlist_name`);

--
-- Indexes for table `misc_peripheral`
--
ALTER TABLE `misc_peripheral`
  ADD PRIMARY KEY (`Name`,`Serial_no`),
  ADD KEY `misc_peripheral_ibfk_1` (`Item_ID`);

--
-- Indexes for table `offers`
--
ALTER TABLE `offers`
  ADD PRIMARY KEY (`Platform_name`,`Subscription_name`),
  ADD KEY `Subscription_name` (`Subscription_name`);

--
-- Indexes for table `pc`
--
ALTER TABLE `pc`
  ADD PRIMARY KEY (`Console_name`,`Console_serial_no`);

--
-- Indexes for table `platform`
--
ALTER TABLE `platform`
  ADD PRIMARY KEY (`Name`),
  ADD KEY `Company_name` (`Company_name`);

--
-- Indexes for table `plays_on`
--
ALTER TABLE `plays_on`
  ADD PRIMARY KEY (`Title_name`,`Platform_name`),
  ADD KEY `Platform_name` (`Platform_name`);

--
-- Indexes for table `published_by`
--
ALTER TABLE `published_by`
  ADD PRIMARY KEY (`Title_name`,`Publisher_name`),
  ADD KEY `Publisher_name` (`Publisher_name`);

--
-- Indexes for table `publisher`
--
ALTER TABLE `publisher`
  ADD PRIMARY KEY (`Name`);

--
-- Indexes for table `report`
--
ALTER TABLE `report`
  ADD PRIMARY KEY (`Sub_collection_name`,`Super_collection_name`),
  ADD KEY `Super_collection_name` (`Super_collection_name`);

--
-- Indexes for table `storage_device`
--
ALTER TABLE `storage_device`
  ADD PRIMARY KEY (`Name`,`Serial_no`),
  ADD KEY `storage_device_ibfk_1` (`Item_ID`);

--
-- Indexes for table `subscription`
--
ALTER TABLE `subscription`
  ADD PRIMARY KEY (`Name`),
  ADD KEY `subscription_ibfk_1` (`Item_ID`);

--
-- Indexes for table `sub_collection`
--
ALTER TABLE `sub_collection`
  ADD PRIMARY KEY (`Name`),
  ADD KEY `Super_collection_name` (`Super_collection_name`);

--
-- Indexes for table `super_collection`
--
ALTER TABLE `super_collection`
  ADD PRIMARY KEY (`Name`),
  ADD KEY `Owner_username` (`Owner_username`);

--
-- Indexes for table `supports`
--
ALTER TABLE `supports`
  ADD PRIMARY KEY (`Platform_name`,`Console_name`),
  ADD KEY `Console_name` (`Console_name`);

--
-- Indexes for table `system_report`
--
ALTER TABLE `system_report`
  ADD PRIMARY KEY (`Timestamp`);

--
-- Indexes for table `title`
--
ALTER TABLE `title`
  ADD PRIMARY KEY (`Name`),
  ADD KEY `title_ibfk_1` (`Item_ID`);

--
-- Indexes for table `title_genre`
--
ALTER TABLE `title_genre`
  ADD PRIMARY KEY (`Title_name`,`Genre`);

--
-- Indexes for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`Owner_username`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `company_link`
--
ALTER TABLE `company_link`
  ADD CONSTRAINT `company_link_ibfk_1` FOREIGN KEY (`Company_name`) REFERENCES `company` (`NAME`);

--
-- Constraints for table `console`
--
ALTER TABLE `console`
  ADD CONSTRAINT `console_ibfk_1` FOREIGN KEY (`Company_made_by`) REFERENCES `company` (`NAME`),
  ADD CONSTRAINT `console_ibfk_2` FOREIGN KEY (`Item_ID`) REFERENCES `item` (`Item_ID`) ON DELETE CASCADE ON UPDATE RESTRICT;

--
-- Constraints for table `console_color`
--
ALTER TABLE `console_color`
  ADD CONSTRAINT `console_color_ibfk_1` FOREIGN KEY (`Console_name`,`Console_serial_no`) REFERENCES `console` (`Name`, `Serial_no`);

--
-- Constraints for table `controller`
--
ALTER TABLE `controller`
  ADD CONSTRAINT `controller_ibfk_1` FOREIGN KEY (`Item_ID`) REFERENCES `item` (`Item_ID`) ON DELETE CASCADE ON UPDATE RESTRICT;

--
-- Constraints for table `developed_by`
--
ALTER TABLE `developed_by`
  ADD CONSTRAINT `developed_by_ibfk_1` FOREIGN KEY (`Title_name`) REFERENCES `title` (`Name`),
  ADD CONSTRAINT `developed_by_ibfk_2` FOREIGN KEY (`Developer_name`) REFERENCES `developer` (`Name`);

--
-- Constraints for table `developer`
--
ALTER TABLE `developer`
  ADD CONSTRAINT `developer_ibfk_1` FOREIGN KEY (`Name`) REFERENCES `company` (`NAME`);

--
-- Constraints for table `generates_collection_report`
--
ALTER TABLE `generates_collection_report`
  ADD CONSTRAINT `generates_collection_report_ibfk_1` FOREIGN KEY (`Sub_collection_name`) REFERENCES `sub_collection` (`Name`),
  ADD CONSTRAINT `generates_collection_report_ibfk_2` FOREIGN KEY (`Super_collection_name`) REFERENCES `super_collection` (`Name`);

--
-- Constraints for table `generates_sys_report`
--
ALTER TABLE `generates_sys_report`
  ADD CONSTRAINT `generates_sys_report_ibfk_1` FOREIGN KEY (`Admin_username`) REFERENCES `admin` (`Username`),
  ADD CONSTRAINT `generates_sys_report_ibfk_2` FOREIGN KEY (`Report_timestamp`) REFERENCES `system_report` (`Timestamp`),
  ADD CONSTRAINT `generates_sys_report_ibfk_3` FOREIGN KEY (`Super_collection_name`) REFERENCES `super_collection` (`Name`);

--
-- Constraints for table `in_collection`
--
ALTER TABLE `in_collection`
  ADD CONSTRAINT `in_collection_ibfk_1` FOREIGN KEY (`Item_ID`) REFERENCES `item` (`Item_ID`) ON DELETE CASCADE ON UPDATE RESTRICT,
  ADD CONSTRAINT `in_collection_ibfk_2` FOREIGN KEY (`Collection_name`) REFERENCES `sub_collection` (`Name`);

--
-- Constraints for table `item`
--
ALTER TABLE `item`
  ADD CONSTRAINT `item_ibfk_1` FOREIGN KEY (`Wishlist_name`) REFERENCES `wishlist` (`Owner_username`) ON DELETE SET NULL ON UPDATE RESTRICT;

--
-- Constraints for table `misc_peripheral`
--
ALTER TABLE `misc_peripheral`
  ADD CONSTRAINT `misc_peripheral_ibfk_1` FOREIGN KEY (`Item_ID`) REFERENCES `item` (`Item_ID`) ON DELETE CASCADE ON UPDATE RESTRICT;

--
-- Constraints for table `offers`
--
ALTER TABLE `offers`
  ADD CONSTRAINT `offers_ibfk_1` FOREIGN KEY (`Platform_name`) REFERENCES `platform` (`Name`),
  ADD CONSTRAINT `offers_ibfk_2` FOREIGN KEY (`Subscription_name`) REFERENCES `subscription` (`Name`);

--
-- Constraints for table `pc`
--
ALTER TABLE `pc`
  ADD CONSTRAINT `pc_ibfk_1` FOREIGN KEY (`Console_name`,`Console_serial_no`) REFERENCES `console` (`Name`, `Serial_no`);

--
-- Constraints for table `platform`
--
ALTER TABLE `platform`
  ADD CONSTRAINT `platform_ibfk_1` FOREIGN KEY (`Company_name`) REFERENCES `company` (`NAME`);

--
-- Constraints for table `plays_on`
--
ALTER TABLE `plays_on`
  ADD CONSTRAINT `plays_on_ibfk_1` FOREIGN KEY (`Title_name`) REFERENCES `title` (`Name`),
  ADD CONSTRAINT `plays_on_ibfk_2` FOREIGN KEY (`Platform_name`) REFERENCES `platform` (`Name`);

--
-- Constraints for table `published_by`
--
ALTER TABLE `published_by`
  ADD CONSTRAINT `published_by_ibfk_1` FOREIGN KEY (`Title_name`) REFERENCES `title` (`Name`),
  ADD CONSTRAINT `published_by_ibfk_2` FOREIGN KEY (`Publisher_name`) REFERENCES `publisher` (`Name`);

--
-- Constraints for table `publisher`
--
ALTER TABLE `publisher`
  ADD CONSTRAINT `publisher_ibfk_1` FOREIGN KEY (`Name`) REFERENCES `company` (`NAME`);

--
-- Constraints for table `report`
--
ALTER TABLE `report`
  ADD CONSTRAINT `report_ibfk_1` FOREIGN KEY (`Sub_collection_name`) REFERENCES `sub_collection` (`Name`),
  ADD CONSTRAINT `report_ibfk_2` FOREIGN KEY (`Super_collection_name`) REFERENCES `super_collection` (`Name`);

--
-- Constraints for table `storage_device`
--
ALTER TABLE `storage_device`
  ADD CONSTRAINT `storage_device_ibfk_1` FOREIGN KEY (`Item_ID`) REFERENCES `item` (`Item_ID`) ON DELETE CASCADE ON UPDATE RESTRICT;

--
-- Constraints for table `subscription`
--
ALTER TABLE `subscription`
  ADD CONSTRAINT `subscription_ibfk_1` FOREIGN KEY (`Item_ID`) REFERENCES `item` (`Item_ID`) ON DELETE CASCADE ON UPDATE RESTRICT;

--
-- Constraints for table `sub_collection`
--
ALTER TABLE `sub_collection`
  ADD CONSTRAINT `sub_collection_ibfk_1` FOREIGN KEY (`Super_collection_name`) REFERENCES `super_collection` (`Name`);

--
-- Constraints for table `super_collection`
--
ALTER TABLE `super_collection`
  ADD CONSTRAINT `super_collection_ibfk_1` FOREIGN KEY (`Owner_username`) REFERENCES `client` (`Username`);

--
-- Constraints for table `supports`
--
ALTER TABLE `supports`
  ADD CONSTRAINT `supports_ibfk_1` FOREIGN KEY (`Platform_name`) REFERENCES `platform` (`Name`),
  ADD CONSTRAINT `supports_ibfk_2` FOREIGN KEY (`Console_name`) REFERENCES `console` (`Name`);

--
-- Constraints for table `title`
--
ALTER TABLE `title`
  ADD CONSTRAINT `title_ibfk_1` FOREIGN KEY (`Item_ID`) REFERENCES `item` (`Item_ID`) ON DELETE CASCADE ON UPDATE RESTRICT;

--
-- Constraints for table `title_genre`
--
ALTER TABLE `title_genre`
  ADD CONSTRAINT `title_genre_ibfk_1` FOREIGN KEY (`Title_name`) REFERENCES `title` (`Name`);

--
-- Constraints for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD CONSTRAINT `wishlist_ibfk_1` FOREIGN KEY (`Owner_username`) REFERENCES `client` (`Username`);
COMMIT;
