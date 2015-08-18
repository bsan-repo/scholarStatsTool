CREATE DATABASE IF NOT EXISTS academic;

USE academic;

CREATE TABLE IF NOT EXISTS config_parameter(
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(127),
    value VARCHAR(255),
    INDEX(name)
);

CREATE TABLE IF NOT EXISTS author_to_search(
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255),
    processed BOOLEAN DEFAULT FALSE
);

CREATE TABLE IF NOT EXISTS affiliation(
	id INTEGER PRIMARY KEY AUTO_INCREMENT,
	msa_id INTEGER,
	official_name VARCHAR(255), 
	homepage VARCHAR(255),
	latitude DOUBLE,
	longitude DOUBLE,
	INDEX(msa_id)
) ENGINE=INNODB DEFAULT CHARSET=latin1;


CREATE TABLE IF NOT EXISTS author_details(
	id INTEGER PRIMARY KEY AUTO_INCREMENT, 
	msa_id INTEGER,
	affiliation_id INTEGER,
	affiliation VARCHAR(255),
	msa_affiliation_id INTEGER,
	research_interest VARCHAR(255),
	homepage_url VARCHAR(255),
	version INTEGER,
	FOREIGN KEY(affiliation_id)
		REFERENCES affiliation(id)
		ON DELETE SET NULL
) ENGINE=INNODB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS author(
	id INTEGER PRIMARY KEY AUTO_INCREMENT, 
	name VARCHAR(255),
	details_id INTEGER,
	INDEX(name),
	FOREIGN KEY(details_id)
		REFERENCES author_details(id)
		ON DELETE CASCADE
) ENGINE=INNODB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS era_journal(
	id INTEGER PRIMARY KEY AUTO_INCREMENT, 
	era_id VARCHAR(6),
	name VARCHAR(100), 
	acronym VARCHAR(15), 
	rank VARCHAR(2)
) ENGINE=INNODB DEFAULT CHARSET=latin1;
	
CREATE TABLE IF NOT EXISTS era_conference(
	id INTEGER PRIMARY KEY AUTO_INCREMENT, 
	era_id VARCHAR(6),
	name VARCHAR(100), 
	acronym VARCHAR(15), 
	rank VARCHAR(2)
) ENGINE=INNODB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS journal(
	id INTEGER PRIMARY KEY AUTO_INCREMENT,
	msa_id INTEGER,
	fullname VARCHAR(256),
	homepage VARCHAR(256),
	era_entry INTEGER DEFAULT NULL,
	INDEX(msa_id),
	FOREIGN KEY(era_entry)
		REFERENCES era_journal(id)
		ON DELETE SET NULL
) ENGINE=INNODB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS conference(
	id INTEGER PRIMARY KEY AUTO_INCREMENT, 
	msa_id INTEGER,
	fullname VARCHAR(256),
	homepage VARCHAR(256),
	era_entry INTEGER DEFAULT NULL,
	INDEX(msa_id),
	FOREIGN KEY(era_entry)
		REFERENCES era_conference(id)
		ON DELETE SET NULL
) ENGINE=INNODB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS paper(
	id INTEGER PRIMARY KEY AUTO_INCREMENT, 
	conference_id INTEGER,
	journal_id INTEGER,
	year INTEGER,
	title VARCHAR(255),
	msa_id INTEGER,
	keyword VARCHAR(255),
	msa_conference_id INTEGER,
	msa_journal_id INTEGER,
	INDEX(msa_id),
	FOREIGN KEY(conference_id)
		REFERENCES conference(id)
		ON DELETE SET NULL,
	FOREIGN KEY(journal_id)
		REFERENCES journal(id)
		ON DELETE SET NULL
) ENGINE=INNODB DEFAULT CHARSET=latin1;

CREATE TABLE author_paper(
	id INTEGER PRIMARY KEY AUTO_INCREMENT, 
	author_id INTEGER,
	paper_id INTEGER,
	msa_seq_id INTEGER,
	msa_paper_id INTEGER,
	msa_author_id INTEGER,
        INDEX(msa_paper_id),
        INDEX(msa_author_id),
	FOREIGN KEY(author_id)
		REFERENCES author(id)
		ON DELETE CASCADE,
	FOREIGN KEY(paper_id)
		REFERENCES paper(id)
		ON DELETE CASCADE
) ENGINE=INNODB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS paper_ref(
	id INTEGER PRIMARY KEY AUTO_INCREMENT, 
	paper_id INTEGER,
	citation_id INTEGER,
	msa_paper_id INTEGER,
	msa_citation_id INTEGER,
	msa_seq_ref INTEGER,
        INDEX(msa_paper_id),
        INDEX(msa_citation_id),
	FOREIGN KEY(paper_id)
		REFERENCES paper(id)
		ON DELETE CASCADE,
	FOREIGN KEY(citation_id)
		REFERENCES paper(id)
		ON DELETE CASCADE
) ENGINE=INNODB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS author_stats(
	author_id INTEGER PRIMARY KEY, 
        publications INTEGER DEFAULT 0,
        qpublications INTEGER DEFAULT 0,
	citations INTEGER DEFAULT 0,
	qcitations INTEGER DEFAULT 0,
	hindex INTEGER DEFAULT 0,
	qhindex INTEGER DEFAULT 0,
	FOREIGN KEY(author_id)
		REFERENCES author(id)
		ON DELETE CASCADE
) ENGINE=INNODB DEFAULT CHARSET=latin1;







