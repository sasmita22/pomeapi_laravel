-- phpMyAdmin SQL Dump
-- version 4.7.7
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 23, 2018 at 11:38 AM
-- Server version: 10.1.30-MariaDB
-- PHP Version: 7.2.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pome`
--

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE `tasks` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deskripsi` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` int(11) NOT NULL,
  `type` int(10) UNSIGNED NOT NULL,
  `project_structure` int(10) UNSIGNED NOT NULL,
  `deadline_at` date DEFAULT NULL,
  `finished_at` date DEFAULT NULL,
  `handled_by` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tasks`
--

INSERT INTO `tasks` (`id`, `name`, `deskripsi`, `status`, `type`, `project_structure`, `deadline_at`, `finished_at`, `handled_by`) VALUES
(1, 'Membuat ERD', 'Task Membuar ERD. Lorem ipsum dolor sit amet, consectetur adipiscing elit. ', 0, 1, 1, '2018-12-27', NULL, '1'),
(2, 'Pembuatan Server', 'Task Pembuatan Server. Lorem ipsum dolor sit amet, consectetur adipiscing elit.', 0, 1, 1, '2018-12-27', NULL, '1'),
(3, 'Mockup Interface', 'Task Mockup Interface. Lorem ipsum dolor sit amet, consectetur adipiscing elit.', 0, 2, 1, '2018-12-29', NULL, '3'),
(4, 'Pembuatan FrontEnd', 'Task Pembuatan FrontEnd. Lorem ipsum dolor sit amet, consectetur adipiscing elit.', 0, 2, 2, '2018-12-21', NULL, '3'),
(5, 'Membuat Database', 'Task Membuat Database. Lorem ipsum dolor sit amet, consectetur adipiscing elit.', 0, 3, 2, '2018-12-21', NULL, '4'),
(6, 'Mengisi Database', 'Task Mengisi Database. Lorem ipsum dolor sit amet, consectetur adipiscing elit.', 0, 3, 3, '2018-12-25', NULL, '4'),
(7, 'Testing', 'Task Testing. Lorem ipsum dolor sit amet, consectetur adipiscing elit.', 0, 4, 3, '2018-12-20', NULL, '4'),
(8, 'Debugging', 'Task Debugging. Lorem ipsum dolor sit amet, consectetur adipiscing elit.', 0, 4, 4, '2018-12-28', NULL, '6');

--
-- Triggers `tasks`
--
DELIMITER $$
CREATE TRIGGER `update_project_structures` AFTER UPDATE ON `tasks` FOR EACH ROW BEGIN
                SET @id = new.project_structure;
               
                set @beres = (SELECT COUNT(IF(status=0,1,null)) = 0
                from tasks
                where project_structure = @id
                group by project_structure);       
                
                IF @beres = 1 then
                    BEGIN
                        update project_structures
                        set status = 1
                        where id = @id;            
                    END;
                ELSE
                    BEGIN
                        update project_structures
                        set status = 0
                        where id = @id;               
                    END;
                END IF;
            END
$$
DELIMITER ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tasks_type_foreign` (`type`),
  ADD KEY `tasks_handled_by_foreign` (`handled_by`),
  ADD KEY `tasks_project_structure_foreign` (`project_structure`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `tasks_handled_by_foreign` FOREIGN KEY (`handled_by`) REFERENCES `staff` (`nip`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tasks_project_structure_foreign` FOREIGN KEY (`project_structure`) REFERENCES `project_structures` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tasks_type_foreign` FOREIGN KEY (`type`) REFERENCES `types` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
