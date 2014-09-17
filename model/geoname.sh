#!/bin/bash

DBNAME='geonames'

DBUSER='postgres'
DBHOST="localhost"
DBPORT="5432"
WORKPATH="~/.geoname-data/.tmp"
WORKPATH_DB="~/.geoname-data"

TMPPATH="tmp"
PCPATH="pc"
PREFIX="_"
FILES="allCountries.zip alternateNames.zip userTags.zip admin1CodesASCII.txt admin2Codes.txt countryInfo.txt featureCodes_en.txt iso-languagecodes.txt timeZones.txt"

case $1 in
  install)
	sh "$0 download"
	sh "$0 db_import"
	;;
  install_all)
	sh "$0 download"
	sh "$0 db_import_all"
	;;
  download)
		# check if needed directories do already exsist
		if [ -d "$WORKPATH" ]; then
			echo "$WORKPATH exists..."
			sleep 0
		else
			echo "$WORKPATH and subdirectories will be created..."
			mkdir $WORKPATH
			mkdir "$WORKPATH/$TMPPATH"
			mkdir "$WORKPATH/$PCPATH"
			echo "created $WORKPATH"
		fi

		echo
		echo ",---- STARTING (downloading, unpacking and preparing)"
		cd $WORKPATH/$TMPPATH

		for i in $FILES
		do
			wget -N -q "http://download.geonames.org/export/dump/$i" # get newer files
			if [ $i -nt $PREFIX$i ] || [ ! -e $PREFIX$i ] ; then
				cp -p $i $PREFIX$i
				unzip -u -q $i

				case "$i" in
					iso-languagecodes.txt)
						tail -n +2 iso-languagecodes.txt > iso-languagecodes.txt.tmp;
						;;
					countryInfo.txt)
						grep -v '^#' countryInfo.txt | head -n -2 > countryInfo.txt.tmp;
						;;
					timeZones.txt)
						tail -n +2 timeZones.txt > timeZones.txt.tmp;
						;;
				esac
				echo "| $1 has been downloaded";
			else
				echo "| $i is already the latest version"
			fi
		done

		# download the postalcodes. You must know yourself the url
		cd $WORKPATH/$PCPATH
		wget -q -N "http://download.geonames.org/export/zip/allCountries.zip"

		if [ $WORKPATH/$PCPATH/allCountries.zip -nt $WORKPATH/$PCPATH/allCountries$PREFIX.zip ] || [ ! -e $WORKPATH/$PCPATH/allCountries.zip ]; then
			echo "Attempt to unzip $WORKPATH/$PCPATH/allCountries.zip file..."
			unzip -u -q $WORKPATH/$PCPATH/allCountries.zip
			cp -p $WORKPATH/$PCPATH/allCountries.zip $WORKPATH/$PCPATH/allCountries$PREFIX.zip
			echo "| ....zip has been downloaded"
		else
			echo "| ....zip is already the latest version"
		fi

		cp "${WORKPATH}/${TMPPATH}/allCountries.txt" "${WORKPATH_DB}/geoname.csv"
		cp "${WORKPATH}/${PCPATH}/allCountries.txt" "${WORKPATH_DB}/geopostal.csv"
		cp "${WORKPATH}/${TMPPATH}/timeZones.txt.tmp" "${WORKPATH_DB}/geotimezone.csv"
		cp "${WORKPATH}/${TMPPATH}/featureCodes_en.txt" "${WORKPATH_DB}/geotype.csv"
		cp "${WORKPATH}/${TMPPATH}/admin1CodesASCII.txt" "${WORKPATH_DB}/geoarea1admin.csv"
		cp "${WORKPATH}/${TMPPATH}/admin2Codes.txt" "${WORKPATH_DB}/geoarea2admin.csv"
		cp "${WORKPATH}/${TMPPATH}/iso-languagecodes.txt.tmp" "${WORKPATH_DB}/geolang.csv"
		cp "${WORKPATH}/${TMPPATH}/countryInfo.txt.tmp" "${WORKPATH_DB}/geocountry.csv"
		cp "${WORKPATH}/${TMPPATH}/alternateNames.txt" "${WORKPATH_DB}/geoaltname.csv"
		#sed -i -e 's/^[[:space:]]*//' -e 's/[[:space:]]*$//' "${WORKPATH_DB}/geoaltname.csv"
	;;
  db_import_all)
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
	