CREATE INDEX paper_ref_msa_paper_id ON paper_ref (msa_paper_id) USING BTREE;
CREATE INDEX paper_ref_msa_citation_id ON paper_ref (msa_citation_id) USING BTREE;
CREATE INDEX author_paper_msa_paper_id ON author_paper (msa_paper_id) USING BTREE;
CREATE INDEX author_paper_msa_author_id ON author_paper (msa_author_id) USING BTREE;
CREATE INDEX paper__msa_id ON paper (msa_id) USING BTREE;
CREATE INDEX conference_msa_id ON conference (msa_id) USING BTREE;
CREATE INDEX journal_msa_id ON journal (msa_id) USING BTREE;
CREATE INDEX affiliation_msa_id ON affiliation (msa_id) USING BTREE;

CREATE INDEX paper_title_id ON paper (title) USING BTREE;



ALTER TABLE author_to_search AUTO_INCREMENT = 33;
ALTER TABLE affiliation AUTO_INCREMENT = 23;
ALTER TABLE author_details AUTO_INCREMENT = 41;
ALTER TABLE author AUTO_INCREMENT = 41;
ALTER TABLE journal AUTO_INCREMENT = 7280;
ALTER TABLE conference AUTO_INCREMENT = 13819;
ALTER TABLE paper AUTO_INCREMENT = 66233;
ALTER TABLE author_paper AUTO_INCREMENT = 4786;
ALTER TABLE paper_ref AUTO_INCREMENT = 170795;




-- Added to authordata
-- Include era tables from this script
CREATE INDEX publisher_index_work ON author_works (publisher) USING BTREE;
CREATE INDEX gpublisher_index_work ON author_works (publisher_in_google) USING BTREE;

CREATE INDEX publisher_index_cit ON citing_works (publisher) USING BTREE;
CREATE INDEX gpublisher_index_cit ON citing_works (publisher_in_google) USING BTREE;
CREATE INDEX extpublisher_index_cit ON citing_works (publisher_in_external_web) USING BTREE;

-- work 
ALTER TABLE author_works ADD COLUMN era_j_id INTEGER;
ALTER TABLE author_works ADD COLUMN era_c_id INTEGER;
-- citation
ALTER TABLE citing_works ADD COLUMN era_j_id INTEGER;
ALTER TABLE citing_works ADD COLUMN era_c_id INTEGER;
-- work 196, 196, 9, 9
update author_works as aw left join era_journal as ej on aw.publisher=ej.name set era_j_id=ej.id;
update author_works as aw left join era_journal as ej on aw.publisher_in_google=ej.name set era_j_id=ej.id where aw.era_j_id is null and aw.era_c_id is null;
update author_works as aw left join era_conference as ej on aw.publisher=ej.name set era_c_id=ej.id where aw.era_j_id is null and aw.era_c_id is null;
update author_works as aw left join era_conference as ej on aw.publisher_in_google=ej.name set era_c_id=ej.id where aw.era_j_id is null and aw.era_c_id is null;
-- citation  1188, 1, 1348, 52, 11, 51
update citing_works as aw left join era_journal as ej on aw.publisher=ej.name set era_j_id=ej.id;
update citing_works as aw left join era_journal as ej on aw.publisher_in_google=ej.name set era_j_id=ej.id where aw.era_j_id is null and aw.era_c_id is null;
update citing_works as aw left join era_journal as ej on aw.publisher_in_external_web=ej.name set era_j_id=ej.id where aw.era_j_id is null and aw.era_c_id is null;
update citing_works as aw left join era_conference as ej on aw.publisher=ej.name set era_c_id=ej.id where aw.era_j_id is null and aw.era_c_id is null;
update citing_works as aw left join era_conference as ej on aw.publisher_in_google=ej.name set era_c_id=ej.id where aw.era_j_id is null and aw.era_c_id is null;
update citing_works as aw left join era_conference as ej on aw.publisher_in_external_web=ej.name set era_c_id=ej.id where aw.era_j_id is null and aw.era_c_id is null;

select count(*) from author_works where era_j_id is not null or era_c_id is not null; -- 204
select count(*) from citing_works where era_j_id is not null or era_c_id is not null -- 348