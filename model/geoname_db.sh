#!/bin/bash

#variant for RedBean Integration by Surikat

WORKPATH_DB="/var/www/geoname-data"
DBHOST="localhost"
DBPORT="5432"
DBUSER='postgres'
DBNAME='geonames'
echo "FILL DATABASE ( can be very long ) ..."

psql -e -U $DBUSER -h $DBHOST -p $DBPORT $DBNAME <<EOT
DROP TABLE geoname CASCADE;
CREATE TABLE geoname (
	id				SERIAL PRIMARY KEY,
	name			VARCHAR(200),
	asciiname		VARCHAR(200),
	geoaltnames	TEXT,
	latitude		FLOAT,
	longitude		FLOAT,
	fclass			CHAR(1),
	fcode			VARCHAR(10),
	country			VARCHAR(2),
	cc2				VARCHAR(60),
	admin1			VARCHAR(20),
	admin2			VARCHAR(80),
	admin3			VARCHAR(20),
	admin4			VARCHAR(20),
	population		BIGINT,
	elevation		INT,
	gtopo30			INT,
	timezone		VARCHAR(40),
	moddate			DATE
);
	
DROP TABLE geoaltname;
CREATE TABLE geoaltname (
	id				SERIAL PRIMARY KEY,
	geoname_id		INT,
	geoaltnameid	INT,
	iso_language	VARCHAR(7),
	name 			TEXT,
	is_preferred_name BOOLEAN,
	is_short_name	BOOLEAN,
	unknow1	TEXT,
	unknow2	TEXT
);

DROP TABLE geocountry;
CREATE TABLE "geocountry" (
	id						SERIAL PRIMARY KEY,
	iso_alpha2		 		CHAR(2),
	iso_alpha3		 		CHAR(3),
	iso_numeric				INTEGER,
	fips_code				CHARACTER VARYING(3),
	country					CHARACTER VARYING(200),
	capital					CHARACTER VARYING(200),
	areainsqkm				DOUBLE PRECISION,
	population				INTEGER,
	continent				CHAR(2),
	tld						CHAR(10),
	currency_code			CHAR(3),
	currency_name			CHAR(15),
	phone					CHARACTER VARYING(20),
	postal					CHARACTER VARYING(60),
	postal_regex				CHARACTER VARYING(200),
	languages				CHARACTER VARYING(200),
	geoname_id				INT,
	neighbours				CHARACTER VARYING(50),
	equivalent_fips_code	CHARACTER VARYING(3)
);



DROP TABLE geolang;
CREATE TABLE geolang(
	id			SERIAL PRIMARY KEY,
	iso_639_3	 CHAR(4),
	iso_639_2	 VARCHAR(50),
	iso_639_1	 VARCHAR(50),
	name VARCHAR(200)
);


DROP TABLE geoarea1admin;
CREATE TABLE geoarea1admin (
	id		 SERIAL PRIMARY KEY,
	code		CHAR(20),
	name		TEXT,
	name_ascii TEXT,
	geoname_id INT
);

DROP TABLE geoarea2admin;
CREATE TABLE geoarea2admin (
	id		 SERIAL PRIMARY KEY,
	code		CHAR(80),
	name		TEXT,
	name_ascii TEXT,
	geoname_id INT
);

DROP TABLE geotype;
CREATE TABLE geotype (
	id		 SERIAL PRIMARY KEY,
	code		CHAR(7),
	name		VARCHAR(200),
	description TEXT
);

DROP TABLE geotimezone;
CREATE TABLE geotimezone (
	id		 SERIAL PRIMARY KEY,
	countrycode CHAR(2),
	time_zone VARCHAR(200),
	gmt_offset NUMERIC(3,1),
	dst_offset NUMERIC(3,1),
	raw_offset NUMERIC(3,1)
);

DROP TABLE geocontinent;
CREATE TABLE geocontinent (
	id		 SERIAL PRIMARY KEY,
	code		CHAR(2),
	name		VARCHAR(20),
	geoname_id INT
);

DROP TABLE geopostal;
CREATE TABLE geopostal (
	id		 SERIAL PRIMARY KEY,
	countrycode CHAR(2),
	geopostal	VARCHAR(20),
	placename	 VARCHAR(180),
	admin1name	VARCHAR(100),
	admin1code	VARCHAR(20),
	admin2name	VARCHAR(100),
	admin2code	VARCHAR(20),
	admin3name	VARCHAR(100),
	admin3code	VARCHAR(20),
	latitude	FLOAT,
	longitude	 FLOAT,
	accuracy	SMALLINT
);
INSERT INTO geocontinent (code,name,geoname_id) VALUES ('AF', 'Africa', 6255146);
INSERT INTO geocontinent (code,name,geoname_id) VALUES ('AS', 'Asia', 6255147);
INSERT INTO geocontinent (code,name,geoname_id) VALUES ('EU', 'Europe', 6255148);
INSERT INTO geocontinent (code,name,geoname_id) VALUES ('NA', 'North America', 6255149);
INSERT INTO geocontinent (code,name,geoname_id) VALUES ('OC', 'Oceania', 6255150);
INSERT INTO geocontinent (code,name,geoname_id) VALUES ('SA', 'South America', 6255151);
INSERT INTO geocontinent (code,name,geoname_id) VALUES ('AN', 'Antarctica', 6255152);


ALTER TABLE ONLY geocountry
	ADD CONSTRAINT fk_geoname_id FOREIGN KEY (geoname_id) REFERENCES geoname(id);

copy geoname (id,name,asciiname,geoaltnames,latitude,longitude,fclass,fcode,country,cc2,admin1,admin2,admin3,admin4,population,elevation,gtopo30,timezone,moddate) from '${WORKPATH_DB}/geoname.csv' null as '';
copy geopostal (countrycode,geopostal,placename,admin1name,admin1code,admin2name,admin2code,admin3name,admin3code,latitude,longitude,accuracy) from '${WORKPATH_DB}/geopostal.csv' null as '';
copy geotimezone (countrycode,time_zone,gmt_offset,dst_offset,raw_offset) from '${WORKPATH_DB}/geotimezone.csv' null as '';
copy geotype (code,name,description) from '${WORKPATH_DB}/geotype.csv' null as '';
copy geoarea1admin (code,name,name_ascii,geoname_id) from '${WORKPATH_DB}/geoarea1admin.csv' null as '';
copy geoarea2admin (code,name,name_ascii,geoname_id) from '${WORKPATH_DB}/geoarea2admin.csv' null as '';
copy geolang (iso_639_3,iso_639_2,iso_639_1,name) from '${WORKPATH_DB}/geolang.csv' null as '';
copy geocountry (iso_alpha2,iso_alpha3,iso_numeric,fips_code,country,capital,areainsqkm,population,continent,tld,currency_code,currency_name,phone,postal,postal_regex,languages,geoname_id,neighbours,equivalent_fips_code) from '${WORKPATH_DB}/geocountry.csv' null as '';
copy geoaltname (geoname_id,geoaltnameid,iso_language,name,is_preferred_name,is_short_name,unknow1,unknow2) from '${WORKPATH_DB}/geoaltname.csv' null as '';

DELETE FROM geoaltname WHERE geoname_id NOT IN (SELECT id from geoname);

ALTER TABLE ONLY geoaltname
	ADD CONSTRAINT fk_geoname_id FOREIGN KEY (geoname_id) REFERENCES geoname(id);
	
CREATE INDEX index_geocountry_geoname_id ON geocountry USING hash (geoname_id);
CREATE INDEX index_geoaltname_geoname_id ON geoaltname USING hash (geoname_id);

EOT

echo "'----- DONE ( have fun... )"