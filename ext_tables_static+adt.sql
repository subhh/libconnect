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
(1, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 0, 0, 0, 'Biologie', '5', 'W'),
(2, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 0, 0, 0, 'Allgemein / Fachübergreifend', '28', 'AZ'),
(3, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 0, 0, 0, 'Allgemeine und vergleichende Sprach- und Literaturwissenschaft', '13', 'E'),
(4, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 0, 0, 0, 'Anglistik, Amerikanistik', '12', 'H'),
(5, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 0, 0, 0, 'Archäologie', '27', 'LD-LG'),
(6, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 0, 0, 0, 'Architektur, Bauingenieur- und Vermessungswesen', '45', 'ZH-ZI'),
(7, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 0, 0, 0, 'Chemie', '3', 'V'),
(8, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 0, 0, 0, 'Elektrotechnik, Mess- und Regelungstechnik', '46', 'ZN'),
(9, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 0, 0, 0, 'Energie, Umweltschutz, Kerntechnik', '47', 'ZP'),
(10, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 0, 0, 0, 'Ethnologie (Volks- und Völkerkunde)', '29', 'LA-LC'),
(11, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 0, 0, 0, 'Geographie', '6', 'R'),
(12, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 0, 0, 0, 'Geowissenschaften', '7', 'TE-TZ'),
(13, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 0, 0, 0, 'Germanistik, Niederländische Philologie, Skandinavistik', '11', 'G'),
(14, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 0, 0, 0, 'Geschichte', '26', 'N'),
(15, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 0, 0, 0, 'Informatik', '30', 'SQ-SU'),
(16, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 0, 0, 0, 'Informations-, Buch- und Bibliothekswesen, Handschriftenkunde', '54', 'AN'),
(17, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 0, 0, 0, 'Klassische Philologie', '9', 'F'),
(18, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 0, 0, 0, 'Kunstgeschichte', '24', 'LH-LO'),
(19, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 0, 0, 0, 'Land- und Forstwirtschaft, Gartenbau, Fischereiwirtschaft, Hauswirtschaft, Ernährung', '48', 'ZA-ZE'),
(20, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 0, 0, 0, 'Maschinenwesen, Werkstoffwissenschaften, Fertigungstechnik, Bergbau und Hüttenwesen, Verkehrstechnik, Feinwerktechnik', '49', 'ZL'),
(21, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 0, 0, 0, 'Mathematik', '2', 'SA-SP'),
(22, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 0, 0, 0, 'Medien- und Kommunikationswissenschaften, Publizistik, Film- und Theaterwissenschaft', '53', 'AP'),
(23, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 0, 0, 0, 'Medizin', '8', 'WW-YZ'),
(24, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 0, 0, 0, 'Musikwissenschaft', '25', 'LP-LZ'),
(25, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 0, 0, 0, 'Naturwissenschaft allgemein', '50', 'TA-TD'),
(26, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 0, 0, 0, 'Pädagogik', '23', 'D'),
(27, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 0, 0, 0, 'Pharmazie', '4', '-'),
(28, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 0, 0, 0, 'Philosophie', '21', 'CA-CI'),
(29, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 0, 0, 0, 'Physik', '1', 'U'),
(30, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 0, 0, 0, 'Politologie', '17', 'MA-MM'),
(31, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 0, 0, 0, 'Psychologie', '22', 'CL-CZ'),
(32, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 0, 0, 0, 'Rechtswissenschaft', '15', 'P'),
(33, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 0, 0, 0, 'Romanistik', '10', 'I'),
(34, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 0, 0, 0, 'Slavistik', '51', 'K'),
(35, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 0, 0, 0, 'Soziologie', '18', 'MN-MS'),
(36, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 0, 0, 0, 'Sport', '20', 'ZX-ZY'),
(37, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 0, 0, 0, 'Technik allgemein', '44', 'ZG'),
(38, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 0, 0, 0, 'Theologie und Religionswissenschaft', '19', 'B'),
(39, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 0, 0, 0, 'Verfahrenstechnik, Biotechnologie, Lebensmitteltechnologie', '52', 'ZM'),
(40, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 0, 0, 0, 'Wirtschaftswissenschaften', '16', 'Q'),
(41, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 0, 0, 0, 'Wissenschaftskunde, Forschungs-, Hochschul-, Museumswesen', '55', 'AK-AL'),
(42, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 0, 0, 0, 'Bildungsgeschichte', '0', 'DD'),
(43, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 0, 0, 0, 'Asien-Afrika-Wissenschaften', 'AA', ''),
(44, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 0, 0, 0, 'Alphabetische Auflistung', 'all', 'ALL');
