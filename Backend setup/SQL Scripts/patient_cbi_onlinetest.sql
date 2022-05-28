-- phpMyAdmin SQL Dump
-- version 5.0.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:2022
-- Generation Time: May 28, 2022 at 08:32 AM
-- Server version: 10.4.11-MariaDB
-- PHP Version: 7.4.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mits`
--

-- --------------------------------------------------------

--
-- Table structure for table `patient_cbi_onlinetest`
--

CREATE TABLE `patient_cbi_onlinetest` (
  `id` int(10) UNSIGNED NOT NULL,
  `added_by` bigint(20) NOT NULL,
  `Type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Question` varchar(1024) COLLATE utf8mb4_unicode_ci NOT NULL,
  `question_ml` varchar(2048) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Answer0` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Answer1` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Answer2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Answer3` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Answer4` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Answer5` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `question_order` int(11) NOT NULL DEFAULT 0,
  `status` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `patient_cbi_onlinetest`
--

INSERT INTO `patient_cbi_onlinetest` (`id`, `added_by`, `Type`, `Question`, `question_ml`, `Answer0`, `Answer1`, `Answer2`, `Answer3`, `Answer4`, `Answer5`, `question_order`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'Personal Burnout', 'How ofter do you feel tired?', 'Berapa kerap anda berasa letih?', NULL, '1', '2', '3', '4', '5', 1, '1', NULL, NULL),
(2, 1, 'Personal Burnout', 'How often are you physically exhausted?', 'Berapa kerap anda berasa letih secara fizikal?', NULL, '1', '2', '3', '4', '5', 2, '1', NULL, NULL),
(3, 1, 'Personal Burnout', 'How often are you emotionally exhausted?', 'Berapa kerap anda berasa letih secara emosi?', NULL, '1', '2', '3', '4', '5', 1, '1', NULL, NULL),
(4, 1, 'Personal Burnout', 'How often do you think: I can\'t take it anymore?', 'Berapa kerap anda berfikir: “Saya sudah tidak sanggup meneruskannya?”', NULL, '1', '2', '3', '4', '5', 2, '1', NULL, NULL),
(5, 1, 'Personal Burnout', 'How often do you feel worn out?', 'Berapa kerap anda berasa lesu?', NULL, '1', '2', '3', '4', '5', 1, '1', NULL, NULL),
(6, 1, 'Personal Burnout', 'How often do you feel weak and susceptible to illness?', 'Berapa kerap anda berasa lemah dan senang mendapat penyakit?', NULL, '1', '2', '3', '4', '5', 2, '1', NULL, NULL),
(7, 1, 'Work Burnout', 'Is your work emotionally exhausting?', 'Adakah kerja anda meletihkan emosi anda?', NULL, '1', '2', '3', '4', '5', 1, '1', NULL, NULL),
(8, 1, 'Work Burnout', 'Do you feel burn out because of your work?', 'Adakah anda berasa lesu upaya (burnout) disebabkan pekerjaan anda?', NULL, '1', '2', '3', '4', '5', 1, '1', NULL, NULL),
(9, 1, 'Work Burnout', 'Does your work frustate you?', 'Adakah anda berasa kecewa dengan pekerjaan anda?', NULL, '1', '2', '3', '4', '5', 1, '1', NULL, NULL),
(10, 1, 'Work Burnout', 'Do you feel worn out at the end of the working day?', 'Adakah anda berasa lesu pada akhir hari bekerja?', NULL, '1', '2', '3', '4', '5', 1, '1', NULL, NULL),
(11, 1, 'Work Burnout', 'Are you exhausted in the morning at the thought of another day at work?', 'Adakah anda keletihan pada waktu pagi apabila memikirkan sehari lagi di tempat kerja?', NULL, '1', '2', '3', '4', '5', 1, '1', NULL, NULL),
(12, 1, 'Work Burnout', 'Do you feel that every working hour is tiring for you?', 'Adakah anda berasa setiap waktu bekerja memenatkan anda?', NULL, '1', '2', '3', '4', '5', 1, '1', NULL, NULL),
(13, 1, 'Client/Customer Burnout', 'Do you find it hard to work with clients?', 'Adakah anda mengalami kesukaran untuk berurusan dengan klien?', NULL, '1', '2', '3', '4', '5', 1, '1', NULL, NULL),
(14, 1, 'Client/Customer Burnout', 'Do you find it frustrating to work with clients?', 'Adakah anda berasa berurusan dengan klien mengecewakan?', NULL, '1', '2', '3', '4', '5', 1, '1', NULL, NULL),
(15, 1, 'Client/Customer Burnout', 'Does it drain your energy to work with clients?', 'Adakah bekerja dengan klien menghabiskan tenaga anda?', NULL, '1', '2', '3', '4', '5', 1, '1', NULL, NULL),
(16, 1, 'Client/Customer Burnout', 'Do you feel that you give more than you get back when you with clients?', 'Apabila bekerja dengan klien, adakah anda berasa lebih banyak memberi daripada menerima?', NULL, '1', '2', '3', '4', '5', 1, '1', NULL, NULL),
(17, 1, 'Client/Customer Burnout', 'Do you sometimes wonder how long you will be able to continue working with clients?', 'Adakah anda tertanya-tanya sejauh mana anda mampu meneruskan urusan dengan klien?', NULL, '1', '2', '3', '4', '5', 1, '1', NULL, NULL),
(18, 1, 'DASS', 'I found It hard to wind down', 'Saya dapati diri saya sukar ditenteramkan.', NULL, '1', '2', '3', NULL, NULL, 1, '1', NULL, NULL),
(19, 1, 'DASS', 'I was aware of dryness of my mouth', 'Saya sedar mulut saya terasa kering.', NULL, '1', '2', '3', NULL, NULL, 1, '1', NULL, NULL),
(20, 1, 'DASS', 'I couldn\'t seem to experience any positive feeling at all', 'Saya tidak dapat mengalami perasaan positif sama sekali.', NULL, '1', '2', '3', NULL, NULL, 1, '1', NULL, NULL),
(21, 1, 'DASS', 'I experienced breating difficulty (eg, excessively rapid breathing, breathlessness absenced of physical exertion)', 'Saya mengalami kesukaran bernafas (contohnya pernafasan yang laju, tercungap-cungap walaupun tidak melakukan senaman fizikal).', NULL, '1', '2', '3', NULL, NULL, 1, '1', NULL, NULL),
(22, 1, 'DASS', 'I found it difficult to work up the initiative to do things', 'Saya sukar untuk mendapatkan semangat bagi melakukan sesuatu perkara.', NULL, '1', '2', '3', NULL, NULL, 1, '1', NULL, NULL),
(23, 1, 'DASS', 'I tended to over-react to situations', 'Saya cenderung untuk bertindak keterlaluan dalam sesuatu keadaan.', NULL, '1', '2', '3', NULL, NULL, 1, '1', NULL, NULL),
(24, 1, 'DASS', 'I experienced trembling (eg, In the hands)', 'Saya rasa menggeletar (contohnya pada tangan).', NULL, '1', '2', '3', NULL, NULL, 1, '1', NULL, NULL),
(25, 1, 'DASS', 'I felt that I was using a lot of nervous energy', 'Saya rasa saya menggunakan banyak tenaga dalam keadaan cemas.', NULL, '1', '2', '3', NULL, NULL, 1, '1', NULL, NULL),
(26, 1, 'DASS', 'I was worried about situations in which might panic and make a fool of myself', 'Saya bimbang keadaan di mana saya mungkin menjadi panik dan melakukan perkara yang membodohkan diri sendiri.', NULL, '1', '2', '3', NULL, NULL, 1, '1', NULL, NULL),
(27, 1, 'DASS', 'I felt that I had nothing to look forward to', 'Saya rasa saya tidak mempunyai apa-apa untuk diharapkan.', NULL, '1', '2', '3', NULL, NULL, 1, '1', NULL, NULL),
(28, 1, 'DASS', 'I found myself getting agigate', 'Saya dapati diri saya semakin gelisah.', NULL, '1', '2', '3', NULL, NULL, 1, '1', NULL, NULL),
(29, 1, 'DASS', 'I found difficult to relax', 'Saya rasa sukar untuk relaks.', NULL, '1', '2', '3', NULL, NULL, 1, '1', NULL, NULL),
(30, 1, 'DASS', 'I felt down-hearted and blue', 'Saya rasa sedih dan murung.', NULL, '1', '2', '3', NULL, NULL, 1, '1', NULL, NULL),
(31, 1, 'DASS', 'I was intolerant of anything that kept me from getting on with what I was doing', 'Saya tidak dapat menahan sabar dengan perkara yang menghalang saya meneruskan apa yang saya lakukan.', NULL, '1', '2', '3', NULL, NULL, 1, '1', NULL, NULL),
(32, 1, 'DASS', 'I felt I was close to panic', 'Saya rasa hampir-hampir menjadi panik/cemas.', NULL, '1', '2', '3', NULL, NULL, 1, '1', NULL, NULL),
(33, 1, 'DASS', 'I was unable to become enthusiastic about anything', 'Saya tidak bersemangat dengan apa jua yang saya lakukan', NULL, '1', '2', '3', NULL, NULL, 1, '1', NULL, NULL),
(34, 1, 'DASS', 'I felt I wasn\'t worth much as a person', 'Saya rasa tidak begitu berharga sebagai seorang individu.', NULL, '1', '2', '3', NULL, NULL, 1, '1', NULL, NULL),
(35, 1, 'DASS', 'I felt that I was rather touchy', 'Saya rasa saya mudah tersentuh.', NULL, '1', '2', '3', NULL, NULL, 1, '1', NULL, NULL),
(36, 1, 'DASS', 'I was aware of the action of my heart in the absence of physical exertin (eg, sen heart rate Increase, heart missing a beat', 'Saya sedar tindakbalas jantung saya walaupun tidak melakukan aktiviti fizikal (contohnya kadar denyutan jantung bertambah, atau denyutan jantung berkurangan).', NULL, '1', '2', '3', NULL, NULL, 1, '1', NULL, NULL),
(37, 1, 'DASS', 'I felt scared without any good reason', 'Saya berasa takut tanpa sebab yang munasabah.', NULL, '1', '2', '3', NULL, NULL, 1, '1', NULL, NULL),
(38, 1, 'DASS', 'I felt that life was meaningless', 'Saya rasa hidup ini tidak bermakna.', NULL, '1', '2', '3', NULL, NULL, 1, '1', NULL, NULL),
(39, 1, 'PHQ-9', 'Little interest or pleasure in doing things', NULL, NULL, '1', '2', '3', NULL, NULL, 1, '1', NULL, NULL),
(40, 1, 'PHQ-9', 'Feeling down, depressed or hopeless', NULL, NULL, '1', '2', '3', NULL, NULL, 1, '1', NULL, NULL),
(41, 1, 'PHQ-9', 'Trouble falling/staying asleep, sleeping too much', NULL, NULL, '1', '2', '3', NULL, NULL, 1, '1', NULL, NULL),
(42, 1, 'PHQ-9', 'Feeling tired or having little energy', NULL, NULL, '1', '2', '3', NULL, NULL, 1, '1', NULL, NULL),
(43, 1, 'PHQ-9', 'Poor appetite or over eating', NULL, NULL, '1', '2', '3', NULL, NULL, 1, '1', NULL, NULL),
(44, 1, 'PHQ-9', 'Feeling bad about yourself- or that you are a failure or have let yourslef or yo your family down', NULL, NULL, '1', '2', '3', NULL, NULL, 1, '1', NULL, NULL),
(45, 1, 'PHQ-9', 'Trouble concentrating on things, such as reading the newspaper or watching television', NULL, NULL, '1', '2', '3', NULL, NULL, 1, '1', NULL, NULL),
(46, 1, 'PHQ-9', 'Moving or speaking so slowly that people could have noticed. Or the opposite being so fidgety or restless that you have been moving around a lot more than usual', NULL, NULL, '1', '2', '3', NULL, NULL, 1, '1', NULL, NULL),
(47, 1, 'PHQ-9', 'Thoughts that you would be better off dead or of hurting yourself in some way', NULL, NULL, '1', '2', '3', NULL, NULL, 1, '1', NULL, NULL),
(48, 1, 'Understanding & Communication', 'Concentrating on doing something for ten minutes?', NULL, NULL, '1', '2', '3', '4', '5', 1, '1', NULL, NULL),
(49, 1, 'Understanding & Communication', 'Remembering to do important things', NULL, NULL, '1', '2', '3', '4', '5', 1, '1', NULL, NULL),
(50, 1, 'Understanding & Communication', 'Analyzing and finding solutions to problems in day-to-day lil', NULL, NULL, '1', '2', '3', '4', '5', 1, '1', NULL, NULL),
(51, 1, 'Understanding & Communication', 'Learning a new task, for example, learning how to get to a r place?', NULL, NULL, '1', '2', '3', '4', '5', 1, '1', NULL, NULL),
(52, 1, 'Understanding & Communication', 'Generally understanding what people say?', NULL, NULL, '1', '2', '3', '4', '5', 1, '1', NULL, NULL),
(53, 1, 'Understanding & Communication', 'Starting and maintaining a conversation?', NULL, NULL, '1', '2', '3', '4', '5', 1, '1', NULL, NULL),
(54, 1, 'GA', 'Standing for long periods, such as 30 minutes?', NULL, NULL, '1', '2', '3', '4', '5', 1, '1', NULL, NULL),
(55, 1, 'GA', 'Standing up from sitting down?', NULL, NULL, '1', '2', '3', '4', '5', 1, '1', NULL, NULL),
(56, 1, 'GA', 'Moving around inside your home?', NULL, NULL, '1', '2', '3', '4', '5', 1, '1', NULL, NULL),
(57, 1, 'GA', 'Walking a long distance, such as a kilometer(or equivalent)?', NULL, NULL, '1', '2', '3', '4', '5', 1, '1', NULL, NULL),
(58, 1, 'GA', 'Are you exhausted in the morning at the thought of another day at work?', NULL, NULL, '1', '2', '3', '4', '5', 1, '1', NULL, NULL),
(59, 1, 'SC', 'Washing your whole body?', NULL, NULL, '1', '2', '3', '4', '5', 1, '1', NULL, NULL),
(60, 1, 'SC', 'Getting dressed?', NULL, NULL, '1', '2', '3', '4', '5', 1, '1', NULL, NULL),
(61, 1, 'SC', 'Eating?', NULL, NULL, '1', '2', '3', '4', '5', 1, '1', NULL, NULL),
(62, 1, 'SC', 'Staying by yourself for a few days?', NULL, NULL, '1', '2', '3', '4', '5', 1, '1', NULL, NULL),
(63, 1, 'GAWP', 'Dealing with people you do not know?', NULL, NULL, '1', '2', '3', '4', '5', 1, '1', NULL, NULL),
(64, 1, 'GAWP', 'Maintaining a friendship?', NULL, NULL, '1', '2', '3', '4', '5', 1, '1', NULL, NULL),
(65, 1, 'GAWP', 'Getting along with people who are close to you?', NULL, NULL, '1', '2', '3', '4', '5', 1, '1', NULL, NULL),
(66, 1, 'GAWP', 'Making new friends?', NULL, NULL, '1', '2', '3', '4', '5', 1, '1', NULL, NULL),
(67, 1, 'GAWP', 'Sexual activities?', NULL, NULL, '1', '2', '3', '4', '5', 1, '1', NULL, NULL),
(68, 1, 'LA-H', 'Taking care of your household responsibilities?', NULL, NULL, '1', '2', '3', '4', '5', 1, '1', NULL, NULL),
(69, 1, 'LA-H', 'Doing most important household tasks well?', NULL, NULL, '1', '2', '3', '4', '5', 1, '1', NULL, NULL),
(70, 1, 'LA-H', 'Getting all of the household work done that you needed to do?', NULL, NULL, '1', '2', '3', '4', '5', 1, '1', NULL, NULL),
(71, 1, 'LA-H', 'Getting your household work done as quickly as needed?', NULL, NULL, '1', '2', '3', '4', '5', 1, '1', NULL, NULL),
(72, 1, 'LA-S/W', 'Your day-to-day work/school?', NULL, NULL, '1', '2', '3', '4', '5', 1, '1', NULL, NULL),
(73, 1, 'LA-S/W', 'Doing your most important work/school tasks well?', NULL, NULL, '1', '2', '3', '4', '5', 1, '1', NULL, NULL),
(74, 1, 'LA-S/W', 'Getting all of the work done that you need to do?', NULL, NULL, '1', '2', '3', '4', '5', 1, '1', NULL, NULL),
(75, 1, 'LA-S/W', 'Getting your work done as quickly as needed?', NULL, NULL, '1', '2', '3', '4', '5', 1, '1', NULL, NULL),
(76, 1, 'PIS', 'How much of a problem did you have in joining in communiff activities(for example, festivities, religious, or other activities) in the same way as anyone else can.', NULL, NULL, '1', '2', '3', '4', '5', 1, '1', NULL, NULL),
(77, 1, 'PIS', 'HOW much of a problem did you have because of barriers hindrances around you?', NULL, NULL, '1', '2', '3', '4', '5', 1, '1', NULL, NULL),
(78, 1, 'PIS', 'How much of a problem did you have living with dignity because of the attitudes and actions of others?', NULL, NULL, '1', '2', '3', '4', '5', 1, '1', NULL, NULL),
(79, 1, 'PIS', 'How much time did you spend on your health condition or consequences?', NULL, NULL, '1', '2', '3', '4', '5', 1, '1', NULL, NULL),
(80, 1, 'PIS', 'How much have you been emotionally affected by your health condition?', NULL, NULL, '1', '2', '3', '4', '5', 1, '1', NULL, NULL),
(81, 1, 'PIS', 'How much has your health been a drain on the financial resources of you or your family?', NULL, NULL, '1', '2', '3', '4', '5', 1, '1', NULL, NULL),
(82, 1, 'PIS', 'How much of a problem did your family have because of your health problems?', NULL, NULL, '1', '2', '3', '4', '5', 1, '1', NULL, NULL),
(83, 1, 'PIS', 'How much of a problem did you have in doing things by yourself for relaxation or pleasure?', NULL, NULL, '1', '2', '3', '4', '5', 1, '1', NULL, NULL),
(84, 1, 'BDI', 'Getting all of the work done that you need to do?', NULL, '0 I do not feel sad.', '1 I feel sad.', '2 I am sad all the time and I can\'t snap out of it.', '3 I am so sad and unhappy that I can\'t stand it.', NULL, NULL, 1, '1', NULL, NULL),
(85, 1, 'LA-S/W', 'Your day-to-day work/school?', NULL, NULL, '1', '2', '3', '4', '5', 1, '1', NULL, NULL),
(86, 1, 'LA-S/W', 'Doing your most important work/school tasks well?', NULL, NULL, '1', '2', '3', '4', '5', 1, '1', NULL, NULL),
(87, 1, 'LA-S/W', 'Getting all of the work done that you need to do?', NULL, NULL, '1', '2', '3', '4', '5', 1, '1', NULL, NULL),
(88, 1, 'LA-S/W', 'Getting your work done as quickly as needed?', NULL, NULL, '1', '2', '3', '4', '5', 1, '1', NULL, NULL),
(89, 1, 'PIS', 'How much of a problem did you have in joining in communiff activities(for example, festivities, religious, or other activities) in the same way as anyone else can.', NULL, NULL, '1', '2', '3', '4', '5', 1, '1', NULL, NULL),
(90, 1, 'PIS', 'HOW much of a problem did you have because of barriers hindrances around you?', NULL, NULL, '1', '2', '3', '4', '5', 1, '1', NULL, NULL),
(91, 1, 'PIS', 'How much of a problem did you have living with dignity because of the attitudes and actions of others?', NULL, NULL, '1', '2', '3', '4', '5', 1, '1', NULL, NULL),
(92, 1, 'PIS', 'How much time did you spend on your health condition or consequences?', NULL, NULL, '1', '2', '3', '4', '5', 1, '1', NULL, NULL),
(93, 1, 'PIS', 'How much have you been emotionally affected by your health condition?', NULL, NULL, '1', '2', '3', '4', '5', 1, '1', NULL, NULL),
(94, 1, 'PIS', 'How much has your health been a drain on the financial resources of you or your family?', NULL, NULL, '1', '2', '3', '4', '5', 1, '1', NULL, NULL),
(95, 1, 'PIS', 'How much of a problem did your family have because of your health problems?', NULL, NULL, '1', '2', '3', '4', '5', 1, '1', NULL, NULL),
(96, 1, 'PIS', 'How much of a problem did you have in doing things by yourself for relaxation or pleasure?', NULL, NULL, '1', '2', '3', '4', '5', 1, '1', NULL, NULL),
(97, 1, 'BDI', '01', NULL, '0 I do not feel sad.', '1 I feel sad.', '2 I am sad all the time and I can\'t snap out of it.', '3 I am so sad and unhappy that I can\'t stand it.', NULL, NULL, 1, '1', NULL, NULL),
(98, 1, 'BDI', '02', NULL, '0 I am not particularly discouraged about the future.', '1 I feel discouraged about the future.', '2 I feel I have nothing to look forward to.', '3 I feel the future is hopeless and that things cannot improve.', NULL, NULL, 1, '1', NULL, NULL),
(99, 1, 'BDI', '03', NULL, '0 I am sad all the time and I can\'t snap out of it.', '1 I feel I have failed more than the average person.', '2 As I look back on my life, all I can see is a lot of failures.', '3 I feel I am a complete failure as a person.', NULL, NULL, 1, '1', NULL, NULL),
(100, 1, 'BDI', '04', NULL, '0 I get as much satisfaction out of things as I used to.', '1 I don\'t enjoy things the way I used to.', '2 I don\'t get real satisfaction out of anything anymore.', '3 I am dissatisfied or bored with everything.', NULL, NULL, 1, '1', NULL, NULL),
(101, 1, 'BDI', '05', NULL, '0 I don\'t feel particularly guilty.', '1 I feel guilty a good part of the time.', '2 I feel quite guilty most of the time.', '3 I feel guilty all of the time.\r\n', NULL, NULL, 1, '1', NULL, NULL),
(102, 1, 'BDI', '06', NULL, '0 I don\'t feel I am being punished.', '1 I feel I may be punished.', '2 I expect to be punished.', '3 I feel I am being punished.', NULL, NULL, 1, '1', NULL, NULL),
(103, 1, 'BDI', '07', NULL, '0 I don\'t feel disappointed in myself.', '1 I am disappointed in myself.', '2 I am disgusted with myself.', '3 I hate myself.', NULL, NULL, 1, '1', NULL, NULL),
(104, 0, 'sss', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, '1', NULL, NULL),
(105, 1, 'BDI', '08', NULL, '0 I don\'t feel I am any worse than anybody else.', '1 I am critical of myself for my weaknesses or mistakes.', '2 I blame myself all the time for my faults.', '3 I blame myself for everything bad that happens.', NULL, NULL, 0, '1', NULL, NULL),
(106, 1, 'BDI', '09', NULL, '0 I don\'t have any thoughts of killing myself.', '1 I have thoughts of killing myself, but I would not carry them out.', '2 I would like to kill myself.', '3 I would kill myself if I had the chance.', NULL, NULL, 1, '1', NULL, NULL),
(107, 1, 'BDI', '10', NULL, '0 I don\'t cry any more than usual.', '1 I cry more now than I used to.', '2 I cry all the time now.', '3 I used to be able to cry, but now I can\'t cry even though I want to.', NULL, NULL, 0, '1', NULL, NULL),
(108, 1, 'BDI', '10', NULL, '0 I don\'t cry any more than usual.', '1 I cry more now than I used to.', '2 I cry all the time now.', '3 I used to be able to cry, but now I can\'t cry even though I want to.', NULL, NULL, 0, '1', NULL, NULL),
(109, 1, 'BDI', '11', NULL, '0 I am no more irritated by things than I ever was.', '1 I am slightly more irritated now than usual.', '2 I am quite annoyed or irritated a good deal of the time.', '3 I feel irritated all the time.', NULL, NULL, 0, '1', NULL, NULL),
(110, 1, 'BDI', '12', NULL, '0 I have not lost interest in other people.', '1 I am less interested in other people than I used to be.', '2 I have lost most of my interest in other people.', '3 I have lost all of my interest in other people.', NULL, NULL, 0, '1', NULL, NULL),
(111, 1, 'BDI', '13', NULL, '0 I make decisions about as well as I ever could.', '1 I put off making decisions more than I used to.', '2 I have greater difficulty in making decisions more than I used to.', '3 I can\'t make decisions at all anymore.', NULL, NULL, 0, '1', NULL, NULL),
(112, 1, 'BDI', '14', NULL, '0 I don\'t feel that I look any worse than I used to.', '1 I am worried that I am looking old or unattractive.', '2 I feel there are permanent changes in my appearance that make me look unattractive.', '3 I believe that I look ugly.', NULL, NULL, 0, '1', NULL, NULL),
(113, 1, 'BDI', '15', NULL, '0 I can work about as well as before.', '1 It takes an extra effort to get started at doing something.', '2 I have to push myself very hard to do anything.', '3 I can\'t do any work at all.', NULL, NULL, 0, '1', NULL, NULL),
(114, 1, 'BDI', '16', NULL, '0 I can sleep as well as usual.', '1 I don\'t sleep as well as I used to.', '2 I wake up 1-2 hours earlier than usual and find it hard to get back to sleep.', '3 I wake up several hours earlier than I used to and cannot get back to sleep.', NULL, NULL, 0, '1', NULL, NULL),
(115, 1, 'BDI', '17', NULL, '0 I don\'t get more tired than usual.', '1 I get tired more easily than I used to.', '2 I get tired from doing almost anything.', '3 I am too tired to do anything.', NULL, NULL, 0, '1', NULL, NULL),
(116, 1, 'BDI', '18', NULL, '0 My appetite is no worse than usual.', '1 My appetite is not as good as it used to be.', '2 My appetite is much worse now.', '3 I have no appetite at all anymore.', NULL, NULL, 0, '1', NULL, NULL),
(117, 1, 'BDI', '19', NULL, '0 I haven\'t lost much weight, if any, lately.', '1 I have lost more than five pounds.', '2 I have lost more than ten pounds.', '3 I have lost more than fifteen pounds.', NULL, NULL, 0, '1', NULL, NULL),
(118, 1, 'BDI', '20', NULL, '0 I am no more worried about my health than usual.', '1 I am worried about physical problems like aches, pains, upset stomach, or constipation.', '2 I am very worried about physical problems and it\'s hard to think of much else.', '3 I am so worried about my physical problems that I cannot think of anything else.', NULL, NULL, 0, '1', NULL, NULL),
(119, 1, 'BAI', '1. Numbness', NULL, '0', '1', '2', '3', NULL, NULL, 0, '1', NULL, NULL),
(120, 1, 'BAI', '2. Feeling hot', NULL, '0', '1', '2', '3', NULL, NULL, 0, '1', NULL, NULL),
(121, 1, 'BAI', '3. Wobbliness in legs', NULL, '0', '1', '2', '3', NULL, NULL, 0, '1', NULL, NULL),
(122, 1, 'BAI', '4. Unable to relax', NULL, '0', '1', '2', '3', NULL, NULL, 0, '1', NULL, NULL),
(123, 1, 'BAI', '5. Fear of worst happening', NULL, '0', '1', '2', '3', NULL, NULL, 0, '1', NULL, NULL),
(124, 1, 'BAI', '6. Dizzy or lightheaded', NULL, '0', '1', '2', '3', NULL, NULL, 0, '1', NULL, NULL),
(125, 1, 'BAI', '7. Heart pounding/racing', NULL, '0', '1', '2', '3', NULL, NULL, 0, '1', NULL, NULL),
(126, 1, 'BAI', '8. Unsteady', NULL, '0', '1', '2', '3', NULL, NULL, 0, '1', NULL, NULL),
(127, 1, 'BAI', '9. Terrified or afraid', NULL, '0', '1', '2', '3', NULL, NULL, 0, '1', NULL, NULL),
(128, 1, 'BAI', '10. Nervous', NULL, '0', '1', '2', '3', NULL, NULL, 0, '1', NULL, NULL),
(129, 1, 'BAI', '11. Feeling of choking', NULL, '0', '1', '2', '3', NULL, NULL, 0, '1', NULL, NULL),
(130, 1, 'BAI', '12. Hands trembling', NULL, '0', '1', '2', '3', NULL, NULL, 0, '1', NULL, NULL),
(131, 1, 'BAI', '13. Shaky/unsteady', NULL, '0', '1', '2', '3', NULL, NULL, 0, '1', NULL, NULL),
(132, 1, 'BAI', '14. Fear of losing control', NULL, '0', '1', '2', '3', NULL, NULL, 0, '1', NULL, NULL),
(133, 1, 'BAI', '15. Difficulty in breathing', NULL, '0', '1', '2', '3', NULL, NULL, 0, '1', NULL, NULL),
(134, 1, 'BAI', '16. Fear of dying', NULL, '0', '1', '2', '3', NULL, NULL, 0, '1', NULL, NULL),
(135, 1, 'BAI', '17. Scared', NULL, '0', '1', '2', '3', NULL, NULL, 0, '1', NULL, NULL),
(136, 1, 'BAI', '18. Indigestion', NULL, '0', '1', '2', '3', NULL, NULL, 0, '1', NULL, NULL),
(137, 1, 'BAI', '19. Faint/lightheaded', NULL, '0', '1', '2', '3', NULL, NULL, 0, '1', NULL, NULL),
(138, 1, 'BAI', '20. Face flushed', NULL, '0', '1', '2', '3', NULL, NULL, 0, '1', NULL, NULL),
(139, 1, 'BAI', '21. Hot/cold sweats', NULL, '0', '1', '2', '3', NULL, NULL, 0, '1', NULL, NULL),
(140, 1, 'ATQ', '1. I am not good.', NULL, '0', '1', '2', '3', '4', '5', 0, '1', NULL, NULL),
(141, 1, 'ATQ', '2. Why can\'t I ever succeed?', NULL, '0', '1', '2', '3', '4', '5', 0, '1', NULL, NULL),
(142, 1, 'ATQ', '3. No one understands me.', NULL, '0', '1', '2', '3', '4', '5', 0, '1', NULL, NULL),
(143, 1, 'ATQ', '4. I don\'t think I can go on.', NULL, '0', '1', '2', '3', '4', '5', 0, '1', NULL, NULL),
(144, 1, 'ATQ', '5. Nothing feels good anymore.', NULL, '0', '1', '2', '3', '4', '5', 0, '1', NULL, NULL),
(145, 1, 'ATQ', '6. I can\'t stand this anymore.', NULL, '0', '1', '2', '3', '4', '5', 0, '1', NULL, NULL),
(146, 1, 'ATQ', '7. I can\'t get started.', NULL, '0', '1', '2', '3', '4', '5', 0, '1', NULL, NULL),
(147, 1, 'ATQ', '8. What\'s wrong with me?', NULL, '0', '1', '2', '3', '4', '5', 0, '1', NULL, NULL),
(148, 1, 'ATQ', '9. I can\'t get things together.', NULL, '0', '1', '2', '3', '4', '5', 0, '1', NULL, NULL),
(149, 1, 'ATQ', '10. I wish I could disappear.', NULL, '0', '1', '2', '3', '4', '5', 0, '1', NULL, NULL),
(150, 1, 'ATQ', '11. What\'s the matter with me?', NULL, '0', '1', '2', '3', '4', '5', 0, '1', NULL, NULL),
(151, 1, 'ATQ', '12. I am worthless.', NULL, '0', '1', '2', '3', '4', '5', 0, '1', NULL, NULL),
(152, 1, 'ATQ', '13. My future is bleak.', NULL, '0', '1', '2', '3', '4', '5', 0, '1', NULL, NULL),
(153, 1, 'ATQ', '14. I feel so helpless.', NULL, '0', '1', '2', '3', '4', '5', 0, '1', NULL, NULL),
(154, 1, 'ATQ', '15. There must be something wrong with me.', NULL, '0', '1', '2', '3', '4', '5', 0, '1', NULL, NULL),
(155, 1, 'ATQ', '16. It\'s just not worth it.', NULL, '0', '1', '2', '3', '4', '5', 0, '1', NULL, NULL),
(156, 1, 'ATQ', '17. I can\'t finish anything.', NULL, '0', '1', '2', '3', '4', '5', 0, '1', NULL, NULL),
(157, 1, 'PSP', 'Socially useful activities, including work or academic study', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, '1', NULL, NULL),
(158, 1, 'PSP', 'Personal and social relationships', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, '1', NULL, NULL),
(159, 1, 'PSP', 'Self-care', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, '1', NULL, NULL),
(160, 1, 'PSP', 'Disturbing and aggressive behaviors', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, '1', NULL, NULL),
(161, 1, 'Suicidal Scale', '01. Isolation', NULL, 'Somebody present', 'Somebody nearby, or in visual or vocal contact', 'No one nearby or in visual or vocal contact', NULL, NULL, NULL, 0, '1', NULL, NULL),
(162, 1, 'Suicidal Scale', '02. Timing', NULL, 'Intervention is probable', 'Intervention is not likely', 'Intervention is highly unlikely', NULL, NULL, NULL, 0, '1', NULL, NULL),
(163, 1, 'Suicidal Scale', '03. Precautions against discvery/intervention', NULL, 'No precautions', 'Passive precautions (eg; alone in room with unlocked door', 'Active precautions (as locked door)', NULL, NULL, NULL, 0, '1', NULL, NULL),
(164, 1, 'Suicidal Scale', '04. Acting to get help during/after attempt', NULL, 'Notified potential helper regarding attempt', 'Second default radioContacted but did not specifically notify regarding attempt', 'Did not contact or notify potential helper', NULL, NULL, NULL, 0, '1', NULL, NULL),
(165, 1, 'Suicidal Scale', '05. Final acts in anticipating of death (will, gifts, insurance)', NULL, 'None', 'Thought about or made some arrangements', 'Made definite plans or completed arrangements', NULL, NULL, NULL, 0, '1', NULL, NULL),
(166, 1, 'Suicidal Scale', '06. Active preparation for attempt', NULL, 'None', 'Minimal to moderate', 'Extensive', NULL, NULL, NULL, 0, '1', NULL, NULL),
(167, 1, 'Suicidal Scale', '07. Suicide note', NULL, 'Absence of note', 'Note written, but torn up; note thought about', 'Presence of note', NULL, NULL, NULL, 0, '1', NULL, NULL),
(168, 1, 'Suicidal Scale', '08. Overt communication of intent before the attempt', NULL, 'None', 'Equivocal communication', 'Unequivocal communication', NULL, NULL, NULL, 0, '1', NULL, NULL),
(169, 1, 'Suicidal Scale', '09. Allged purpose of attempt', NULL, 'To manipulate environment, get attention, get revenge', 'Components of above and below', 'Disabled radioTo escape, surcease, solve problems', NULL, NULL, NULL, 0, '1', NULL, NULL),
(170, 1, 'Suicidal Scale', '10. Expectations of fatality', NULL, 'Thought that death was unlikely', 'Thought that death was possible but not probable', 'Thought that death was probable or certain', NULL, NULL, NULL, 0, '1', NULL, NULL),
(171, 1, 'Suicidal Scale', '11. Conception of method\'s lethality', NULL, 'Did less to self that s/he thought would be lethal', 'Wasn\'t sure if what s/he did would be lethal', 'Equaled or exceed what s/he thought would be lethal', NULL, NULL, NULL, 0, '1', NULL, NULL),
(172, 1, 'Suicidal Scale', '12. Seriousness of attempt', NULL, 'Did not seriously attempt to end life', 'Uncertain about seriousness to end life', 'Seriously attempted to end life', NULL, NULL, NULL, 0, '1', NULL, NULL),
(173, 1, 'Suicidal Scale', '13. Attitude towards living/dying', NULL, 'Did not want to die', 'Components of above and below', 'Wanted to die', NULL, NULL, NULL, 0, '1', NULL, NULL),
(174, 1, 'Suicidal Scale', '14. Conception of medical rescuability', NULL, 'Thought death would be unlikely if received medical attentionThought death would be unlikely if received medical attention', 'Was uncertain if death could be averted by medical attention', 'Was certain of death even if received medical attention', NULL, NULL, NULL, 0, '1', NULL, NULL),
(175, 1, 'Suicidal Scale', '15. Degree of premeditation', NULL, 'None; impulsive', 'Suicide contemplated for 3 hours or less prior to attempt', 'Disabled radioSuicide contemplated for more than 3 hours prior to attempt', NULL, NULL, NULL, 0, '1', NULL, NULL),
(176, 1, 'BDI', '21', NULL, '0 I have not noticed any recent change in my interest in sex.', '1 I am less interested in sex than I used to be.', '2 I have almost no interest in sex.', '3 I have lost interest in sex completely.', NULL, NULL, 0, '1', NULL, NULL),
(177, 1, 'Personal Burnout', 'Do you have enough energy for family and friends during leisure time?', 'Adakah anda keletihan pada waktu pagi apabila memikirkan sehari lagi di tempat kerja?', '0', '1', '2', '3', '4', '5', 1, '1', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `patient_cbi_onlinetest`
--
ALTER TABLE `patient_cbi_onlinetest`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `patient_cbi_onlinetest`
--
ALTER TABLE `patient_cbi_onlinetest`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=178;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
