REPLACE INTO `oxarticles` (`OXID`, `OXMAPID`, `OXSHOPID`, `OXPARENTID`, `OXACTIVE`, `OXACTIVEFROM`, `OXACTIVETO`, `OXARTNUM`, `OXEAN`, `OXDISTEAN`, `OXMPN`, `OXTITLE`, `OXSHORTDESC`, `OXPRICE`, `OXBLFIXEDPRICE`, `OXPRICEA`, `OXPRICEB`, `OXPRICEC`, `OXBPRICE`, `OXTPRICE`, `OXUNITNAME`, `OXUNITQUANTITY`, `OXEXTURL`, `OXURLDESC`, `OXURLIMG`, `OXVAT`, `OXTHUMB`, `OXICON`, `OXPIC1`, `OXPIC2`, `OXPIC3`, `OXPIC4`, `OXPIC5`, `OXPIC6`, `OXPIC7`, `OXPIC8`, `OXPIC9`, `OXPIC10`, `OXPIC11`, `OXPIC12`, `OXWEIGHT`, `OXSTOCK`, `OXSTOCKFLAG`, `OXSTOCKTEXT`, `OXNOSTOCKTEXT`, `OXDELIVERY`, `OXINSERT`, `OXTIMESTAMP`, `OXLENGTH`, `OXWIDTH`, `OXHEIGHT`, `OXFILE`, `OXSEARCHKEYS`, `OXTEMPLATE`, `OXQUESTIONEMAIL`, `OXISSEARCH`, `OXISCONFIGURABLE`, `OXVARNAME`, `OXVARSTOCK`, `OXVARCOUNT`, `OXVARSELECT`, `OXVARMINPRICE`, `OXVARMAXPRICE`, `OXVARNAME_1`, `OXVARSELECT_1`, `OXVARNAME_2`, `OXVARSELECT_2`, `OXVARNAME_3`, `OXVARSELECT_3`, `OXTITLE_1`, `OXSHORTDESC_1`, `OXURLDESC_1`, `OXSEARCHKEYS_1`, `OXTITLE_2`, `OXSHORTDESC_2`, `OXURLDESC_2`, `OXSEARCHKEYS_2`, `OXTITLE_3`, `OXSHORTDESC_3`, `OXURLDESC_3`, `OXSEARCHKEYS_3`, `OXBUNDLEID`, `OXFOLDER`, `OXSUBCLASS`, `OXSTOCKTEXT_1`, `OXSTOCKTEXT_2`, `OXSTOCKTEXT_3`, `OXNOSTOCKTEXT_1`, `OXNOSTOCKTEXT_2`, `OXNOSTOCKTEXT_3`, `OXSORT`, `OXSOLDAMOUNT`, `OXNONMATERIAL`, `OXFREESHIPPING`, `OXREMINDACTIVE`, `OXREMINDAMOUNT`, `OXAMITEMID`, `OXAMTASKID`, `OXVENDORID`, `OXMANUFACTURERID`, `OXSKIPDISCOUNTS`, `OXRATING`, `OXRATINGCNT`, `OXMINDELTIME`, `OXMAXDELTIME`, `OXDELTIMEUNIT`, `OXUPDATEPRICE`, `OXUPDATEPRICEA`, `OXUPDATEPRICEB`, `OXUPDATEPRICEC`, `OXUPDATEPRICETIME`, `OXISDOWNLOADABLE`, `OXSHOWCUSTOMAGREEMENT`, `OEVATTBE_ISTBESERVICE`, `OXHIDDEN`) VALUES
('1126',1100, 1,'',1,'0000-00-00 00:00:00','0000-00-00 00:00:00','1126','','','','ABSINTH','ABSINTH',30,0,0,0,0,0,0,'',0,'','','',NULL,'','','','','','','','','','','','','','',0,10,1,'','','0000-00-00','2010-12-15','2021-06-01 10:22:48',0,0,0,'','','','',1,0,'',0,0,'',30,0,'','','','','','','','','','','','','','','','','','','','','oxarticle','','','','','','',0,2,0,0,0,0,'','','vendorId','manufacturerId',0,5,1,4,5,'DAY',0,0,0,0,'0000-00-00 00:00:00',0,1,1,0),
('1127',1101,1,'',1,'0000-00-00 00:00:00','0000-00-00 00:00:00','1127','','','','ABSINTH','ABSINTH',30,0,0,0,0,0,0,'',0,'','','',NULL,'','','','','','','','','','','','','','',0,10,1,'','','0000-00-00','2010-12-15','2021-06-01 10:22:48',0,0,0,'','','','',1,0,'',0,0,'',30,0,'','','','','','','','','','','','','','','','','','','','','oxarticle','','','','','','',0,2,0,0,0,0,'','','vendorId','manufacturerId',0,5,1,4,5,'DAY',0,0,0,0,'0000-00-00 00:00:00',0,1,1,0),
('1131',1102,1,'',1,'0000-00-00 00:00:00','0000-00-00 00:00:00','1131','','','','ABSINTH','ABSINTH',30,0,0,0,0,0,0,'',0,'','','',NULL,'','','','','','','','','','','','','','',0,10,1,'','','0000-00-00','2010-12-15','2021-06-01 10:22:48',0,0,0,'','','','',1,0,'',0,0,'',30,0,'','','','','','','','','','','','','','','','','','','','','oxarticle','','','','','','',0,2,0,0,0,0,'','','vendorId','manufacturerId',0,5,1,4,5,'DAY',0,0,0,0,'0000-00-00 00:00:00',0,1,1,0),
('_testArticle',1103,1,'',1,'0000-00-00 00:00:00','0000-00-00 00:00:00','1122','','','','ABSINTH','ABSINTH',699,0,0,0,0,0,0,'',0,'','','',NULL,'','','','','','','','','','','','','','',0,10,1,'','','0000-00-00','2010-12-15','2021-06-01 10:22:48',0,0,0,'','','','',1,0,'',0,0,'',699,0,'','','','','','','','','','','','','','','','','','','','','oxarticle','','','','','','',0,2,0,0,0,0,'','','','manufacturerId',0,5,1,4,5,'DAY',0,0,0,0,'0000-00-00 00:00:00',0,1,1,0);

