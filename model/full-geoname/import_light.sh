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
	
COPY geoname (id,name,asciiname,geoaltnames,latitude,longitude,fclass,fcode,country,cc2,admin1,admin2,admin3,admin4,population,elevation,gtopo30,timezone,moddate) FROM '${WORKPATH_DB}/geoname.csv' null as '';

CREATE EXTENSION postgis;
CREATE EXTENSION postgis_topology;
CREATE EXTENSION postgis_tiger_geocoder;
SELECT AddGeometryColumn ('public','geoname','the_geom',4326,'POINT',2);
UPDATE geoname SET the_geom = ST_PointFromText('POINT(' || longitude || ' ' || latitude || ')', 4326);
CREATE INDEX idx_geoname_the_geom ON public.geoname USING gist(the_geom);

EOT

echo "'----- DONE ( have fun... )"