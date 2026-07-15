-- ScholarLink Automated Daily Backup
-- Date Generated: 2026-07-15 02:11:33

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `application`;
CREATE TABLE `application` (
  `ApplicationID` int(11) NOT NULL AUTO_INCREMENT,
  `ScholarshipID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `DateSubmitted` timestamp NOT NULL DEFAULT current_timestamp(),
  `Status` varchar(50) DEFAULT 'Pending',
  `TotalScore` int(11) DEFAULT 0,
  `GPA` decimal(4,2) DEFAULT NULL,
  `YearLevel` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`ApplicationID`),
  KEY `ScholarshipID` (`ScholarshipID`),
  KEY `UserID` (`UserID`),
  CONSTRAINT `application_ibfk_1` FOREIGN KEY (`ScholarshipID`) REFERENCES `scholarship` (`ScholarshipID`) ON DELETE CASCADE,
  CONSTRAINT `application_ibfk_2` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `application` (`ApplicationID`, `ScholarshipID`, `UserID`, `DateSubmitted`, `Status`, `TotalScore`, `GPA`, `YearLevel`) VALUES ('1', '1', '6', '2026-07-14 16:55:23', 'Submitted', '0', '1.45', NULL);
INSERT INTO `application` (`ApplicationID`, `ScholarshipID`, `UserID`, `DateSubmitted`, `Status`, `TotalScore`, `GPA`, `YearLevel`) VALUES ('2', '19', '6', '2026-07-14 22:09:52', 'Submitted', '0', '1.45', NULL);
INSERT INTO `application` (`ApplicationID`, `ScholarshipID`, `UserID`, `DateSubmitted`, `Status`, `TotalScore`, `GPA`, `YearLevel`) VALUES ('3', '5', '6', '2026-07-14 22:24:39', 'Submitted', '0', '1.45', NULL);

DROP TABLE IF EXISTS `application_custom_answers`;
CREATE TABLE `application_custom_answers` (
  `AnswerID` int(11) NOT NULL AUTO_INCREMENT,
  `ApplicationID` int(11) NOT NULL,
  `FieldID` int(11) NOT NULL,
  `AnswerText` text NOT NULL,
  PRIMARY KEY (`AnswerID`),
  KEY `ApplicationID` (`ApplicationID`),
  KEY `FieldID` (`FieldID`),
  CONSTRAINT `fk_aca_application` FOREIGN KEY (`ApplicationID`) REFERENCES `application` (`ApplicationID`) ON DELETE CASCADE,
  CONSTRAINT `fk_aca_field` FOREIGN KEY (`FieldID`) REFERENCES `scholarship_custom_fields` (`FieldID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `audit_log`;
CREATE TABLE `audit_log` (
  `AuditID` int(11) NOT NULL AUTO_INCREMENT,
  `UserID` int(11) DEFAULT NULL,
  `ActionPerformed` varchar(255) DEFAULT NULL,
  `ActionDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `Description` text DEFAULT NULL,
  `IPAddress` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`AuditID`)
) ENGINE=InnoDB AUTO_INCREMENT=155 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('1', '3', 'Document Verified', '2026-03-21 22:16:01', 'Evaluator marked Document #4 as Verified', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('2', '2', 'Application Approved', '2026-03-22 21:11:39', 'Internal Admin marked Application #6 as Approved', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('3', '1', 'Auto Logout', '2026-03-22 23:00:19', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('4', '3', 'Auto Logout', '2026-03-23 00:10:03', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('5', '3', 'Auto Logout', '2026-03-23 01:13:30', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('6', '6', 'Auto Logout', '2026-03-23 10:12:04', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('7', '3', 'Document Verified', '2026-03-23 10:35:46', 'Evaluator marked Document #5 as Verified', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('8', '6', 'Auto Logout', '2026-03-23 13:24:51', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('9', '6', 'Auto Logout', '2026-03-23 13:35:31', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('10', '3', 'Document Verified', '2026-03-23 14:07:12', 'Evaluator marked Document #10 as Verified', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('11', '3', 'Document Verified', '2026-03-23 14:07:18', 'Evaluator marked Document #11 as Verified', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('12', '3', 'Document Verified', '2026-03-23 14:17:29', 'Evaluator marked Document #12 as Verified', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('13', '3', 'Document Verified', '2026-03-23 14:17:31', 'Evaluator marked Document #13 as Verified', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('14', '3', 'Auto Logout', '2026-03-23 14:40:17', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('15', '1', 'Security Update', '2026-03-24 01:06:41', 'Super Admin changed Session Timeout to 300 seconds', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('16', '1', 'Security Update', '2026-03-24 01:12:58', 'Super Admin changed Strict Password to OFF', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('17', '1', 'Security Update', '2026-03-24 01:13:01', 'Super Admin changed Strict Password to ON', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('18', '2', 'Auto Logout', '2026-03-24 01:40:52', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('19', '3', 'Auto Logout', '2026-03-24 01:59:49', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('20', '6', 'Auto Logout', '2026-04-23 21:50:20', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('21', '6', 'Auto Logout', '2026-04-23 21:59:11', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('22', '6', 'Auto Logout', '2026-04-23 22:13:42', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('23', '3', 'Auto Logout', '2026-04-23 22:31:09', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('24', '3', 'Auto Logout', '2026-04-23 22:49:29', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('25', '6', 'Auto Logout', '2026-04-23 23:48:20', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('26', '2', 'Auto Logout', '2026-04-24 00:49:59', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('27', '3', 'Auto Logout', '2026-04-24 01:07:01', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('28', '6', 'Auto Logout', '2026-04-24 01:35:18', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('29', '6', 'Auto Logout', '2026-04-24 01:56:58', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('30', '6', 'Auto Logout', '2026-04-24 02:10:38', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('31', '6', 'Auto Logout', '2026-04-24 02:47:47', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('32', '6', 'Auto Logout', '2026-04-24 03:08:25', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('33', '6', 'Auto Logout', '2026-04-24 03:22:10', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('34', '6', 'Auto Logout', '2026-04-24 03:27:56', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('35', '6', 'Auto Logout', '2026-04-24 03:46:21', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('36', '6', 'Auto Logout', '2026-04-24 03:52:31', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('37', '6', 'Password Reset', '2026-04-24 04:40:07', 'User successfully changed their password via reset link.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('38', '6', 'Password Reset', '2026-04-24 04:43:04', 'User successfully changed their password via reset link.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('39', '3', 'Auto Logout', '2026-04-24 12:25:17', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('40', '6', 'Auto Logout', '2026-04-24 12:50:02', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('41', '6', 'Auto Logout', '2026-04-24 15:12:59', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('42', '6', 'Auto Logout', '2026-04-25 19:49:49', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('43', '6', 'Auto Logout', '2026-04-25 19:55:06', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('44', '3', 'Auto Logout', '2026-04-29 21:20:49', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('45', '3', 'Auto Logout', '2026-04-29 21:33:39', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('46', '3', 'Auto Logout', '2026-04-29 21:43:52', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('47', '6', 'Auto Logout', '2026-04-29 22:07:09', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('48', '6', 'Auto Logout', '2026-05-07 13:13:55', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('49', '8', 'Account Created', '2026-05-07 13:18:25', 'A new student account was registered via the portal.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('50', '6', 'Auto Logout', '2026-05-15 19:18:01', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('51', '6', 'Auto Logout', '2026-05-15 19:26:37', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('52', '6', 'Auto Logout', '2026-05-15 19:53:18', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('53', '6', 'Auto Logout', '2026-05-15 20:04:16', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('54', '3', 'Auto Logout', '2026-05-15 23:09:23', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('55', '1', 'Security Update', '2026-05-15 23:25:46', 'Super Admin changed Strict Password to OFF', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('56', '1', 'Security Update', '2026-05-15 23:25:48', 'Super Admin changed Strict Password to ON', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('57', '1', 'Security Update', '2026-05-15 23:25:50', 'Super Admin changed Require 2fa to ON', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('58', '1', 'Security Update', '2026-05-15 23:25:51', 'Super Admin changed Require 2fa to OFF', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('59', '6', 'Auto Logout', '2026-05-15 23:57:27', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('60', '6', 'Auto Logout', '2026-05-16 00:09:52', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('61', '6', 'Auto Logout', '2026-05-16 00:20:08', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('62', '6', 'Auto Logout', '2026-05-16 01:04:56', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('63', '3', 'Auto Logout', '2026-05-17 22:49:59', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('64', '6', 'Auto Logout', '2026-05-17 23:30:53', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('65', '6', 'Auto Logout', '2026-05-17 23:40:50', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('66', '3', 'Document Verified', '2026-05-17 23:49:19', 'Evaluator marked Document #14 as Verified', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('67', '6', 'Auto Logout', '2026-05-18 00:01:33', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('68', '6', 'Auto Logout', '2026-05-18 00:09:48', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('69', '6', 'Auto Logout', '2026-05-18 00:32:54', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('70', '6', 'Auto Logout', '2026-05-18 11:05:55', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('71', '6', 'Auto Logout', '2026-05-18 22:36:53', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('72', '6', 'Auto Logout', '2026-05-18 22:42:42', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('73', '2', 'Auto Logout', '2026-05-18 23:02:03', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('74', '6', 'Auto Logout', '2026-05-18 23:16:09', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('75', '2', 'Auto Logout', '2026-05-18 23:17:49', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('76', '2', 'Auto Logout', '2026-05-18 23:34:12', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('77', '2', 'Auto Logout', '2026-05-18 23:40:11', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('78', '6', 'Auto Logout', '2026-05-19 11:18:40', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('79', '9', 'Account Created', '2026-05-19 11:27:46', 'A new student account was registered via the portal.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('80', '2', 'Auto Logout', '2026-05-19 11:50:20', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('81', '10', 'Account Created', '2026-05-19 17:40:03', 'A new student account was registered via the portal.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('82', '6', 'Auto Logout', '2026-05-19 20:29:14', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('83', '6', 'Auto Logout', '2026-05-19 20:48:11', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('84', '11', 'Account Created', '2026-05-19 20:57:53', 'A new student account was registered via the portal.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('85', '11', 'Auto Logout', '2026-05-19 21:03:41', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('86', '11', 'Auto Logout', '2026-05-19 21:10:34', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('87', '12', 'Account Created', '2026-05-19 21:11:49', 'A new student account was registered via the portal.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('88', '2', 'Auto Logout', '2026-05-19 21:40:58', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('89', '3', 'Auto Logout', '2026-05-19 21:49:16', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('90', '2', 'Auto Logout', '2026-05-19 21:49:20', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('91', '3', 'Document Verified', '2026-05-19 21:53:01', 'Evaluator marked Document #15 as Verified', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('92', '2', 'Application Approved', '2026-05-19 21:53:55', 'Internal Admin marked Application #11 as Approved', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('93', '6', 'Auto Logout', '2026-05-19 22:04:55', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('94', '6', 'Auto Logout', '2026-05-19 22:28:36', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('95', '3', 'Auto Logout', '2026-05-19 22:48:07', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('96', '3', 'Auto Logout', '2026-05-19 23:01:34', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('97', '3', 'Auto Logout', '2026-05-19 23:21:41', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('98', '3', 'Auto Logout', '2026-05-19 23:40:07', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('99', '3', 'Document Verified', '2026-05-20 09:17:50', 'Evaluator marked Document #16 as Verified', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('100', '6', 'Auto Logout', '2026-05-20 09:23:31', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('101', '1', 'Auto Logout', '2026-05-20 09:30:55', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('102', '13', 'Account Created', '2026-05-20 15:16:31', 'A new student account was registered via the portal.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('103', '13', 'Auto Logout', '2026-05-20 15:29:17', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('104', '14', 'Account Created', '2026-05-20 15:33:09', 'A new student account was registered via the portal.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('105', '6', 'Password Reset', '2026-05-20 15:38:43', 'User successfully changed their password via reset link.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('106', '6', 'Auto Logout', '2026-05-20 15:50:56', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('107', '2', 'Auto Logout', '2026-05-20 16:08:01', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('108', '3', 'Auto Logout', '2026-05-20 16:22:03', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('109', '3', 'Auto Logout', '2026-05-20 16:27:17', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('110', '6', 'Password Reset', '2026-06-08 21:27:53', 'User successfully changed their password via reset link.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('111', '6', 'Auto Logout', '2026-06-08 21:44:47', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('112', '6', 'Auto Logout', '2026-06-08 22:01:27', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('113', '6', 'Auto Logout', '2026-06-10 00:35:11', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('114', '2', 'Auto Logout', '2026-06-10 09:50:56', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('115', '2', 'Auto Logout', '2026-06-10 10:16:59', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('116', '2', 'Auto Logout', '2026-06-10 10:33:51', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('117', '2', 'Auto Logout', '2026-06-10 11:17:49', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('118', '2', 'Auto Logout', '2026-06-10 11:44:34', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('119', '2', 'Auto Logout', '2026-06-10 13:03:55', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('120', '3', 'Auto Logout', '2026-06-16 08:51:59', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('121', '6', 'Auto Logout', '2026-06-16 09:47:23', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('122', '6', 'Auto Logout', '2026-06-16 09:58:44', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('123', '6', 'Auto Logout', '2026-06-26 10:00:50', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('124', '3', 'Auto Logout', '2026-06-26 10:16:29', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('125', '1', 'System Restore', '2026-06-29 08:16:41', 'Super Admin restored the database from a backup file.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('126', '1', 'System Backup', '2026-06-29 08:16:56', 'Super Admin exported a full database backup.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('127', '1', 'Auto Logout', '2026-06-29 08:25:23', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('128', '1', 'Auto Logout', '2026-06-29 08:37:28', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('129', '1', 'Auto Logout', '2026-06-29 08:43:42', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('130', '1', 'Auto Logout', '2026-06-29 08:53:38', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('131', '1', 'Auto Logout', '2026-06-29 09:03:21', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('132', '1', 'Auto Logout', '2026-06-29 09:35:02', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('133', '3', 'Auto Logout', '2026-06-29 09:49:41', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('134', '3', 'Auto Logout', '2026-06-29 10:00:03', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('135', '6', 'Auto Logout', '2026-06-29 10:24:24', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('136', '6', 'Auto Logout', '2026-06-29 10:30:47', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('137', '6', 'Auto Logout', '2026-06-29 10:51:27', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('138', '3', 'Auto Logout', '2026-06-29 11:25:25', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('139', '3', 'Auto Logout', '2026-06-29 11:43:17', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('140', '3', 'Auto Logout', '2026-06-29 11:55:41', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('141', '6', 'Auto Logout', '2026-06-29 12:13:40', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('142', '3', 'Auto Logout', '2026-06-29 13:44:26', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('143', '6', 'Auto Logout', '2026-06-29 21:44:25', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('144', '6', 'Auto Logout', '2026-06-29 22:00:54', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('145', '2', 'Auto Logout', '2026-06-29 22:28:44', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('146', '1', 'System Restore', '2026-07-01 08:47:10', 'Super Admin restored the database from a backup file.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('147', '6', 'Password Reset Requested', '2026-07-09 10:07:25', 'User requested a password reset link.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('148', '6', 'Password Reset Requested', '2026-07-09 10:09:05', 'User requested a password reset link.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('149', '16', 'Account Created', '2026-07-09 10:13:23', 'A new student account was registered via the portal.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('150', '6', 'Password Reset Requested', '2026-07-09 11:09:49', 'User requested a password reset link.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('151', '1', 'System Auto-Backup', '2026-07-09 11:10:51', 'Automated daily database backup completed.', 'System');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('152', '1', 'System Auto-Backup', '2026-07-14 15:27:31', 'Automated daily database backup completed.', 'System');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('153', '6', 'Auto Logout', '2026-07-14 16:38:21', 'User session expired due to inactivity.', '::1');
INSERT INTO `audit_log` (`AuditID`, `UserID`, `ActionPerformed`, `ActionDate`, `Description`, `IPAddress`) VALUES ('154', '6', 'Auto Logout', '2026-07-14 22:17:41', 'User session expired due to inactivity.', '::1');

DROP TABLE IF EXISTS `criteria`;
CREATE TABLE `criteria` (
  `CriteriaID` int(11) NOT NULL AUTO_INCREMENT,
  `ScholarshipID` int(11) NOT NULL,
  `CriteriaName` varchar(100) NOT NULL,
  `Weight` decimal(5,2) NOT NULL,
  PRIMARY KEY (`CriteriaID`),
  KEY `ScholarshipID` (`ScholarshipID`),
  CONSTRAINT `criteria_ibfk_1` FOREIGN KEY (`ScholarshipID`) REFERENCES `scholarship` (`ScholarshipID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `document_requirement`;
CREATE TABLE `document_requirement` (
  `RequirementID` int(11) NOT NULL AUTO_INCREMENT,
  `ScholarshipID` int(11) NOT NULL,
  `DocumentName` varchar(100) NOT NULL,
  PRIMARY KEY (`RequirementID`),
  KEY `ScholarshipID` (`ScholarshipID`),
  CONSTRAINT `document_requirement_ibfk_1` FOREIGN KEY (`ScholarshipID`) REFERENCES `scholarship` (`ScholarshipID`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=115 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('1', '1', 'Certificate of Registration (COR)');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('2', '1', 'Report of Grades');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('3', '2', 'Certificate of Registration (COR)');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('4', '2', 'Report of Grades');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('5', '3', 'Certificate of Registration (COR)');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('6', '3', 'Report of Grades');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('7', '3', 'Endorsement from Sports Development Director');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('8', '4', 'Certificate of Registration (COR)');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('9', '4', 'Report of Grades');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('10', '4', 'Endorsement from Socio-Cultural Director');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('11', '5', 'Certificate of Registration (COR)');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('12', '5', 'Report of Grades');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('13', '5', 'Endorsement from Publication Adviser');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('14', '6', 'Certificate of Registration (COR)');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('15', '6', 'Report of Grades');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('16', '6', 'ROTC Commandant Endorsement');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('17', '7', 'Certificate of Registration (COR)');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('18', '7', 'Report of Grades');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('19', '7', 'Endorsement from Adviser');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('20', '8', 'Certificate of Registration (COR)');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('21', '8', 'Report of Grades');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('22', '8', 'Barangay Certificate of Indigency');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('23', '9', 'Certificate of Registration (COR)');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('24', '9', 'Report of Grades');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('25', '9', 'Certificate of Indigency');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('26', '9', 'Certificate of Good Moral Character');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('27', '10', 'Certificate of Registration (COR)');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('28', '10', 'Report of Grades');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('29', '10', 'BIR Income Tax Return or Tax Exemption');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('30', '11', 'Certificate of Registration (COR)');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('31', '11', 'Report of Grades');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('32', '11', 'Certificate of Good Moral Character');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('33', '12', 'Certificate of Registration (COR)');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('34', '12', 'Report of Grades');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('35', '12', 'Barangay Certificate of Indigency');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('36', '13', 'Certificate of Registration (COR)');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('37', '13', 'Report of Grades');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('38', '13', 'Barangay Certificate of Indigency');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('39', '14', 'Certificate of Registration (COR)');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('40', '14', 'Report of Grades');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('41', '14', 'Barangay Certificate of Indigency');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('42', '15', 'Certificate of Registration (COR)');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('43', '15', 'Report of Grades');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('44', '15', 'Barangay Certificate of Indigency');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('45', '16', 'Certificate of Registration (COR)');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('46', '16', 'Report of Grades');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('47', '16', 'Barangay Certificate of Residency');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('48', '16', 'DSWD Certificate of Indigency');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('49', '16', 'Certificate of Good Moral Character');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('50', '16', 'Recommendation Letter 1');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('51', '17', 'Certificate of Registration (COR)');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('52', '17', 'Report of Grades');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('53', '17', 'DSWD Certificate of Indigency');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('54', '17', 'Certificate of Good Moral Character');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('55', '17', 'Recommendation Letter 1');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('56', '18', 'Certificate of Registration (COR)');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('57', '18', 'Report of Grades');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('58', '18', 'Barangay Certificate of Residency');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('59', '18', 'DSWD Certificate of Indigency');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('60', '19', 'Certificate of Registration (COR)');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('61', '19', 'Report of Grades');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('62', '19', 'Barangay Certificate of Indigency');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('63', '20', 'Certificate of Registration (COR)');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('64', '20', 'Report of Grades');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('65', '20', 'Barangay Certificate of Residency');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('66', '20', 'DSWD Certificate of Indigency');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('67', '20', 'Certificate of Good Moral Character');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('68', '20', 'Recommendation Letter 1');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('69', '21', 'Certificate of Registration (COR)');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('70', '21', 'Report of Grades');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('71', '21', 'Certificate of Good Moral Character');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('72', '21', 'Recommendation Letter 1');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('73', '21', 'Medical Certificate (Fit, X-ray, Urinalysis, Fecalysis, Eye Exam)');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('74', '22', 'Certificate of Registration (COR)');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('75', '22', 'Report of Grades');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('76', '22', 'Barangay Certificate of Residency');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('77', '22', 'DSWD Certificate of Indigency');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('78', '22', 'Certificate of Good Moral Character');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('79', '22', 'Recommendation Letter 1');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('80', '23', 'Certificate of Registration (COR)');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('81', '23', 'Report of Grades');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('82', '23', 'IP Certification/ID');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('83', '23', 'Barangay Certificate of Residency');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('84', '23', 'DSWD Certificate of Indigency');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('85', '23', 'Certificate of Good Moral Character');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('86', '23', 'Recommendation Letter 1');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('87', '24', 'Certificate of Registration (COR)');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('88', '24', 'Report of Grades');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('89', '24', 'Barangay Certificate of Residency');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('90', '24', 'DSWD Certificate of Indigency');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('91', '24', 'Certificate of Good Moral Character');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('92', '24', 'Recommendation Letter 1');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('93', '25', 'Certificate of Registration (COR)');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('94', '25', 'Report of Grades');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('95', '25', 'Barangay Certificate of Indigency (Mayantoc)');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('96', '25', 'MSWDO Certificate of Indigency');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('97', '25', 'BIR Certificate of Tax Exemption');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('98', '26', 'Certificate of Registration (COR)');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('99', '26', 'Report of Grades');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('100', '26', 'Income Tax Return (ITR)');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('101', '27', 'Certificate of Registration (COR)');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('102', '27', 'Report of Grades');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('103', '27', 'Barangay Certificate of Residency');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('104', '27', 'DSWD Certificate of Indigency');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('105', '27', 'Certificate of Good Moral Character');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('106', '27', 'Recommendation Letter 1');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('107', '16', 'Recommendation Letter 2');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('108', '17', 'Recommendation Letter 2');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('109', '20', 'Recommendation Letter 2');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('110', '21', 'Recommendation Letter 2');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('111', '22', 'Recommendation Letter 2');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('112', '23', 'Recommendation Letter 2');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('113', '24', 'Recommendation Letter 2');
INSERT INTO `document_requirement` (`RequirementID`, `ScholarshipID`, `DocumentName`) VALUES ('114', '27', 'Recommendation Letter 2');

DROP TABLE IF EXISTS `landing_content`;
CREATE TABLE `landing_content` (
  `section_key` varchar(50) NOT NULL,
  `title` varchar(255) NOT NULL,
  `body` text NOT NULL,
  PRIMARY KEY (`section_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `landing_content` (`section_key`, `title`, `body`) VALUES ('grants_header', 'Active Scholarships', 'Currently accepting applications for this semester.');
INSERT INTO `landing_content` (`section_key`, `title`, `body`) VALUES ('hero', 'Unlock your future with ScholarLink.', 'Discover financial assistance programs, track your applications, and focus on your education. Browse the available TAU grants below to get started.');
INSERT INTO `landing_content` (`section_key`, `title`, `body`) VALUES ('no_grants', 'No active scholarships', 'There are currently no scholarship programs accepting applications. Please check back later.');

DROP TABLE IF EXISTS `messages`;
CREATE TABLE `messages` (
  `MessageID` int(11) NOT NULL AUTO_INCREMENT,
  `SenderID` int(11) NOT NULL,
  `ReceiverID` int(11) NOT NULL,
  `MessageText` text NOT NULL,
  `IsRead` tinyint(1) DEFAULT 0,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`MessageID`),
  KEY `SenderID` (`SenderID`),
  KEY `ReceiverID` (`ReceiverID`),
  CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`SenderID`) REFERENCES `users` (`UserID`) ON DELETE CASCADE,
  CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`ReceiverID`) REFERENCES `users` (`UserID`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `messages` (`MessageID`, `SenderID`, `ReceiverID`, `MessageText`, `IsRead`, `CreatedAt`) VALUES ('1', '6', '2', 'hello', '1', '2026-05-18 22:56:27');
INSERT INTO `messages` (`MessageID`, `SenderID`, `ReceiverID`, `MessageText`, `IsRead`, `CreatedAt`) VALUES ('2', '2', '6', 'hi', '1', '2026-05-18 22:56:34');
INSERT INTO `messages` (`MessageID`, `SenderID`, `ReceiverID`, `MessageText`, `IsRead`, `CreatedAt`) VALUES ('3', '6', '2', 'can i ask a question po?', '1', '2026-05-18 22:59:00');
INSERT INTO `messages` (`MessageID`, `SenderID`, `ReceiverID`, `MessageText`, `IsRead`, `CreatedAt`) VALUES ('4', '2', '6', 'ano po yon?', '1', '2026-05-18 22:59:09');
INSERT INTO `messages` (`MessageID`, `SenderID`, `ReceiverID`, `MessageText`, `IsRead`, `CreatedAt`) VALUES ('5', '6', '2', 'about po sana sa scholarship', '1', '2026-05-18 23:09:11');
INSERT INTO `messages` (`MessageID`, `SenderID`, `ReceiverID`, `MessageText`, `IsRead`, `CreatedAt`) VALUES ('6', '2', '6', 'anong scholarship po/', '1', '2026-05-18 23:23:39');
INSERT INTO `messages` (`MessageID`, `SenderID`, `ReceiverID`, `MessageText`, `IsRead`, `CreatedAt`) VALUES ('7', '6', '2', 'sa cyber sentinel po', '1', '2026-05-18 23:34:59');
INSERT INTO `messages` (`MessageID`, `SenderID`, `ReceiverID`, `MessageText`, `IsRead`, `CreatedAt`) VALUES ('8', '6', '2', 'ask ko lang po sana', '1', '2026-05-18 23:35:14');
INSERT INTO `messages` (`MessageID`, `SenderID`, `ReceiverID`, `MessageText`, `IsRead`, `CreatedAt`) VALUES ('9', '2', '6', 'ano pp yon', '1', '2026-05-18 23:40:07');
INSERT INTO `messages` (`MessageID`, `SenderID`, `ReceiverID`, `MessageText`, `IsRead`, `CreatedAt`) VALUES ('10', '6', '2', 'ano po requirements', '1', '2026-05-19 11:14:10');
INSERT INTO `messages` (`MessageID`, `SenderID`, `ReceiverID`, `MessageText`, `IsRead`, `CreatedAt`) VALUES ('11', '2', '6', 'nasa applications na po', '1', '2026-05-19 11:15:52');
INSERT INTO `messages` (`MessageID`, `SenderID`, `ReceiverID`, `MessageText`, `IsRead`, `CreatedAt`) VALUES ('12', '6', '2', '⚠️ AI Escalate: I couldn\'t get an answer for: \"can I talk to the admin\"', '1', '2026-06-08 21:29:47');
INSERT INTO `messages` (`MessageID`, `SenderID`, `ReceiverID`, `MessageText`, `IsRead`, `CreatedAt`) VALUES ('13', '6', '2', '⚠️ AI Escalate: I couldn\'t get an answer for: \"1+1\"', '1', '2026-06-08 21:49:27');
INSERT INTO `messages` (`MessageID`, `SenderID`, `ReceiverID`, `MessageText`, `IsRead`, `CreatedAt`) VALUES ('14', '6', '2', '⚠️ AI Escalate: I couldn\'t get an answer for: \"delete convo\"', '1', '2026-06-16 09:22:11');
INSERT INTO `messages` (`MessageID`, `SenderID`, `ReceiverID`, `MessageText`, `IsRead`, `CreatedAt`) VALUES ('15', '6', '2', '⚠️ AI Escalate: I couldn\'t get an answer for: \"delete chat\"', '1', '2026-06-16 09:30:16');

DROP TABLE IF EXISTS `notifications`;
CREATE TABLE `notifications` (
  `NotificationID` int(11) NOT NULL AUTO_INCREMENT,
  `UserID` int(11) NOT NULL,
  `Title` varchar(255) NOT NULL,
  `Message` text NOT NULL,
  `Type` varchar(50) DEFAULT 'info',
  `IsRead` tinyint(1) DEFAULT 0,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`NotificationID`),
  KEY `UserID` (`UserID`),
  CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `notifications` (`NotificationID`, `UserID`, `Title`, `Message`, `Type`, `IsRead`, `CreatedAt`) VALUES ('1', '6', 'Application Evaluated! ✍️', 'Your application for the Cyber Sentinel Award has been officially scored and Shortlisted by an evaluator.', 'info', '1', '2026-05-17 23:50:38');
INSERT INTO `notifications` (`NotificationID`, `UserID`, `Title`, `Message`, `Type`, `IsRead`, `CreatedAt`) VALUES ('2', '6', 'Application Approved! 🏆', 'Congratulations! Your application for the Cyber Sentinel Award has been officially Approved.', 'success', '1', '2026-05-19 21:53:55');

DROP TABLE IF EXISTS `program`;
CREATE TABLE `program` (
  `ProgramID` int(11) NOT NULL AUTO_INCREMENT,
  `ProgramName` varchar(150) NOT NULL,
  PRIMARY KEY (`ProgramID`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `program` (`ProgramID`, `ProgramName`) VALUES ('1', 'BS Agriculture');
INSERT INTO `program` (`ProgramID`, `ProgramName`) VALUES ('2', 'Bachelor of Animal Science (BAS)');
INSERT INTO `program` (`ProgramID`, `ProgramName`) VALUES ('3', 'BS Forestry');
INSERT INTO `program` (`ProgramID`, `ProgramName`) VALUES ('4', 'BS Food Technology');
INSERT INTO `program` (`ProgramID`, `ProgramName`) VALUES ('5', 'AB Economics');
INSERT INTO `program` (`ProgramID`, `ProgramName`) VALUES ('6', 'BS Psychology');
INSERT INTO `program` (`ProgramID`, `ProgramName`) VALUES ('7', 'BS Development Communication');
INSERT INTO `program` (`ProgramID`, `ProgramName`) VALUES ('8', 'BS Business Administration');
INSERT INTO `program` (`ProgramID`, `ProgramName`) VALUES ('9', 'BS Entrepreneurship');
INSERT INTO `program` (`ProgramID`, `ProgramName`) VALUES ('10', 'BS Agribusiness');
INSERT INTO `program` (`ProgramID`, `ProgramName`) VALUES ('11', 'BS Tourism Management');
INSERT INTO `program` (`ProgramID`, `ProgramName`) VALUES ('12', 'Bachelor of Elementary Education (BEEd)');
INSERT INTO `program` (`ProgramID`, `ProgramName`) VALUES ('13', 'Bachelor of Secondary Education (BSEd)');
INSERT INTO `program` (`ProgramID`, `ProgramName`) VALUES ('14', 'Bachelor of Early Childhood Education (BECEd)');
INSERT INTO `program` (`ProgramID`, `ProgramName`) VALUES ('15', 'Bachelor of Technology and Livelihood Education (BTLEd)');
INSERT INTO `program` (`ProgramID`, `ProgramName`) VALUES ('16', 'BS Agricultural and Biosystems Engineering');
INSERT INTO `program` (`ProgramID`, `ProgramName`) VALUES ('17', 'BS Geodetic Engineering');
INSERT INTO `program` (`ProgramID`, `ProgramName`) VALUES ('18', 'BS Information Technology (BSIT)');
INSERT INTO `program` (`ProgramID`, `ProgramName`) VALUES ('19', 'Doctor of Veterinary Medicine (DVM)');

DROP TABLE IF EXISTS `scholarship`;
CREATE TABLE `scholarship` (
  `ScholarshipID` int(11) NOT NULL AUTO_INCREMENT,
  `ProgramID` int(11) DEFAULT NULL,
  `YearLevel` varchar(50) DEFAULT NULL,
  `Name` varchar(150) NOT NULL,
  `Description` text DEFAULT NULL,
  `Deadline` date NOT NULL,
  `AwardAmount` decimal(10,2) DEFAULT NULL,
  `NumberOfSlots` int(11) DEFAULT NULL,
  `Status` varchar(20) DEFAULT 'Active',
  `MinimumGWA` decimal(4,2) DEFAULT 2.00,
  `CreatedBy` int(11) DEFAULT NULL,
  `GenderRequirement` varchar(20) NOT NULL DEFAULT 'Any',
  `ScholarshipType` enum('Private','Government') DEFAULT 'Private',
  `FormConfig` varchar(255) DEFAULT 'Academic,Family,Financial,Essay',
  `ReleaseFrequency` varchar(50) DEFAULT 'Per Semester',
  `AllowsDual` enum('Yes','No') NOT NULL DEFAULT 'No',
  PRIMARY KEY (`ScholarshipID`),
  KEY `ProgramID` (`ProgramID`),
  KEY `fk_scholarship_creator` (`CreatedBy`),
  CONSTRAINT `fk_scholarship_creator` FOREIGN KEY (`CreatedBy`) REFERENCES `users` (`UserID`) ON DELETE SET NULL,
  CONSTRAINT `scholarship_ibfk_1` FOREIGN KEY (`ProgramID`) REFERENCES `program` (`ProgramID`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `scholarship` (`ScholarshipID`, `ProgramID`, `YearLevel`, `Name`, `Description`, `Deadline`, `AwardAmount`, `NumberOfSlots`, `Status`, `MinimumGWA`, `CreatedBy`, `GenderRequirement`, `ScholarshipType`, `FormConfig`, `ReleaseFrequency`, `AllowsDual`) VALUES ('1', NULL, NULL, 'Academic Scholars - Full', 'GWA of 1.20-1.00 of an academic load for regular students with no dropped, conditional, or failed grades.', '2026-08-30', '5000.00', NULL, 'Active', '1.20', '2', 'Any', 'Government', 'Academic,Family,Financial,Essay', 'Per Semester', 'No');
INSERT INTO `scholarship` (`ScholarshipID`, `ProgramID`, `YearLevel`, `Name`, `Description`, `Deadline`, `AwardAmount`, `NumberOfSlots`, `Status`, `MinimumGWA`, `CreatedBy`, `GenderRequirement`, `ScholarshipType`, `FormConfig`, `ReleaseFrequency`, `AllowsDual`) VALUES ('2', NULL, NULL, 'Academic Scholars - Partial', 'GWA of 1.75-1.21. Must be regularly enrolled with a minimum of 12 units and no dropped, conditional, or failed grades.', '2026-08-30', '1500.00', NULL, 'Active', '1.75', '2', 'Any', 'Government', 'Academic,Family,Financial,Essay', 'Per Semester', 'No');
INSERT INTO `scholarship` (`ScholarshipID`, `ProgramID`, `YearLevel`, `Name`, `Description`, `Deadline`, `AwardAmount`, `NumberOfSlots`, `Status`, `MinimumGWA`, `CreatedBy`, `GenderRequirement`, `ScholarshipType`, `FormConfig`, `ReleaseFrequency`, `AllowsDual`) VALUES ('3', NULL, NULL, 'Athletes Regular Training Allowance', 'Subject to monitoring, evaluation, and endorsement of the Director for Sports Development.', '2026-08-30', '5000.00', NULL, 'Active', '3.00', '2', 'Any', 'Government', 'Academic,Family,Financial,Essay', 'Per Semester', 'No');
INSERT INTO `scholarship` (`ScholarshipID`, `ProgramID`, `YearLevel`, `Name`, `Description`, `Deadline`, `AwardAmount`, `NumberOfSlots`, `Status`, `MinimumGWA`, `CreatedBy`, `GenderRequirement`, `ScholarshipType`, `FormConfig`, `ReleaseFrequency`, `AllowsDual`) VALUES ('4', NULL, NULL, 'Cultural Performers Allowance', 'Subject to monitoring, evaluation, and endorsement of the Socio-Cultural Development Director.', '2026-08-30', '5000.00', NULL, 'Active', '3.00', '2', 'Any', 'Government', 'Academic,Family,Financial,Essay', 'Per Semester', 'No');
INSERT INTO `scholarship` (`ScholarshipID`, `ProgramID`, `YearLevel`, `Name`, `Description`, `Deadline`, `AwardAmount`, `NumberOfSlots`, `Status`, `MinimumGWA`, `CreatedBy`, `GenderRequirement`, `ScholarshipType`, `FormConfig`, `ReleaseFrequency`, `AllowsDual`) VALUES ('5', NULL, NULL, 'Golden Harvest Incentives', 'Incentives for the Official Student Publication editorial board.', '2026-08-30', '5000.00', NULL, 'Active', '3.00', '2', 'Any', 'Government', 'Academic,Family,Financial,Essay', 'Per Semester', 'No');
INSERT INTO `scholarship` (`ScholarshipID`, `ProgramID`, `YearLevel`, `Name`, `Description`, `Deadline`, `AwardAmount`, `NumberOfSlots`, `Status`, `MinimumGWA`, `CreatedBy`, `GenderRequirement`, `ScholarshipType`, `FormConfig`, `ReleaseFrequency`, `AllowsDual`) VALUES ('6', NULL, NULL, 'ROTC Cadet Allowance', 'Incentives for First Class (P5,000) and Second Class (P2,500) Cadets.', '2026-08-30', '5000.00', NULL, 'Active', '3.00', '2', 'Any', 'Government', 'Academic,Family,Financial,Essay', 'Per Semester', 'No');
INSERT INTO `scholarship` (`ScholarshipID`, `ProgramID`, `YearLevel`, `Name`, `Description`, `Deadline`, `AwardAmount`, `NumberOfSlots`, `Status`, `MinimumGWA`, `CreatedBy`, `GenderRequirement`, `ScholarshipType`, `FormConfig`, `ReleaseFrequency`, `AllowsDual`) VALUES ('7', NULL, NULL, 'SSC/CSC Leadership Incentives', 'For students holding leadership positions indicated in the TAU Code.', '2026-08-30', '5000.00', NULL, 'Active', '3.00', '2', 'Any', 'Government', 'Academic,Family,Financial,Essay', 'Per Semester', 'No');
INSERT INTO `scholarship` (`ScholarshipID`, `ProgramID`, `YearLevel`, `Name`, `Description`, `Deadline`, `AwardAmount`, `NumberOfSlots`, `Status`, `MinimumGWA`, `CreatedBy`, `GenderRequirement`, `ScholarshipType`, `FormConfig`, `ReleaseFrequency`, `AllowsDual`) VALUES ('8', NULL, NULL, 'Tulong Dunong Program', 'One-time SUC program for low-income earning families.', '2026-09-15', '7500.00', NULL, 'Active', '3.00', '2', 'Any', 'Government', 'Academic,Family,Financial,Essay', 'Per Semester', 'No');
INSERT INTO `scholarship` (`ScholarshipID`, `ProgramID`, `YearLevel`, `Name`, `Description`, `Deadline`, `AwardAmount`, `NumberOfSlots`, `Status`, `MinimumGWA`, `CreatedBy`, `GenderRequirement`, `ScholarshipType`, `FormConfig`, `ReleaseFrequency`, `AllowsDual`) VALUES ('9', '1', NULL, 'City of Tarlac Integrated Scholarship (Agriculture)', 'For indigent families enrolled in Agriculture/Agri-related courses.', '2026-09-15', '3500.00', NULL, 'Active', '2.00', '17', 'Any', 'Government', 'Academic,Family,Financial,Essay', 'Per Semester', 'No');
INSERT INTO `scholarship` (`ScholarshipID`, `ProgramID`, `YearLevel`, `Name`, `Description`, `Deadline`, `AwardAmount`, `NumberOfSlots`, `Status`, `MinimumGWA`, `CreatedBy`, `GenderRequirement`, `ScholarshipType`, `FormConfig`, `ReleaseFrequency`, `AllowsDual`) VALUES ('10', NULL, NULL, 'ACEF-GIAHEP Program', 'For agriculture, forestry, fisheries, and veterinary medicine students.', '2026-09-30', '12500.00', NULL, 'Active', '3.00', '19', 'Any', 'Government', 'Academic,Family,Financial,Essay', 'Per Semester', 'No');
INSERT INTO `scholarship` (`ScholarshipID`, `ProgramID`, `YearLevel`, `Name`, `Description`, `Deadline`, `AwardAmount`, `NumberOfSlots`, `Status`, `MinimumGWA`, `CreatedBy`, `GenderRequirement`, `ScholarshipType`, `FormConfig`, `ReleaseFrequency`, `AllowsDual`) VALUES ('11', NULL, NULL, 'DOST Scholarships (RA 7687 / MERIT)', 'For talented students in priority S&T courses.', '2026-09-30', '14000.00', NULL, 'Active', '2.00', '18', 'Any', 'Government', 'Academic,Family,Financial,Essay', 'Per Semester', 'No');
INSERT INTO `scholarship` (`ScholarshipID`, `ProgramID`, `YearLevel`, `Name`, `Description`, `Deadline`, `AwardAmount`, `NumberOfSlots`, `Status`, `MinimumGWA`, `CreatedBy`, `GenderRequirement`, `ScholarshipType`, `FormConfig`, `ReleaseFrequency`, `AllowsDual`) VALUES ('12', NULL, NULL, 'Tertiary Educational Subsidy (TES)', 'For low-income families certified by the Barangay.', '2026-10-15', '20000.00', NULL, 'Active', '3.00', '3', 'Any', 'Government', 'Academic,Family,Financial,Essay', 'Per Semester', 'No');
INSERT INTO `scholarship` (`ScholarshipID`, `ProgramID`, `YearLevel`, `Name`, `Description`, `Deadline`, `AwardAmount`, `NumberOfSlots`, `Status`, `MinimumGWA`, `CreatedBy`, `GenderRequirement`, `ScholarshipType`, `FormConfig`, `ReleaseFrequency`, `AllowsDual`) VALUES ('13', NULL, NULL, 'TES Tulong Dunong Program', 'For low-income families certified by the Barangay.', '2026-10-15', '7500.00', NULL, 'Active', '3.00', '3', 'Any', 'Government', 'Academic,Family,Financial,Essay', 'Per Semester', 'No');
INSERT INTO `scholarship` (`ScholarshipID`, `ProgramID`, `YearLevel`, `Name`, `Description`, `Deadline`, `AwardAmount`, `NumberOfSlots`, `Status`, `MinimumGWA`, `CreatedBy`, `GenderRequirement`, `ScholarshipType`, `FormConfig`, `ReleaseFrequency`, `AllowsDual`) VALUES ('14', NULL, NULL, 'Tulong Agri Program', 'For students in agriculture, fisheries, forestry, food tech, and veterinary medicine.', '2026-10-15', '13500.00', NULL, 'Active', '3.00', '3', 'Any', 'Government', 'Academic,Family,Financial,Essay', 'Per Semester', 'No');
INSERT INTO `scholarship` (`ScholarshipID`, `ProgramID`, `YearLevel`, `Name`, `Description`, `Deadline`, `AwardAmount`, `NumberOfSlots`, `Status`, `MinimumGWA`, `CreatedBy`, `GenderRequirement`, `ScholarshipType`, `FormConfig`, `ReleaseFrequency`, `AllowsDual`) VALUES ('15', NULL, NULL, 'TAU Alumni and Friends Grant', 'For bonafide TAU students from low-income families.', '2026-09-15', '4000.00', NULL, 'Active', '3.00', '24', 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No');
INSERT INTO `scholarship` (`ScholarshipID`, `ProgramID`, `YearLevel`, `Name`, `Description`, `Deadline`, `AwardAmount`, `NumberOfSlots`, `Status`, `MinimumGWA`, `CreatedBy`, `GenderRequirement`, `ScholarshipType`, `FormConfig`, `ReleaseFrequency`, `AllowsDual`) VALUES ('16', NULL, '1st Year', 'Lorna Fernando Dinos Scholarship Program', 'For 1st-year single students (max 25 yrs old) in Tarlac.', '2026-08-30', '9000.00', NULL, 'Active', '2.00', '25', 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No');
INSERT INTO `scholarship` (`ScholarshipID`, `ProgramID`, `YearLevel`, `Name`, `Description`, `Deadline`, `AwardAmount`, `NumberOfSlots`, `Status`, `MinimumGWA`, `CreatedBy`, `GenderRequirement`, `ScholarshipType`, `FormConfig`, `ReleaseFrequency`, `AllowsDual`) VALUES ('17', NULL, '3rd Year', 'Philchema Inc. Scholarship Grant', 'For 3rd-year BS Animal Science, Agriculture, or VetMed students.', '2026-08-30', '10000.00', NULL, 'Active', '2.00', '20', 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No');
INSERT INTO `scholarship` (`ScholarshipID`, `ProgramID`, `YearLevel`, `Name`, `Description`, `Deadline`, `AwardAmount`, `NumberOfSlots`, `Status`, `MinimumGWA`, `CreatedBy`, `GenderRequirement`, `ScholarshipType`, `FormConfig`, `ReleaseFrequency`, `AllowsDual`) VALUES ('18', NULL, NULL, 'Prado Builders Scholarship', 'For residents of Tarlac Province from low-income families.', '2026-09-15', '20000.00', NULL, 'Active', '3.00', '25', 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No');
INSERT INTO `scholarship` (`ScholarshipID`, `ProgramID`, `YearLevel`, `Name`, `Description`, `Deadline`, `AwardAmount`, `NumberOfSlots`, `Status`, `MinimumGWA`, `CreatedBy`, `GenderRequirement`, `ScholarshipType`, `FormConfig`, `ReleaseFrequency`, `AllowsDual`) VALUES ('19', NULL, '4th Year', 'The Camileños Inc. Scholarship Program', 'For graduating students from low-income families.', '2026-08-30', '3000.00', NULL, 'Active', '3.00', '25', 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No');
INSERT INTO `scholarship` (`ScholarshipID`, `ProgramID`, `YearLevel`, `Name`, `Description`, `Deadline`, `AwardAmount`, `NumberOfSlots`, `Status`, `MinimumGWA`, `CreatedBy`, `GenderRequirement`, `ScholarshipType`, `FormConfig`, `ReleaseFrequency`, `AllowsDual`) VALUES ('20', NULL, '1st Year', 'Tolentino - Dahlgren Scholarship Program', 'For 1st-year students from Tarlac.', '2026-08-30', '9000.00', NULL, 'Active', '2.00', '25', 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No');
INSERT INTO `scholarship` (`ScholarshipID`, `ProgramID`, `YearLevel`, `Name`, `Description`, `Deadline`, `AwardAmount`, `NumberOfSlots`, `Status`, `MinimumGWA`, `CreatedBy`, `GenderRequirement`, `ScholarshipType`, `FormConfig`, `ReleaseFrequency`, `AllowsDual`) VALUES ('21', NULL, '2nd Year', 'Bounty Cares Foundation Inc.', 'For 2nd-year Agri, Biosystems, Food Tech, Biology, Chem, and 4th-year VetMed.', '2026-08-25', '37740.00', NULL, 'Active', '2.50', '21', 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No');
INSERT INTO `scholarship` (`ScholarshipID`, `ProgramID`, `YearLevel`, `Name`, `Description`, `Deadline`, `AwardAmount`, `NumberOfSlots`, `Status`, `MinimumGWA`, `CreatedBy`, `GenderRequirement`, `ScholarshipType`, `FormConfig`, `ReleaseFrequency`, `AllowsDual`) VALUES ('22', NULL, '1st Year', 'Cristobal Partido Scholarship Inc.', 'For 1st-year students from Tarlac.', '2026-08-30', '9573.00', NULL, 'Active', '2.00', '25', 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No');
INSERT INTO `scholarship` (`ScholarshipID`, `ProgramID`, `YearLevel`, `Name`, `Description`, `Deadline`, `AwardAmount`, `NumberOfSlots`, `Status`, `MinimumGWA`, `CreatedBy`, `GenderRequirement`, `ScholarshipType`, `FormConfig`, `ReleaseFrequency`, `AllowsDual`) VALUES ('23', NULL, '1st Year', 'Scholarship Program for IP Student', 'For 1st-year Indigenous Peoples (IP) students from Tarlac.', '2026-09-30', '12000.00', NULL, 'Active', '2.00', '25', 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No');
INSERT INTO `scholarship` (`ScholarshipID`, `ProgramID`, `YearLevel`, `Name`, `Description`, `Deadline`, `AwardAmount`, `NumberOfSlots`, `Status`, `MinimumGWA`, `CreatedBy`, `GenderRequirement`, `ScholarshipType`, `FormConfig`, `ReleaseFrequency`, `AllowsDual`) VALUES ('24', NULL, '1st Year', 'CJ and LJ Scholarships', 'For 1st-year students from Tarlac.', '2026-09-15', '5000.00', NULL, 'Active', '2.00', '25', 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No');
INSERT INTO `scholarship` (`ScholarshipID`, `ProgramID`, `YearLevel`, `Name`, `Description`, `Deadline`, `AwardAmount`, `NumberOfSlots`, `Status`, `MinimumGWA`, `CreatedBy`, `GenderRequirement`, `ScholarshipType`, `FormConfig`, `ReleaseFrequency`, `AllowsDual`) VALUES ('25', NULL, '3rd Year', 'Don Francisco Santos Scholarship', 'For regular 3rd-year students. Must have a GPA of 2.5 or higher and no incomplete grades.', '2026-09-30', '6000.00', NULL, 'Active', '2.50', '25', 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No');
INSERT INTO `scholarship` (`ScholarshipID`, `ProgramID`, `YearLevel`, `Name`, `Description`, `Deadline`, `AwardAmount`, `NumberOfSlots`, `Status`, `MinimumGWA`, `CreatedBy`, `GenderRequirement`, `ScholarshipType`, `FormConfig`, `ReleaseFrequency`, `AllowsDual`) VALUES ('26', NULL, '1st Year', 'Ninoy and Cory Aquino Foundation Inc.', 'For 1st-year students. Annual household gross income must not exceed P375,000.00.', '2026-09-15', '15000.00', NULL, 'Active', '2.00', '22', 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No');
INSERT INTO `scholarship` (`ScholarshipID`, `ProgramID`, `YearLevel`, `Name`, `Description`, `Deadline`, `AwardAmount`, `NumberOfSlots`, `Status`, `MinimumGWA`, `CreatedBy`, `GenderRequirement`, `ScholarshipType`, `FormConfig`, `ReleaseFrequency`, `AllowsDual`) VALUES ('27', '17', '1st Year', 'Geodetic Engineers of the Philippines Inc.', 'For 1st-year BS Geodetic Engineering students from Tarlac.', '2026-09-15', '2000.00', NULL, 'Active', '2.00', '23', 'Any', 'Private', 'Academic,Family,Financial,Essay', 'Per Semester', 'No');

DROP TABLE IF EXISTS `scholarship_criteria`;
CREATE TABLE `scholarship_criteria` (
  `CriteriaID` int(11) NOT NULL AUTO_INCREMENT,
  `ScholarshipID` int(11) NOT NULL,
  `CriteriaName` varchar(255) NOT NULL,
  PRIMARY KEY (`CriteriaID`),
  KEY `ScholarshipID` (`ScholarshipID`),
  CONSTRAINT `scholarship_criteria_ibfk_1` FOREIGN KEY (`ScholarshipID`) REFERENCES `scholarship` (`ScholarshipID`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `scholarship_criteria` (`CriteriaID`, `ScholarshipID`, `CriteriaName`) VALUES ('1', '1', 'Essay');
INSERT INTO `scholarship_criteria` (`CriteriaID`, `ScholarshipID`, `CriteriaName`) VALUES ('2', '2', 'Essay');
INSERT INTO `scholarship_criteria` (`CriteriaID`, `ScholarshipID`, `CriteriaName`) VALUES ('3', '3', 'Essay');
INSERT INTO `scholarship_criteria` (`CriteriaID`, `ScholarshipID`, `CriteriaName`) VALUES ('4', '4', 'Essay');
INSERT INTO `scholarship_criteria` (`CriteriaID`, `ScholarshipID`, `CriteriaName`) VALUES ('5', '5', 'Essay');
INSERT INTO `scholarship_criteria` (`CriteriaID`, `ScholarshipID`, `CriteriaName`) VALUES ('6', '6', 'Essay');
INSERT INTO `scholarship_criteria` (`CriteriaID`, `ScholarshipID`, `CriteriaName`) VALUES ('7', '7', 'Essay');
INSERT INTO `scholarship_criteria` (`CriteriaID`, `ScholarshipID`, `CriteriaName`) VALUES ('8', '8', 'Essay');
INSERT INTO `scholarship_criteria` (`CriteriaID`, `ScholarshipID`, `CriteriaName`) VALUES ('9', '9', 'Essay');
INSERT INTO `scholarship_criteria` (`CriteriaID`, `ScholarshipID`, `CriteriaName`) VALUES ('10', '10', 'Essay');
INSERT INTO `scholarship_criteria` (`CriteriaID`, `ScholarshipID`, `CriteriaName`) VALUES ('11', '11', 'Essay');
INSERT INTO `scholarship_criteria` (`CriteriaID`, `ScholarshipID`, `CriteriaName`) VALUES ('12', '12', 'Essay');
INSERT INTO `scholarship_criteria` (`CriteriaID`, `ScholarshipID`, `CriteriaName`) VALUES ('13', '13', 'Essay');
INSERT INTO `scholarship_criteria` (`CriteriaID`, `ScholarshipID`, `CriteriaName`) VALUES ('14', '14', 'Essay');
INSERT INTO `scholarship_criteria` (`CriteriaID`, `ScholarshipID`, `CriteriaName`) VALUES ('15', '15', 'Essay');
INSERT INTO `scholarship_criteria` (`CriteriaID`, `ScholarshipID`, `CriteriaName`) VALUES ('16', '16', 'Essay');
INSERT INTO `scholarship_criteria` (`CriteriaID`, `ScholarshipID`, `CriteriaName`) VALUES ('17', '17', 'Essay');
INSERT INTO `scholarship_criteria` (`CriteriaID`, `ScholarshipID`, `CriteriaName`) VALUES ('18', '18', 'Essay');
INSERT INTO `scholarship_criteria` (`CriteriaID`, `ScholarshipID`, `CriteriaName`) VALUES ('19', '19', 'Essay');
INSERT INTO `scholarship_criteria` (`CriteriaID`, `ScholarshipID`, `CriteriaName`) VALUES ('20', '20', 'Essay');
INSERT INTO `scholarship_criteria` (`CriteriaID`, `ScholarshipID`, `CriteriaName`) VALUES ('21', '21', 'Essay');
INSERT INTO `scholarship_criteria` (`CriteriaID`, `ScholarshipID`, `CriteriaName`) VALUES ('22', '22', 'Essay');
INSERT INTO `scholarship_criteria` (`CriteriaID`, `ScholarshipID`, `CriteriaName`) VALUES ('23', '23', 'Essay');
INSERT INTO `scholarship_criteria` (`CriteriaID`, `ScholarshipID`, `CriteriaName`) VALUES ('24', '24', 'Essay');
INSERT INTO `scholarship_criteria` (`CriteriaID`, `ScholarshipID`, `CriteriaName`) VALUES ('25', '25', 'Essay');
INSERT INTO `scholarship_criteria` (`CriteriaID`, `ScholarshipID`, `CriteriaName`) VALUES ('26', '26', 'Essay');
INSERT INTO `scholarship_criteria` (`CriteriaID`, `ScholarshipID`, `CriteriaName`) VALUES ('27', '27', 'Essay');

DROP TABLE IF EXISTS `scholarship_custom_fields`;
CREATE TABLE `scholarship_custom_fields` (
  `FieldID` int(11) NOT NULL AUTO_INCREMENT,
  `ScholarshipID` int(11) NOT NULL,
  `FieldName` varchar(255) NOT NULL,
  `FieldType` enum('Text','Textarea','Number','Date') DEFAULT 'Text',
  PRIMARY KEY (`FieldID`),
  KEY `ScholarshipID` (`ScholarshipID`),
  CONSTRAINT `fk_scf_scholarship` FOREIGN KEY (`ScholarshipID`) REFERENCES `scholarship` (`ScholarshipID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `score`;
CREATE TABLE `score` (
  `ScoreID` int(11) NOT NULL AUTO_INCREMENT,
  `ApplicationID` int(11) NOT NULL,
  `CriteriaID` int(11) NOT NULL,
  `EvaluatorID` int(11) DEFAULT NULL,
  `ScoreValue` decimal(5,2) NOT NULL,
  `Comments` text DEFAULT NULL,
  PRIMARY KEY (`ScoreID`),
  KEY `ApplicationID` (`ApplicationID`),
  KEY `CriteriaID` (`CriteriaID`),
  KEY `EvaluatorID` (`EvaluatorID`),
  CONSTRAINT `score_ibfk_1` FOREIGN KEY (`ApplicationID`) REFERENCES `application` (`ApplicationID`) ON DELETE CASCADE,
  CONSTRAINT `score_ibfk_2` FOREIGN KEY (`CriteriaID`) REFERENCES `criteria` (`CriteriaID`) ON DELETE CASCADE,
  CONSTRAINT `score_ibfk_3` FOREIGN KEY (`EvaluatorID`) REFERENCES `users` (`UserID`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `submitted_document`;
CREATE TABLE `submitted_document` (
  `SubmittedDocID` int(11) NOT NULL AUTO_INCREMENT,
  `ApplicationID` int(11) NOT NULL,
  `RequirementID` int(11) NOT NULL,
  `FilePath` varchar(255) NOT NULL,
  `UploadDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `VerificationStatus` varchar(50) DEFAULT 'Pending',
  PRIMARY KEY (`SubmittedDocID`),
  KEY `ApplicationID` (`ApplicationID`),
  KEY `RequirementID` (`RequirementID`),
  CONSTRAINT `submitted_document_ibfk_1` FOREIGN KEY (`ApplicationID`) REFERENCES `application` (`ApplicationID`) ON DELETE CASCADE,
  CONSTRAINT `submitted_document_ibfk_2` FOREIGN KEY (`RequirementID`) REFERENCES `document_requirement` (`RequirementID`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `submitted_document` (`SubmittedDocID`, `ApplicationID`, `RequirementID`, `FilePath`, `UploadDate`, `VerificationStatus`) VALUES ('1', '1', '1', '../uploads/vault/6/1774231846_Screenshot 2026-02-20 174228.png', '2026-07-14 16:55:23', 'Pending');
INSERT INTO `submitted_document` (`SubmittedDocID`, `ApplicationID`, `RequirementID`, `FilePath`, `UploadDate`, `VerificationStatus`) VALUES ('2', '1', '2', '../uploads/vault/6/1784015798_Participating in Workplace Communication.pdf', '2026-07-14 16:55:23', 'Pending');
INSERT INTO `submitted_document` (`SubmittedDocID`, `ApplicationID`, `RequirementID`, `FilePath`, `UploadDate`, `VerificationStatus`) VALUES ('3', '2', '61', '../uploads/documents/STU-6-APP-2-REQ-61-6a5643305dc5f.pdf', '2026-07-14 22:09:52', 'Pending');
INSERT INTO `submitted_document` (`SubmittedDocID`, `ApplicationID`, `RequirementID`, `FilePath`, `UploadDate`, `VerificationStatus`) VALUES ('4', '2', '62', '../uploads/documents/STU-6-APP-2-REQ-62-6a5643305f0e9.pdf', '2026-07-14 22:09:52', 'Pending');
INSERT INTO `submitted_document` (`SubmittedDocID`, `ApplicationID`, `RequirementID`, `FilePath`, `UploadDate`, `VerificationStatus`) VALUES ('5', '2', '60', '../uploads/documents/STU-6-APP-2-REQ-60-6a5645fdb427b.pdf', '2026-07-14 22:21:49', 'Pending');
INSERT INTO `submitted_document` (`SubmittedDocID`, `ApplicationID`, `RequirementID`, `FilePath`, `UploadDate`, `VerificationStatus`) VALUES ('6', '3', '12', '../uploads/documents/STU-6-APP-3-REQ-12-6a5646a777cad.pdf', '2026-07-14 22:24:39', 'Pending');
INSERT INTO `submitted_document` (`SubmittedDocID`, `ApplicationID`, `RequirementID`, `FilePath`, `UploadDate`, `VerificationStatus`) VALUES ('7', '3', '13', '../uploads/documents/STU-6-APP-3-REQ-13-6a5646a7794f5.pdf', '2026-07-14 22:24:39', 'Pending');
INSERT INTO `submitted_document` (`SubmittedDocID`, `ApplicationID`, `RequirementID`, `FilePath`, `UploadDate`, `VerificationStatus`) VALUES ('8', '3', '11', '../uploads/documents/STU-6-APP-3-REQ-11-6a56483f7a856.pdf', '2026-07-14 22:31:27', 'Pending');

DROP TABLE IF EXISTS `system_notifications`;
CREATE TABLE `system_notifications` (
  `NotifID` int(11) NOT NULL AUTO_INCREMENT,
  `RecipientID` int(11) NOT NULL,
  `Title` varchar(255) NOT NULL,
  `Message` text NOT NULL,
  `IsRead` tinyint(1) DEFAULT 0,
  `DateCreated` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`NotifID`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `system_notifications` (`NotifID`, `RecipientID`, `Title`, `Message`, `IsRead`, `DateCreated`) VALUES ('1', '3', 'MOA Deactivation Notice', 'Super Admin Notice: Your scholarship program \'asfasfas\' is flagged for deactivation based on the Memorandum of Agreement (MOA). Please review your terms or contact the Super Admin.', '1', '2026-06-29 22:01:19');
INSERT INTO `system_notifications` (`NotifID`, `RecipientID`, `Title`, `Message`, `IsRead`, `DateCreated`) VALUES ('2', '3', 'MOA Deactivation Notice', 'Super Admin Notice: Your scholarship program \'asfasfas\' is flagged for deactivation based on the Memorandum of Agreement (MOA). Please review your terms or contact the Super Admin.', '1', '2026-06-29 22:05:16');
INSERT INTO `system_notifications` (`NotifID`, `RecipientID`, `Title`, `Message`, `IsRead`, `DateCreated`) VALUES ('3', '3', 'MOA Deactivation Notice', 'System Notice: Your scholarship program \'asfasfas\' is flagged for deactivation based on the Memorandum of Agreement (MOA). Please review your terms or contact the University Admin.', '1', '2026-06-29 22:29:03');
INSERT INTO `system_notifications` (`NotifID`, `RecipientID`, `Title`, `Message`, `IsRead`, `DateCreated`) VALUES ('4', '2', 'MOA Deactivation Notice', 'The scholarship program \'asfasfas\' was flagged for MOA Deactivation. The External Provider has been officially notified.', '1', '2026-06-29 22:29:03');
INSERT INTO `system_notifications` (`NotifID`, `RecipientID`, `Title`, `Message`, `IsRead`, `DateCreated`) VALUES ('5', '3', 'MOA Deactivation Notice', 'System Notice: Your scholarship program \'Wildlife Conservation Stipend\' is flagged for deactivation based on the Memorandum of Agreement (MOA). Please review your terms or contact the University Admin.', '0', '2026-07-01 09:24:02');
INSERT INTO `system_notifications` (`NotifID`, `RecipientID`, `Title`, `Message`, `IsRead`, `DateCreated`) VALUES ('6', '2', 'MOA Deactivation Notice', 'The scholarship program \'Wildlife Conservation Stipend\' was flagged for MOA Deactivation. The External Provider has been officially notified.', '0', '2026-07-01 09:24:02');

DROP TABLE IF EXISTS `system_settings`;
CREATE TABLE `system_settings` (
  `setting_key` varchar(50) NOT NULL,
  `setting_value` varchar(255) NOT NULL,
  PRIMARY KEY (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `system_settings` (`setting_key`, `setting_value`) VALUES ('require_2fa', '0');
INSERT INTO `system_settings` (`setting_key`, `setting_value`) VALUES ('session_timeout', '300');
INSERT INTO `system_settings` (`setting_key`, `setting_value`) VALUES ('strict_password', '1');

DROP TABLE IF EXISTS `user_vault`;
CREATE TABLE `user_vault` (
  `VaultID` int(11) NOT NULL AUTO_INCREMENT,
  `UserID` int(11) NOT NULL,
  `DocumentType` varchar(100) NOT NULL,
  `FilePath` varchar(255) NOT NULL,
  `UploadDate` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`VaultID`),
  KEY `UserID` (`UserID`),
  CONSTRAINT `user_vault_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `user_vault` (`VaultID`, `UserID`, `DocumentType`, `FilePath`, `UploadDate`) VALUES ('2', '6', 'Certificate of Registration (COR)', '../uploads/vault/6/1774231846_Screenshot 2026-02-20 174228.png', '2026-03-23 10:10:46');
INSERT INTO `user_vault` (`VaultID`, `UserID`, `DocumentType`, `FilePath`, `UploadDate`) VALUES ('3', '6', 'Certificate of Indigency', '../uploads/vault/6/1779206783_downloaded_file.pdf', '2026-05-20 00:06:23');
INSERT INTO `user_vault` (`VaultID`, `UserID`, `DocumentType`, `FilePath`, `UploadDate`) VALUES ('4', '6', 'Report of Grades', '../uploads/vault/6/1784015798_Participating in Workplace Communication.pdf', '2026-07-14 15:56:38');
INSERT INTO `user_vault` (`VaultID`, `UserID`, `DocumentType`, `FilePath`, `UploadDate`) VALUES ('5', '6', 'Barangay Certificate of Indigency (Mayantoc)', '../uploads/vault/6/1784016144_English for Business and Entrepreneurship.pdf', '2026-07-14 16:02:24');
INSERT INTO `user_vault` (`VaultID`, `UserID`, `DocumentType`, `FilePath`, `UploadDate`) VALUES ('6', '6', 'Barangay Certificate of Indigency', '../uploads/vault/6/1784016151_English for Business and Entrepreneurship.pdf', '2026-07-14 16:02:31');
INSERT INTO `user_vault` (`VaultID`, `UserID`, `DocumentType`, `FilePath`, `UploadDate`) VALUES ('7', '6', 'Barangay Certificate of Residency', '../uploads/vault/6/1784016160_Certificate of Instrument Validation  (1).pdf', '2026-07-14 16:02:40');
INSERT INTO `user_vault` (`VaultID`, `UserID`, `DocumentType`, `FilePath`, `UploadDate`) VALUES ('8', '6', 'BIR Certificate of Tax Exemption', '../uploads/vault/6/1784016167_[Signed]TAU-CET-QF-05-Evaluation-Form-for-Outline-Oral-Presentation.pdf', '2026-07-14 16:02:47');
INSERT INTO `user_vault` (`VaultID`, `UserID`, `DocumentType`, `FilePath`, `UploadDate`) VALUES ('9', '6', 'BIR Income Tax Return or Tax Exemption', '../uploads/vault/6/1784016178_Chapter 8.pdf', '2026-07-14 16:02:58');
INSERT INTO `user_vault` (`VaultID`, `UserID`, `DocumentType`, `FilePath`, `UploadDate`) VALUES ('10', '6', 'Certificate of Good Moral Character', '../uploads/vault/6/1784016188_[Signed]TAU-CET-QF-05-Evaluation-Form-for-Outline-Oral-Presentation.pdf', '2026-07-14 16:03:08');
INSERT INTO `user_vault` (`VaultID`, `UserID`, `DocumentType`, `FilePath`, `UploadDate`) VALUES ('11', '6', 'DSWD Certificate of Indigency', '../uploads/vault/6/1784016197_PD-Tablarin-2026-Rev02-TAU-CET-QF-05-Evaluation-Form-for-Outline-Oral-Presentation (1).pdf', '2026-07-14 16:03:17');
INSERT INTO `user_vault` (`VaultID`, `UserID`, `DocumentType`, `FilePath`, `UploadDate`) VALUES ('12', '6', 'Endorsement from Adviser', '../uploads/vault/6/1784016207_PD-Tablarin-2026-Rev02-TAU-CET-QF-05-Evaluation-Form-for-Outline-Oral-Presentation (1).pdf', '2026-07-14 16:03:27');
INSERT INTO `user_vault` (`VaultID`, `UserID`, `DocumentType`, `FilePath`, `UploadDate`) VALUES ('13', '6', 'ROTC Commandant Endorsement', '../uploads/vault/6/1784016220_asdas.pdf', '2026-07-14 16:03:40');
INSERT INTO `user_vault` (`VaultID`, `UserID`, `DocumentType`, `FilePath`, `UploadDate`) VALUES ('14', '6', 'Endorsement from Publication Adviser', '../uploads/vault/6/1784016230_AdventureWorksDW2012.pdf', '2026-07-14 16:03:50');
INSERT INTO `user_vault` (`VaultID`, `UserID`, `DocumentType`, `FilePath`, `UploadDate`) VALUES ('15', '6', 'Endorsement from Socio-Cultural Director', '../uploads/vault/6/1784016251_list-of-scholarships-and-benefits.pdf', '2026-07-14 16:04:11');
INSERT INTO `user_vault` (`VaultID`, `UserID`, `DocumentType`, `FilePath`, `UploadDate`) VALUES ('16', '6', 'Endorsement from Sports Development Director', '../uploads/vault/6/1784016263_downloaded_file.pdf', '2026-07-14 16:04:23');
INSERT INTO `user_vault` (`VaultID`, `UserID`, `DocumentType`, `FilePath`, `UploadDate`) VALUES ('17', '6', 'Income Tax Return (ITR)', '../uploads/vault/6/1784016269_list-of-scholarships-and-benefits.pdf', '2026-07-14 16:04:29');
INSERT INTO `user_vault` (`VaultID`, `UserID`, `DocumentType`, `FilePath`, `UploadDate`) VALUES ('18', '6', 'IP Certification/ID', '../uploads/vault/6/1784016279_Chapter 6.pdf', '2026-07-14 16:04:39');
INSERT INTO `user_vault` (`VaultID`, `UserID`, `DocumentType`, `FilePath`, `UploadDate`) VALUES ('19', '6', 'Medical Certificate (Fit, X-ray, Urinalysis, Fecalysis, Eye Exam)', '../uploads/vault/6/1784016289_asdas.pdf', '2026-07-14 16:04:49');
INSERT INTO `user_vault` (`VaultID`, `UserID`, `DocumentType`, `FilePath`, `UploadDate`) VALUES ('20', '6', 'MSWDO Certificate of Indigency', '../uploads/vault/6/1784016298_asdas.pdf', '2026-07-14 16:04:58');
INSERT INTO `user_vault` (`VaultID`, `UserID`, `DocumentType`, `FilePath`, `UploadDate`) VALUES ('21', '6', 'Recommendation Letter 1', '../uploads/vault/6/1784016305_AdventureWorksDW2012.pdf', '2026-07-14 16:05:05');
INSERT INTO `user_vault` (`VaultID`, `UserID`, `DocumentType`, `FilePath`, `UploadDate`) VALUES ('22', '6', 'Recommendation Letter 2', '../uploads/vault/6/1784016331_list-of-scholarships-and-benefits.pdf', '2026-07-14 16:05:31');

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `UserID` int(11) NOT NULL AUTO_INCREMENT,
  `Username` varchar(50) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `PasswordHash` varchar(255) NOT NULL,
  `FirstName` varchar(100) NOT NULL,
  `LastName` varchar(100) NOT NULL,
  `Role` enum('Student','Internal_Admin','External_Admin','Super_Admin') DEFAULT 'Student',
  `AccountStatus` varchar(20) DEFAULT 'Active',
  `StudentID_Num` varchar(50) DEFAULT NULL,
  `YearLevel` varchar(50) DEFAULT NULL,
  `GPA` decimal(4,2) DEFAULT NULL,
  `Major` varchar(100) DEFAULT NULL,
  `ProgramID` int(11) DEFAULT NULL,
  `Department` varchar(100) DEFAULT NULL,
  `Organization` varchar(100) DEFAULT NULL,
  `ContactNumber` varchar(20) DEFAULT NULL,
  `DateOfBirth` date DEFAULT NULL,
  `ProfilePicture` varchar(255) DEFAULT NULL,
  `ResetToken` varchar(255) DEFAULT NULL,
  `ResetTokenExpire` datetime DEFAULT NULL,
  `Gender` varchar(20) NOT NULL DEFAULT 'Not Specified',
  PRIMARY KEY (`UserID`),
  UNIQUE KEY `Username` (`Username`),
  UNIQUE KEY `Email` (`Email`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `users` (`UserID`, `Username`, `Email`, `PasswordHash`, `FirstName`, `LastName`, `Role`, `AccountStatus`, `StudentID_Num`, `YearLevel`, `GPA`, `Major`, `ProgramID`, `Department`, `Organization`, `ContactNumber`, `DateOfBirth`, `ProfilePicture`, `ResetToken`, `ResetTokenExpire`, `Gender`) VALUES ('1', 'superadmin', 'admin@tau.edu.ph', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System', 'Admin', 'Super_Admin', 'Active', NULL, NULL, NULL, NULL, NULL, 'IT Department', NULL, NULL, NULL, NULL, NULL, NULL, 'Not Specified');
INSERT INTO `users` (`UserID`, `Username`, `Email`, `PasswordHash`, `FirstName`, `LastName`, `Role`, `AccountStatus`, `StudentID_Num`, `YearLevel`, `GPA`, `Major`, `ProgramID`, `Department`, `Organization`, `ContactNumber`, `DateOfBirth`, `ProfilePicture`, `ResetToken`, `ResetTokenExpire`, `Gender`) VALUES ('2', 'internal01', 'scholarships@tau.edu.ph', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Office of', 'Student Affairs', 'Internal_Admin', 'Active', NULL, NULL, NULL, NULL, NULL, 'OSA', NULL, NULL, NULL, NULL, NULL, NULL, 'Not Specified');
INSERT INTO `users` (`UserID`, `Username`, `Email`, `PasswordHash`, `FirstName`, `LastName`, `Role`, `AccountStatus`, `StudentID_Num`, `YearLevel`, `GPA`, `Major`, `ProgramID`, `Department`, `Organization`, `ContactNumber`, `DateOfBirth`, `ProfilePicture`, `ResetToken`, `ResetTokenExpire`, `Gender`) VALUES ('3', 'ched_eval', 'region3@ched.gov.ph', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Regional', 'Evaluator', 'External_Admin', 'Active', NULL, NULL, NULL, NULL, NULL, NULL, 'CHED Region III', NULL, NULL, NULL, NULL, NULL, 'Not Specified');
INSERT INTO `users` (`UserID`, `Username`, `Email`, `PasswordHash`, `FirstName`, `LastName`, `Role`, `AccountStatus`, `StudentID_Num`, `YearLevel`, `GPA`, `Major`, `ProgramID`, `Department`, `Organization`, `ContactNumber`, `DateOfBirth`, `ProfilePicture`, `ResetToken`, `ResetTokenExpire`, `Gender`) VALUES ('4', '2023-0001', 'juan@tau.edu.ph', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Juan', 'Dela cruz', 'Student', 'Active', '2023-0001', NULL, '1.45', 'BS Information Technology (BSIT)', '18', NULL, NULL, '09201952345', '2004-02-20', '../uploads/profiles/USER_4_PROFILE_1774007130.png', NULL, NULL, 'Not Specified');
INSERT INTO `users` (`UserID`, `Username`, `Email`, `PasswordHash`, `FirstName`, `LastName`, `Role`, `AccountStatus`, `StudentID_Num`, `YearLevel`, `GPA`, `Major`, `ProgramID`, `Department`, `Organization`, `ContactNumber`, `DateOfBirth`, `ProfilePicture`, `ResetToken`, `ResetTokenExpire`, `Gender`) VALUES ('5', '2023-0002', 'maria@tau.edu.ph', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Maria', 'Clara', 'Student', 'Active', '2023-0002', NULL, '1.20', 'Agriculture', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Not Specified');
INSERT INTO `users` (`UserID`, `Username`, `Email`, `PasswordHash`, `FirstName`, `LastName`, `Role`, `AccountStatus`, `StudentID_Num`, `YearLevel`, `GPA`, `Major`, `ProgramID`, `Department`, `Organization`, `ContactNumber`, `DateOfBirth`, `ProfilePicture`, `ResetToken`, `ResetTokenExpire`, `Gender`) VALUES ('6', '2023100194', 'cjcanaria63@gmail.com', '$2y$10$wLXMYYkp.yKwA3vKg6QNqeR0..8wfdMlyq9ZraYGKFbF2ebuHLmxW', 'Cj', 'Canaria', 'Student', 'Active', '2023100194', '3rd Year', '1.45', 'BS Information Technology (BSIT)', '18', NULL, NULL, '09369522832', '2001-06-12', '../uploads/profiles/USER_6_PROFILE_1777004051.jpg', 'cc279c208ff806424f82f89bd8e91f43', '2026-07-09 12:09:29', 'Male');
INSERT INTO `users` (`UserID`, `Username`, `Email`, `PasswordHash`, `FirstName`, `LastName`, `Role`, `AccountStatus`, `StudentID_Num`, `YearLevel`, `GPA`, `Major`, `ProgramID`, `Department`, `Organization`, `ContactNumber`, `DateOfBirth`, `ProfilePicture`, `ResetToken`, `ResetTokenExpire`, `Gender`) VALUES ('7', '2023100067', 'bbleb21@gmail.com', '$2y$10$E.Y5ibEodV06RUkZPeaj2e8j0ThJkyK..47RjvG0uso0mqxyUNhXm', 'Juan', 'Canaria', 'Student', 'Active', '2023100067', '2nd Year', '0.00', 'BS Tourism Management', '11', NULL, NULL, NULL, NULL, NULL, '9792e5c7e0a4c8b1fc15ef797aa34636b81cd221e3033ac39b22f9956940945f', '2026-05-19 21:04:47', 'Not Specified');
INSERT INTO `users` (`UserID`, `Username`, `Email`, `PasswordHash`, `FirstName`, `LastName`, `Role`, `AccountStatus`, `StudentID_Num`, `YearLevel`, `GPA`, `Major`, `ProgramID`, `Department`, `Organization`, `ContactNumber`, `DateOfBirth`, `ProfilePicture`, `ResetToken`, `ResetTokenExpire`, `Gender`) VALUES ('9', '2023100797', 'chescamaetablarin@gmail.com', '$2y$10$F8IYhfJPlxbYOwavnTRDa.fPfR5TDyc30R6rgHvpw57rYnuU3OWd6', 'Chesca Mae', 'Tablarin', 'Student', 'Active', '2023100797', '3rd Year', '0.00', 'BS Information Technology (BSIT)', '18', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Female');
INSERT INTO `users` (`UserID`, `Username`, `Email`, `PasswordHash`, `FirstName`, `LastName`, `Role`, `AccountStatus`, `StudentID_Num`, `YearLevel`, `GPA`, `Major`, `ProgramID`, `Department`, `Organization`, `ContactNumber`, `DateOfBirth`, `ProfilePicture`, `ResetToken`, `ResetTokenExpire`, `Gender`) VALUES ('11', '2023100068', 'jkcanaria123@gmail.com', '$2y$10$pGKywrMVkks058ChcQff/Or65qoV6de1j3yVwLrL7a443L791H8UW', 'John', 'Baloco', 'Student', 'Active', '2023100068', '2nd Year', '0.00', 'BS Geodetic Engineering', '17', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Male');
INSERT INTO `users` (`UserID`, `Username`, `Email`, `PasswordHash`, `FirstName`, `LastName`, `Role`, `AccountStatus`, `StudentID_Num`, `YearLevel`, `GPA`, `Major`, `ProgramID`, `Department`, `Organization`, `ContactNumber`, `DateOfBirth`, `ProfilePicture`, `ResetToken`, `ResetTokenExpire`, `Gender`) VALUES ('12', '2023100184', 'jluna@gmail.com', '$2y$10$TZZShYuhtR1TqRcXouk37umalrBC5oVZ45/O2/dC3y8cTjS6YStua', 'Juan', 'De Luna', 'Student', 'Active', '2023100184', '2nd Year', '0.00', 'AB Economics', '5', NULL, NULL, '09216081819', '2008-12-18', NULL, NULL, NULL, 'Male');
INSERT INTO `users` (`UserID`, `Username`, `Email`, `PasswordHash`, `FirstName`, `LastName`, `Role`, `AccountStatus`, `StudentID_Num`, `YearLevel`, `GPA`, `Major`, `ProgramID`, `Department`, `Organization`, `ContactNumber`, `DateOfBirth`, `ProfilePicture`, `ResetToken`, `ResetTokenExpire`, `Gender`) VALUES ('13', '2023100767', 'chescatablarinmangino@gmail.com', '$2y$10$pKnRPsVTD1BSVi06k.fbQufhNXxOHdLS6//f22Dla5lajv7ji91Xi', 'Chesca Mae', 'Tablarin', 'Student', 'Active', '2023100767', '1st Year', '0.00', 'BS Development Communication', '7', NULL, NULL, '09876754667', '2000-10-06', NULL, NULL, NULL, 'Female');
INSERT INTO `users` (`UserID`, `Username`, `Email`, `PasswordHash`, `FirstName`, `LastName`, `Role`, `AccountStatus`, `StudentID_Num`, `YearLevel`, `GPA`, `Major`, `ProgramID`, `Department`, `Organization`, `ContactNumber`, `DateOfBirth`, `ProfilePicture`, `ResetToken`, `ResetTokenExpire`, `Gender`) VALUES ('14', '2023100269', 'robinzalzos@gmail.com', '$2y$10$uC3HuBstewUEZcHLA5WPB.Qu3EY4w8cduffPCGquIBGU/YjAvEibS', 'CJ', 'Dela Cruz', 'Student', 'Active', '2023100269', '2nd Year', '0.00', 'BS Psychology', '6', NULL, NULL, '09152347886', '1999-09-05', NULL, NULL, NULL, 'Male');
INSERT INTO `users` (`UserID`, `Username`, `Email`, `PasswordHash`, `FirstName`, `LastName`, `Role`, `AccountStatus`, `StudentID_Num`, `YearLevel`, `GPA`, `Major`, `ProgramID`, `Department`, `Organization`, `ContactNumber`, `DateOfBirth`, `ProfilePicture`, `ResetToken`, `ResetTokenExpire`, `Gender`) VALUES ('15', '', 'chrisjunebagayansanidad@gmail.com', '$2y$10$UPaP0uVGqJgGChia74TeeuYjBvTiK3l35kJHpVkGIiIqQBtQCclPK', 'John', 'James', 'Student', 'Active', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Not Specified');
INSERT INTO `users` (`UserID`, `Username`, `Email`, `PasswordHash`, `FirstName`, `LastName`, `Role`, `AccountStatus`, `StudentID_Num`, `YearLevel`, `GPA`, `Major`, `ProgramID`, `Department`, `Organization`, `ContactNumber`, `DateOfBirth`, `ProfilePicture`, `ResetToken`, `ResetTokenExpire`, `Gender`) VALUES ('16', '2023100667', 'kalbostvfkmartin@gmail.com', '$2y$10$ZQlP34brUtkd3M3eGxfCCuIWyl7bKsgt/OLnegBao0.oA2Zibf4ze', 'Juan', 'Okay', 'Student', 'Active', '2023100667', '2nd Year', '0.00', 'BS Food Technology', '4', NULL, NULL, '09087141591', '2005-05-05', NULL, NULL, NULL, 'Male');
INSERT INTO `users` (`UserID`, `Username`, `Email`, `PasswordHash`, `FirstName`, `LastName`, `Role`, `AccountStatus`, `StudentID_Num`, `YearLevel`, `GPA`, `Major`, `ProgramID`, `Department`, `Organization`, `ContactNumber`, `DateOfBirth`, `ProfilePicture`, `ResetToken`, `ResetTokenExpire`, `Gender`) VALUES ('17', 'tarlac_lgu', 'lgu@tarlaccity.gov.ph', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Tarlac City', 'LGU', 'External_Admin', 'Active', NULL, NULL, NULL, NULL, NULL, NULL, 'Tarlac City Government', NULL, NULL, NULL, NULL, NULL, 'Not Specified');
INSERT INTO `users` (`UserID`, `Username`, `Email`, `PasswordHash`, `FirstName`, `LastName`, `Role`, `AccountStatus`, `StudentID_Num`, `YearLevel`, `GPA`, `Major`, `ProgramID`, `Department`, `Organization`, `ContactNumber`, `DateOfBirth`, `ProfilePicture`, `ResetToken`, `ResetTokenExpire`, `Gender`) VALUES ('18', 'dost_ro3', 'region3@dost.gov.ph', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'DOST', 'Region III', 'External_Admin', 'Active', NULL, NULL, NULL, NULL, NULL, NULL, 'Department of Science and Technology', NULL, NULL, NULL, NULL, NULL, 'Not Specified');
INSERT INTO `users` (`UserID`, `Username`, `Email`, `PasswordHash`, `FirstName`, `LastName`, `Role`, `AccountStatus`, `StudentID_Num`, `YearLevel`, `GPA`, `Major`, `ProgramID`, `Department`, `Organization`, `ContactNumber`, `DateOfBirth`, `ProfilePicture`, `ResetToken`, `ResetTokenExpire`, `Gender`) VALUES ('19', 'da_acef', 'acef@da.gov.ph', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Dept. of', 'Agriculture', 'External_Admin', 'Active', NULL, NULL, NULL, NULL, NULL, NULL, 'DA - ACEF', NULL, NULL, NULL, NULL, NULL, 'Not Specified');
INSERT INTO `users` (`UserID`, `Username`, `Email`, `PasswordHash`, `FirstName`, `LastName`, `Role`, `AccountStatus`, `StudentID_Num`, `YearLevel`, `GPA`, `Major`, `ProgramID`, `Department`, `Organization`, `ContactNumber`, `DateOfBirth`, `ProfilePicture`, `ResetToken`, `ResetTokenExpire`, `Gender`) VALUES ('20', 'philchema', 'hr@philchema.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Philchema', 'Inc.', 'External_Admin', 'Active', NULL, NULL, NULL, NULL, NULL, NULL, 'Philchema Inc.', NULL, NULL, NULL, NULL, NULL, 'Not Specified');
INSERT INTO `users` (`UserID`, `Username`, `Email`, `PasswordHash`, `FirstName`, `LastName`, `Role`, `AccountStatus`, `StudentID_Num`, `YearLevel`, `GPA`, `Major`, `ProgramID`, `Department`, `Organization`, `ContactNumber`, `DateOfBirth`, `ProfilePicture`, `ResetToken`, `ResetTokenExpire`, `Gender`) VALUES ('21', 'bounty_cares', 'foundation@bounty.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Bounty Cares', 'Foundation', 'External_Admin', 'Active', NULL, NULL, NULL, NULL, NULL, NULL, 'Bounty Cares Foundation Inc.', NULL, NULL, NULL, NULL, NULL, 'Not Specified');
INSERT INTO `users` (`UserID`, `Username`, `Email`, `PasswordHash`, `FirstName`, `LastName`, `Role`, `AccountStatus`, `StudentID_Num`, `YearLevel`, `GPA`, `Major`, `ProgramID`, `Department`, `Organization`, `ContactNumber`, `DateOfBirth`, `ProfilePicture`, `ResetToken`, `ResetTokenExpire`, `Gender`) VALUES ('22', 'ncaf_admin', 'info@ncaf.ph', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Ninoy & Cory', 'Aquino Foundation', 'External_Admin', 'Active', NULL, NULL, NULL, NULL, NULL, NULL, 'Ninoy and Cory Aquino Foundation Inc.', NULL, NULL, NULL, NULL, NULL, 'Not Specified');
INSERT INTO `users` (`UserID`, `Username`, `Email`, `PasswordHash`, `FirstName`, `LastName`, `Role`, `AccountStatus`, `StudentID_Num`, `YearLevel`, `GPA`, `Major`, `ProgramID`, `Department`, `Organization`, `ContactNumber`, `DateOfBirth`, `ProfilePicture`, `ResetToken`, `ResetTokenExpire`, `Gender`) VALUES ('23', 'gep_reg3', 'reg3@gep.org.ph', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Geodetic', 'Engineers', 'External_Admin', 'Active', NULL, NULL, NULL, NULL, NULL, NULL, 'Geodetic Engineers of the Phil.', NULL, NULL, NULL, NULL, NULL, 'Not Specified');
INSERT INTO `users` (`UserID`, `Username`, `Email`, `PasswordHash`, `FirstName`, `LastName`, `Role`, `AccountStatus`, `StudentID_Num`, `YearLevel`, `GPA`, `Major`, `ProgramID`, `Department`, `Organization`, `ContactNumber`, `DateOfBirth`, `ProfilePicture`, `ResetToken`, `ResetTokenExpire`, `Gender`) VALUES ('24', 'tau_alumni', 'alumni@tau.edu.ph', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'TAU', 'Alumni Assoc', 'External_Admin', 'Active', NULL, NULL, NULL, NULL, NULL, NULL, 'TAU Alumni and Friends', NULL, NULL, NULL, NULL, NULL, 'Not Specified');
INSERT INTO `users` (`UserID`, `Username`, `Email`, `PasswordHash`, `FirstName`, `LastName`, `Role`, `AccountStatus`, `StudentID_Num`, `YearLevel`, `GPA`, `Major`, `ProgramID`, `Department`, `Organization`, `ContactNumber`, `DateOfBirth`, `ProfilePicture`, `ResetToken`, `ResetTokenExpire`, `Gender`) VALUES ('25', 'tarlac_sponsors', 'sponsors@tarlac.ph', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Private', 'Benefactors', 'External_Admin', 'Active', NULL, NULL, NULL, NULL, NULL, NULL, 'Tarlac Private Foundations Coalition', NULL, NULL, NULL, NULL, NULL, 'Not Specified');

SET FOREIGN_KEY_CHECKS = 1;