REPLACE INTO `oxarticles2shop` (`OXSHOPID`, `OXMAPOBJECTID`, `OXTIMESTAMP`) VALUES
(1,	1100,	'2023-04-19 08:30:35'),
(1,	1101,	'2023-04-19 08:30:35'),
(1,	1102,	'2023-04-19 08:30:35'),
(1,	1103,	'2023-04-19 09:10:04');

REPLACE INTO `oxactions2article` (`OXID`, `OXSHOPID`, `OXACTIONID`, `OXARTID`, `OXSORT`, `OXTIMESTAMP`) VALUES
('05879203c875d7954652f396232d1952',	1,	'oxnewest',	'f4f2d8eee51b0fd5eb60a46dff1166d8',	1,	'2016-07-19 14:38:25');

REPLACE INTO `oxobject2article` (`OXID`, `OXOBJECTID`, `OXARTICLENID`, `OXSORT`, `OXTIMESTAMP`) VALUES
('f3b42c8ef2304f374.62829975',	'1126',	'1964',	0,	'2023-05-15 12:25:46'),
('f3b42c8ef2306b686.51689146',	'1127',	'1964',	1,	'2023-05-15 12:25:46');

-- REPLACE INTO `oevattbe_countryvatgroups` (`OEVATTBE_ID`, `OEVATTBE_COUNTRYID`, `OEVATTBE_NAME`, `OEVATTBE_DESCRIPTION`, `OEVATTBE_RATE`, `OEVATTBE_TIMESTAMP`) VALUES
-- (1,	'a7c40f631fc920687.20179984',	'name',	'',	8.00,	'2023-04-13 12:04:47'),
-- (2,	'a7c40f6320aeb2ec2.72885259',	'name',	'',	8.00,	'2023-04-13 12:04:47');

