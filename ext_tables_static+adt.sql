DROP TABLE IF EXISTS tx_libconnect_domain_model_subject;

CREATE TABLE tx_libconnect_domain_model_subject (
    uid int(11) NOT NULL auto_increment,
    pid int(11) DEFAULT '0' NOT NULL,
    tstamp int(11) DEFAULT '0' NOT NULL,
    crdate int(11) DEFAULT '0' NOT NULL,
    cruser_id int(11) DEFAULT '0' NOT NULL,
    deleted tinyint(4) DEFAULT '0' NOT NULL,
    hidden tinyint(4) DEFAULT '0' NOT NULL,
    title tinytext NOT NULL,
    dbisid tinytext NOT NULL,
    ezbnotation tinytext NOT NULL,

    PRIMARY KEY (uid),
    KEY parent (pid)
);

--
-- Daten für Tabelle `tx_libconnect_domain_model_subject`
--

INSERT INTO `tx_libconnect_domain_model_subject` (`uid`, `pid`, `tstamp`, `crdate`, `cruser_id`, `deleted`, `hidden`, `title`, `dbisid`, `ezbnotation`) VALUES
(1, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 0, 0, 0, 'Agrar- und Forstwissenschaft, Gartenbau, Ernährungs- und Haushaltswissenschaft', '1', 'ZA-ZE'),
(2, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 0, 0, 0, 'Allgemeine Naturwissenschaft', '2', 'TA-TD'),
(3, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 0, 0, 0, 'Allgemeine und fachübergreifende Datenbanken', '3', ''),
(4, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 0, 0, 0, 'Allgemeine und fachübergreifende Zeitschriften', '', 'AZ'),
(5, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 0, 0, 0, 'Allgemeine und vergleichende Sprach- und Literaturwissenschaft. Indogermanistik. Außereuropäische Sprachen und Literaturen', '4', 'E'),
(6, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 0, 0, 0, 'Anglistik, Amerikanistik', '5', 'H'),
(7, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 0, 0, 0, 'Archäologie', '6', 'LD-LG'),
(8, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 0, 0, 0, 'Architektur, Bauingenieur- und Vermessungswesen', '7', 'ZH-ZI'),
(9, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 0, 0, 0, 'Biologie, Biotechnologie', '8', 'W'),
(10, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 0, 0, 0, 'Chemie und Pharmazie', '10', 'V'),
(11, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 0, 0, 0, 'Elektrotechnik, Elektronik, Nachrichtentechnik', '11', 'ZN'),
(12, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 0, 0, 0, 'Energietechnik', '12', 'ZP'),
(13, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 0, 0, 0, 'Ethnologie', '13', 'LA-LC'),
(14, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 0, 0, 0, 'Geographie', '14', 'R'),
(15, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 0, 0, 0, 'Geowissenschaften', '15', 'TE-TZ'),
(16, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 0, 0, 0, 'Germanistik. Niederlandistik. Skandinavistik', '16', 'G'),
(17, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 0, 0, 0, 'Geschichte', '17', 'N'),
(18, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 0, 0, 0, 'Geschichte der Pädagogik und des Bildungswesens', '18', 'DD'),
(19, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 0, 0, 0, 'Gesundheitswissenschaften', '19', 'MT'),
(20, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 0, 0, 0, 'Informatik', '20', 'SQ-SU'),
(21, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 0, 0, 0, 'Klassische Philologie. Byzantinistik. Mittellateinische und Neugriechische Philologie. Neulatein', '21', 'F'),
(22, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 0, 0, 0, 'Kunstgeschichte', '22', 'LH-LO'),
(23, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 0, 0, 0, 'Maschinenbau', '23', 'ZL'),
(24, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 0, 0, 0, 'Mathematik', '24', 'SA-SP'),
(25, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 0, 0, 0, 'Medien- und Kommunikationswissenschaften, Kommunikationsdesign', '25', 'AP'),
(26, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 0, 0, 0, 'Medizin', '26', 'WW-YZ'),
(27, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 0, 0, 0, 'Militärwissenschaft', '27', 'MX-MZ'),
(28, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 0, 0, 0, 'Musikwissenschaft', '28', 'LP-LZ'),
(29, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 0, 0, 0, 'Natur- und Umweltschutz', '29', 'AR'),
(30, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 0, 0, 0, 'Pädagogik', '30', 'D'),
(31, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 0, 0, 0, 'Philosophie', '31', 'CA-CK'),
(32, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 0, 0, 0, 'Physik', '32', 'U'),
(33, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 0, 0, 0, 'Politologie', '33', 'MA-MM'),
(34, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 0, 0, 0, 'Psychologie', '34', 'CL-CZ'),
(35, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 0, 0, 0, 'Rechtswissenschaft', '35', 'P'),
(36, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 0, 0, 0, 'Romanistik', '36', 'I'),
(37, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 0, 0, 0, 'Slavistik', '37', 'K'),
(38, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 0, 0, 0, 'Soziologie', '38', 'MN-MS'),
(39, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 0, 0, 0, 'Sport', '39', 'ZX-ZY'),
(40, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 0, 0, 0, 'Technik', '40', 'ZG'),
(41, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 0, 0, 0, 'Theologie und Religionswissenschaft', '41', 'B'),
(42, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 0, 0, 0, 'Werkstoffwissenschaften und Fertigungstechnik', '42', 'ZM'),
(43, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 0, 0, 0, 'Wirtschaftswissenschaften', '43', 'Q'),
(44, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 0, 0, 0, 'Wissenschaftskunde, Forschungs-, Hochschul-, Museumswesen', '44', 'AK-AL'),
(45, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 0, 0, 0, 'Asien-Afrika-Wissenschaften', 'AA', ''),
(46, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 0, 0, 0, 'Judaistik', '', 'JU');
