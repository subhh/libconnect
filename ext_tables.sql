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