REPLACE INTO `oevattbe_articlevat` (`OEVATTBE_ARTICLEID`, `OEVATTBE_COUNTRYID`, `OEVATTBE_VATGROUPID`, `OEVATTBE_TIMESTAMP`) VALUES
('1126',	'a7c40f631fc920687.20179984',	1,	'2023-04-13 12:04:47'),
('1126',	'a7c40f6320aeb2ec2.72885259',	1,	'2023-04-13 12:04:47');

REPLACE INTO `oxobject2category` (`OXID`, `OXOBJECTID`, `OXCATNID`, `OXPOS`, `OXTIME`) VALUES
('c3944abfcb65b13a3.66180278', '1126', '30e44ab8593023055.23928895', 0, 1152122038);

UPDATE `oxactions` SET oxactive = 1 WHERE OXID='oxstart';

UPDATE `oxcountry` SET oevattbe_appliestbevat = 1 WHERE OXID = 'a7c40f631fc920687.20179984';

TRUNCATE TABLE `oevattbe_countryvatgroups`;
REPLACE INTO `oevattbe_countryvatgroups` (`OEVATTBE_ID`, `OEVATTBE_COUNTRYID`, `OEVATTBE_NAME`, `OEVATTBE_DESCRIPTION`, `OEVATTBE_RATE`, `OEVATTBE_TIMESTAMP`) VALUES
(1,	'a7c40f632e04633c9.47194042',	'Reduce rate 1',	'',	6.00,	'2023-05-31 11:41:54'),
(2,	'a7c40f632e04633c9.47194042',	'Reduce rate 2',	'',	12.00,	'2023-05-31 11:41:54'),
(3,	'a7c40f632e04633c9.47194042',	'Standard rate',	'',	21.00,	'2023-05-31 11:41:54'),
(4,	'a7c40f632e04633c9.47194042',	'Parking rate',	'',	12.00,	'2023-05-31 11:41:54'),
(5,	'8f241f110955d3260.55487539',	'Reduce rate',	'',	9.00,	'2023-05-31 11:41:54'),
(6,	'8f241f110955d3260.55487539',	'Standard rate',	'',	20.00,	'2023-05-31 11:41:54'),
(7,	'8f241f110957cb457.97820918',	'Reduce rate',	'',	15.00,	'2023-05-31 11:41:54'),
(8,	'8f241f110957cb457.97820918',	'Standard rate',	'',	21.00,	'2023-05-31 11:41:54'),
(9,	'8f241f110957e6ef8.56458418',	'Standard rate',	'',	25.00,	'2023-05-31 11:41:54'),
(10,	'a7c40f631fc920687.20179984',	'Reduce rate',	'',	7.00,	'2023-05-31 11:41:54'),
(11,	'a7c40f631fc920687.20179984',	'Standard rate',	'',	19.00,	'2023-05-31 11:41:54'),
(12,	'8f241f110958b69e4.93886171',	'Reduce rate',	'',	9.00,	'2023-05-31 11:41:54'),
(13,	'8f241f110958b69e4.93886171',	'Standard rate',	'',	20.00,	'2023-05-31 11:41:54'),
(14,	'a7c40f633114e8fc6.25257477',	'Reduce rate 1',	'',	6.50,	'2023-05-31 11:41:54'),
(15,	'a7c40f633114e8fc6.25257477',	'Reduce rate 2',	'',	13.00,	'2023-05-31 11:41:54'),
(16,	'a7c40f633114e8fc6.25257477',	'Standard rate',	'',	23.00,	'2023-05-31 11:41:54'),
(17,	'a7c40f633038cd578.22975442',	'Super reduce rate',	'',	4.00,	'2023-05-31 11:41:54'),
(18,	'a7c40f633038cd578.22975442',	'Reduce rate',	'',	10.00,	'2023-05-31 11:41:54'),
(19,	'a7c40f633038cd578.22975442',	'Standard rate',	'',	21.00,	'2023-05-31 11:41:54'),
(20,	'a7c40f63272a57296.32117580',	'Super reduce rate',	'',	2.10,	'2023-05-31 11:41:54'),
(21,	'a7c40f63272a57296.32117580',	'Reduce rate 1',	'',	5.50,	'2023-05-31 11:41:54'),
(22,	'a7c40f63272a57296.32117580',	'Reduce rate 2',	'',	10.00,	'2023-05-31 11:41:54'),
(23,	'a7c40f63272a57296.32117580',	'Standard rate',	'',	20.00,	'2023-05-31 11:41:54'),
(24,	'8f241f11095789a04.65154246',	'Reduce rate 1',	'',	5.00,	'2023-05-31 11:41:54'),
(25,	'8f241f11095789a04.65154246',	'Reduce rate 2',	'',	13.00,	'2023-05-31 11:41:54'),
(26,	'8f241f11095789a04.65154246',	'Standard rate',	'',	25.00,	'2023-05-31 11:41:54'),
(27,	'a7c40f632be4237c2.48517912',	'Super reduce rate',	'',	4.80,	'2023-05-31 11:41:54'),
(28,	'a7c40f632be4237c2.48517912',	'Reduce rate 1',	'',	9.00,	'2023-05-31 11:41:54'),
(29,	'a7c40f632be4237c2.48517912',	'Reduce rate 2',	'',	13.50,	'2023-05-31 11:41:54'),
(30,	'a7c40f632be4237c2.48517912',	'Standard rate',	'',	23.00,	'2023-05-31 11:41:54'),
(31,	'a7c40f632be4237c2.48517912',	'Parking rate',	'',	13.50,	'2023-05-31 11:41:54'),
(32,	'a7c40f6323c4bfb36.59919433',	'Super reduce rate',	'',	4.00,	'2023-05-31 11:41:54'),
(33,	'a7c40f6323c4bfb36.59919433',	'Reduce rate',	'',	10.00,	'2023-05-31 11:41:54'),
(34,	'a7c40f6323c4bfb36.59919433',	'Standard rate',	'',	22.00,	'2023-05-31 11:41:54'),
(35,	'8f241f110957b6896.52725150',	'Reduce rate 1',	'',	5.00,	'2023-05-31 11:41:54'),
(36,	'8f241f110957b6896.52725150',	'Reduce rate 2',	'',	9.00,	'2023-05-31 11:41:54'),
(37,	'8f241f110957b6896.52725150',	'Standard rate',	'',	19.00,	'2023-05-31 11:41:54'),
(38,	'8f241f11095cf2ea6.73925511',	'Reduce rate',	'',	12.00,	'2023-05-31 11:41:54'),
(39,	'8f241f11095cf2ea6.73925511',	'Standard rate',	'',	21.00,	'2023-05-31 11:41:54'),
(40,	'8f241f11095d6ffa8.86593236',	'Reduce rate 1',	'',	5.00,	'2023-05-31 11:41:54'),
(41,	'8f241f11095d6ffa8.86593236',	'Reduce rate 2',	'',	9.00,	'2023-05-31 11:41:54'),
(42,	'8f241f11095d6ffa8.86593236',	'Standard rate',	'',	21.00,	'2023-05-31 11:41:54'),
(43,	'a7c40f63264309e05.58576680',	'Super reduce rate',	'',	3.00,	'2023-05-31 11:41:54'),
(44,	'a7c40f63264309e05.58576680',	'Reduce rate',	'',	6.00,	'2023-05-31 11:41:54'),
(45,	'a7c40f63264309e05.58576680',	'Reduce rate',	'',	12.00,	'2023-05-31 11:41:54'),
(46,	'a7c40f63264309e05.58576680',	'Standard rate',	'',	15.00,	'2023-05-31 11:41:54'),
(47,	'a7c40f63264309e05.58576680',	'Parking rate',	'',	12.00,	'2023-05-31 11:41:54'),
(48,	'8f241f11095b3e016.98213173',	'Reduce rate 1',	'',	5.00,	'2023-05-31 11:41:54'),
(49,	'8f241f11095b3e016.98213173',	'Reduce rate 2',	'',	18.00,	'2023-05-31 11:41:54'),
(50,	'8f241f11095b3e016.98213173',	'Standard rate',	'',	27.00,	'2023-05-31 11:41:54'),
(51,	'8f241f11095e36eb3.69050509',	'Reduce rate 1',	'',	5.00,	'2023-05-31 11:41:54'),
(52,	'8f241f11095e36eb3.69050509',	'Reduce rate 2',	'',	7.00,	'2023-05-31 11:41:54'),
(53,	'8f241f11095e36eb3.69050509',	'Standard rate',	'',	18.00,	'2023-05-31 11:41:54'),
(54,	'a7c40f632cdd63c52.64272623',	'Reduce rate',	'',	6.00,	'2023-05-31 11:41:54'),
(55,	'a7c40f632cdd63c52.64272623',	'Standard rate',	'',	21.00,	'2023-05-31 11:41:54'),
(56,	'a7c40f6320aeb2ec2.72885259',	'Reduce rate',	'',	10.00,	'2023-05-31 11:41:54'),
(57,	'a7c40f6320aeb2ec2.72885259',	'Standard rate',	'',	20.00,	'2023-05-31 11:41:54'),
(58,	'a7c40f6320aeb2ec2.72885259',	'Parking rate',	'',	12.00,	'2023-05-31 11:41:54'),
(59,	'8f241f1109624d3f8.50953605',	'Reduce rate 1',	'',	5.00,	'2023-05-31 11:41:54'),
(60,	'8f241f1109624d3f8.50953605',	'Reduce rate 2',	'',	8.00,	'2023-05-31 11:41:54'),
(61,	'8f241f1109624d3f8.50953605',	'Standard rate',	'',	23.00,	'2023-05-31 11:41:54'),
(62,	'a7c40f632f65bd8e2.84963272',	'Reduce rate 1',	'',	6.00,	'2023-05-31 11:41:54'),
(63,	'a7c40f632f65bd8e2.84963272',	'Reduce rate 2',	'',	13.00,	'2023-05-31 11:41:54'),
(64,	'a7c40f632f65bd8e2.84963272',	'Standard rate',	'',	23.00,	'2023-05-31 11:41:54'),
(65,	'a7c40f632f65bd8e2.84963272',	'Parking rate',	'',	13.00,	'2023-05-31 11:41:54'),
(66,	'8f241f110962c3007.60363573',	'Reduce rate 1',	'',	5.00,	'2023-05-31 11:41:54'),
(67,	'8f241f110962c3007.60363573',	'Reduce rate 2',	'',	9.00,	'2023-05-31 11:41:54'),
(68,	'8f241f110962c3007.60363573',	'Standard rate',	'',	24.00,	'2023-05-31 11:41:54'),
(69,	'8f241f11096497149.85116254',	'Reduce rate',	'',	9.50,	'2023-05-31 11:41:54'),
(70,	'8f241f11096497149.85116254',	'Standard rate',	'',	22.00,	'2023-05-31 11:41:54'),
(71,	'8f241f1109647a265.29938154',	'Reduce rate',	'',	10.00,	'2023-05-31 11:41:54'),
(72,	'8f241f1109647a265.29938154',	'Standard rate',	'',	20.00,	'2023-05-31 11:41:54'),
(73,	'a7c40f63293c19d65.37472814',	'Reduce rate 1',	'',	10.00,	'2023-05-31 11:41:54'),
(74,	'a7c40f63293c19d65.37472814',	'Reduce rate 2',	'',	14.00,	'2023-05-31 11:41:54'),
(75,	'a7c40f63293c19d65.37472814',	'Standard rate',	'',	24.00,	'2023-05-31 11:41:54'),
(76,	'a7c40f632848c5217.53322339',	'Reduce rate 1',	'',	6.00,	'2023-05-31 11:41:54'),
(77,	'a7c40f632848c5217.53322339',	'Reduce rate 2',	'',	12.00,	'2023-05-31 11:41:54'),
(78,	'a7c40f632848c5217.53322339',	'Standard rate',	'',	25.00,	'2023-05-31 11:41:54'),
(79,	'a7c40f632a0804ab5.18804076',	'Reduce rate',	'',	5.00,	'2023-05-31 11:41:54'),
(80,	'a7c40f632a0804ab5.18804076',	'Standard rate',	'',	20.00,	'2023-05-31 11:41:54');