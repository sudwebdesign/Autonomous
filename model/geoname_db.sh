#!/bin/bash

#rapid variant for RedBean Integration by Surikat

WORKPATH="${HOME}/tmp/GIS/gisnames/geodata"
TMPPATH="tmp"
PCPATH="pc"
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
	alternatenames	TEXT,
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
	
DROP TABLE alternatename;
CREATE TABLE alternatename (
	id				SERIAL PRIMARY KEY,
	geoname_id		INT,
	iso_language		VARCHAR(7),
	alternate_name 	TEXT
);

DROP TABLE countryinfo;
CREATE TABLE "countryinfo" (
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
	postalRegex				CHARACTER VARYING(200),
	languages				CHARACTER VARYING(200),
	geoname_id				INT,
	neighbours				CHARACTER VARYING(50),
	equivalent_fips_code	CHARACTER VARYING(3)
);



DROP TABLE iso_languagecodes;
CREATE TABLE iso_languagecodes(
	id			SERIAL PRIMARY KEY,
	iso_639_3	 CHAR(4),
	iso_639_2	 VARCHAR(50),
	iso_639_1	 VARCHAR(50),
	language_name VARCHAR(200)
);


DROP TABLE admin1codesascii;
CREATE TABLE admin1codesascii (
	id		 SERIAL PRIMARY KEY,
	code		CHAR(20),
	name		TEXT,
	name_ascii TEXT,
	geoname_id INT
);

DROP TABLE admin2codesascii;
CREATE TABLE admin2codesascii (
	id		 SERIAL PRIMARY KEY,
	code		CHAR(80),
	name		TEXT,
	name_ascii TEXT,
	geoname_id INT
);

DROP TABLE featurecodes;
CREATE TABLE featurecodes (
	id		 SERIAL PRIMARY KEY,
	code		CHAR(7),
	name		VARCHAR(200),
	description TEXT
);

DROP TABLE timezones;
CREATE TABLE timezones (
	id		 SERIAL PRIMARY KEY,
	countrycode CHAR(2),
	time_zone VARCHAR(200),
	gmt_offset NUMERIC(3,1),
	dst_offset NUMERIC(3,1),
	raw_offset NUMERIC(3,1)
);

DROP TABLE continentcodes;
CREATE TABLE continentcodes (
	id		 SERIAL PRIMARY KEY,
	code		CHAR(2),
	name		VARCHAR(20),
	geoname_id INT
);

DROP TABLE postalcodes;
CREATE TABLE postalcodes (
	id		 SERIAL PRIMARY KEY,
	countrycode CHAR(2),
	postalcode	VARCHAR(20),
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
ALTER TABLE ONLY countryinfo
	ADD CONSTRAINT fk_geoname_id FOREIGN KEY (geoname_id) REFERENCES geoname(id);
ALTER TABLE ONLY alternatename
	ADD CONSTRAINT fk_geoname_id FOREIGN KEY (geoname_id) REFERENCES geoname(id);

copy geoname (id,name,asciiname,alternatenames,latitude,longitude,fclass,fcode,country,cc2,admin1,admin2,admin3,admin4,population,elevation,gtopo30,timezone,moddate) from '${WORKPATH}/${TMPPATH}/allCountries.txt' null as '';
copy postalcodes (NULL,countrycode,postalcode,placename,admin1name,admin1code,admin2name,admin2code,admin3name,admin3code,latitude,longitude,accuracy) from '${WORKPATH}/${PCPATH}/allCountries.txt' null as '';
copy timezones (NULL,countrycode,time_zone,gmt_offset,dst_offset,raw_offset) from '${WORKPATH}/${TMPPATH}/timeZones.txt.tmp' null as '';
copy featurecodes (NULL,code,name,description) from '${WORKPATH}/${TMPPATH}/featureCodes_en.txt' null as '';
copy admin1codesascii (NULL,code,name,name_ascii,geoname_id) from '${WORKPATH}/${TMPPATH}/admin1CodesASCII.txt' null as '';
copy admin2codesascii (NULL,code,name,name_ascii,geoname_id) from '${WORKPATH}/${TMPPATH}/admin2Codes.txt' null as '';
copy iso_languagecodes (NULL,iso_639_3,iso_639_2,iso_639_1,language_name) from '${WORKPATH}/${TMPPATH}/iso-languagecodes.txt.tmp' null as '';
copy countryInfo (NULL,iso_alpha2,iso_alpha3,iso_numeric,fips_code,country,capital,areainsqkm,population,continent,tld,currency_code,currency_name,phone,postal,postalRegex,languages,geoname_id,neighbours,equivalent_fips_code) from '${WORKPATH}/${TMPPATH}/countryInfo.txt.tmp' null as '';
copy alternatename (id,geoname_id,iso_language,alternate_name) from '${WORKPATH}/${TMPPATH}/alternateNames.txt' null as '';
INSERT INTO continentcodes VALUES (NULL, 'AF', 'Africa', 6255146);
INSERT INTO continentcodes VALUES (NULL, 'AS', 'Asia', 6255147);
INSERT INTO continentcodes VALUES (NULL, 'EU', 'Europe', 6255148);
INSERT INTO continentcodes VALUES (NULL, 'NA', 'North America', 6255149);
INSERT INTO continentcodes VALUES (NULL, 'OC', 'Oceania', 6255150);
INSERT INTO continentcodes VALUES (NULL, 'SA', 'South America', 6255151);
INSERT INTO continentcodes VALUES (NULL, 'AN', 'Antarctica', 6255152);
CREATE INDEX index_countryinfo_geoname_id ON countryinfo USING hash (geoname_id);
CREATE INDEX index_alternatename_geoname_id ON alternatename USING hash (geoname_id);
EOT

echo "'----- DONE ( have fun... )"