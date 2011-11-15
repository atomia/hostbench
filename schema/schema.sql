CREATE TABLE IF NOT EXISTS perftest_indexed_innodb (
	id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	keycol INT NOT NULL,
	INDEX (keycol)
) ENGINE=InnoDB;

TRUNCATE TABLE perftest_indexed_innodb;

CREATE TABLE IF NOT EXISTS perftest_indexed_myisam (
	id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	keycol INT NOT NULL,
	INDEX (keycol)
) ENGINE=MyISAM;

TRUNCATE TABLE perftest_indexed_myisam;

CREATE TABLE IF NOT EXISTS perftest_non_indexed_innodb (
	id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	keycol INT NOT NULL
) ENGINE=InnoDB;

TRUNCATE TABLE perftest_non_indexed_innodb;

CREATE TABLE IF NOT EXISTS perftest_non_indexed_myisam (
	id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	keycol INT NOT NULL
) ENGINE=MyISAM;

TRUNCATE TABLE perftest_non_indexed_myisam;