DROP TABLE geoaltname_tmp;
CREATE TABLE geoaltname_tmp (
	id				SERIAL PRIMARY KEY,
	geoname_id		INT,
	geoaltnameid	INT,
	iso_language	VARCHAR(7),
	name 			TEXT,
	is_preferred_name BOOLEAN,
	is_short_name	BOOLEAN,
	isColloquial	BOOLEAN,
	isHistoric		BOOLEAN
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
	isColloquial	BOOLEAN,
	isHistoric		BOOLEAN
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
ALTER TABLE ONLY geocountry
	ADD CONSTRAINT fk_geoname_id FOREIGN KEY (geoname_id) REFERENCES geoname(id);
ALTER TABLE ONLY geoaltname
	ADD CONSTRAINT fk_geoname_id FOREIGN KEY (geoname_id) REFERENCES geoname(id);

INSERT INTO geocontinent (code,name,geoname_id) VALUES ('AF', 'Africa', 6255146);
INSERT INTO geocontinent (code,name,geoname_id) VALUES ('AS', 'Asia', 6255147);
INSERT INTO geocontinent (code,name,geoname_id) VALUES ('EU', 'Europe', 6255148);
INSERT INTO geocontinent (code,name,geoname_id) VALUES ('NA', 'North America', 6255149);
INSERT INTO geocontinent (code,name,geoname_id) VALUES ('OC', 'Oceania', 6255150);
INSERT INTO geocontinent (code,name,geoname_id) VALUES ('SA', 'South America', 6255151);
INSERT INTO geocontinent (code,name,geoname_id) VALUES ('AN', 'Antarctica', 6255152);

COPY geoname (id,name,asciiname,geoaltnames,latitude,longitude,fclass,fcode,country,cc2,admin1,admin2,admin3,admin4,population,elevation,gtopo30,timezone,moddate) FROM '${WORKPATH_DB}/geoname.csv' null as '';
COPY geopostal (countrycode,geopostal,placename,admin1name,admin1code,admin2name,admin2code,admin3name,admin3code,latitude,longitude,accuracy) FROM '${WORKPATH_DB}/geopostal.csv' null as '';
COPY geotimezone (countrycode,time_zone,gmt_offset,dst_offset,raw_offset) FROM '${WORKPATH_DB}/geotimezone.csv' null as '';
COPY geotype (code,name,description) FROM '${WORKPATH_DB}/geotype.csv' null as '';
COPY geoarea1admin (code,name,name_ascii,geoname_id) FROM '${WORKPATH_DB}/geoarea1admin.csv' null as '';
COPY geoarea2admin (code,name,name_ascii,geoname_id) FROM '${WORKPATH_DB}/geoarea2admin.csv' null as '';
COPY geolang (iso_639_3,iso_639_2,iso_639_1,name) FROM '${WORKPATH_DB}/geolang.csv' null as '';
COPY geocountry (iso_alpha2,iso_alpha3,iso_numeric,fips_code,country,capital,areainsqkm,population,continent,tld,currency_code,currency_name,phone,postal,postal_regex,languages,geoname_id,neighbours,equivalent_fips_code) FROM '${WORKPATH_DB}/geocountry.csv' null as '';

COPY geoaltname_tmp (geoname_id,geoaltnameid,iso_language,name,is_preferred_name,is_short_name,isColloquial,isHistoric) FROM '${WORKPATH_DB}/geoaltname.csv' null as '';
INSERT INTO geoaltname (SELECT geoaltname_tmp.* FROM geoaltname_tmp LEFT JOIN geoname ON geoaltname_tmp.geoname_id=geoname.id WHERE geoname.id IS NOT NULL);
DROP TABLE geoaltname_tmp;

CREATE INDEX index_geocountry_geoname_id ON geocountry USING hash (geoname_id);
CREATE INDEX index_geoaltname_geoname_id ON geoaltname USING hash (geoname_id);

CREATE EXTENSION postgis;
CREATE EXTENSION postgis_topology;
CREATE EXTENSION postgis_tiger_geocoder;
SELECT AddGeometryColumn ('public','geoname','the_geom',4326,'POINT',2);
UPDATE geoname SET the_geom = ST_PointFromText('POINT(' || longitude || ' ' || latitude || ')', 4326);
CREATE INDEX idx_geoname_the_geom ON public.geoname USING gist(the_geom);

EOT
	echo "'----- DONE ( have fun... )"
	;;
  db_import)
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
	
COPY geoname (id,name,asciiname,geoaltnames,latitude,longitude,fclass,fcode,country,cc2,admin1,admin2,admin3,admin4,population,elevation,gtopo30,timezone,moddate) FROM '${WORKPATH_DB}/geoname.csv' null as '';

CREATE EXTENSION postgis;
CREATE EXTENSION postgis_topology;
CREATE EXTENSION postgis_tiger_geocoder;
SELECT AddGeometryColumn ('public','geoname','the_geom',4326,'POINT',2);
UPDATE geoname SET the_geom = ST_PointFromText('POINT(' || longitude || ' ' || latitude || ')', 4326);
CREATE INDEX idx_geoname_the_geom ON public.geoname USING gist(the_geom);

EOT
	echo "'----- DONE ( have fun... )"
	;;
  db_export)
	echo "FILL DATABASE ( can be very long ) ..."
	psql -e -U $DBUSER -h $DBHOST -p $DBPORT $DBNAME <<EOT

	COPY geoname (id,name,asciiname,geoaltnames,latitude,longitude,fclass,fcode,country,cc2,admin1,admin2,admin3,admin4,population,elevation,gtopo30,timezone,moddate) TO '${WORKPATH_DB}/geoname.csv';
	COPY geopostal (id,countrycode,geopostal,placename,admin1name,admin1code,admin2name,admin2code,admin3name,admin3code,latitude,longitude,accuracy) TO '${WORKPATH_DB}/geopostal.csv';
	COPY geotimezone (id,countrycode,time_zone,gmt_offset,dst_offset,raw_offset) TO '${WORKPATH_DB}/geotimezone.csv';
	COPY geotype (id,code,name,description) TO '${WORKPATH_DB}/geotype.csv';
	COPY geoarea1admin (id,code,name,name_ascii,geoname_id) TO '${WORKPATH_DB}/geoarea1admin.csv';
	COPY geoarea2admin (id,code,name,name_ascii,geoname_id) TO '${WORKPATH_DB}/geoarea2admin.csv';
	COPY geolang (id,iso_639_3,iso_639_2,iso_639_1,name) TO '${WORKPATH_DB}/geolang.csv';
	COPY geocountry (id,iso_alpha2,iso_alpha3,iso_numeric,fips_code,country,capital,areainsqkm,population,continent,tld,currency_code,currency_name,phone,postal,postal_regex,languages,geoname_id,neighbours,equivalent_fips_code) TO '${WORKPATH_DB}/geocountry.csv';
	COPY geoaltname_tmp (id,geoname_id,geoaltnameid,iso_language,name,is_preferred_name,is_short_name,unknow1,unknow2) TO '${WORKPATH_DB}/geoaltname.csv';

EOT

	echo "'----- DONE"
	;;
  *)
	echo "Usage: $0 {install|install_all|download|db_import_all|db_import|db_export}"
	exit 1
	;;
esac