#!/bin/bash

#variant for RedBean Integration by Surikat

WORKPATH_DB="/var/www/geoname-data-new"
DBHOST="localhost"
DBPORT="5432"
DBUSER='postgres'
DBNAME='geonames'
echo "FILL DATABASE ( can be very long ) ..."
psql -e -U $DBUSER -h $DBHOST -p $DBPORT $DBNAME <<EOT

	COPY geoname (id,name,asciiname,geoaltnames,latitude,longitude,fclass,fcode,country,cc2,admin1,admin2,admin3,admin4,population,elevation,gtopo30,timezone,moddate) TO '${WORKPATH_DB}/geoname.csv' null as '';
	COPY geopostal (id,countrycode,geopostal,placename,admin1name,admin1code,admin2name,admin2code,admin3name,admin3code,latitude,longitude,accuracy) TO '${WORKPATH_DB}/geopostal.csv' null as '';
	COPY geotimezone (id,countrycode,time_zone,gmt_offset,dst_offset,raw_offset) TO '${WORKPATH_DB}/geotimezone.csv' null as '';
	COPY geotype (id,code,name,description) TO '${WORKPATH_DB}/geotype.csv' null as '';
	COPY geoarea1admin (id,code,name,name_ascii,geoname_id) TO '${WORKPATH_DB}/geoarea1admin.csv' null as '';
	COPY geoarea2admin (id,code,name,name_ascii,geoname_id) TO '${WORKPATH_DB}/geoarea2admin.csv' null as '';
	COPY geolang (id,iso_639_3,iso_639_2,iso_639_1,name) TO '${WORKPATH_DB}/geolang.csv' null as '';
	COPY geocountry (id,iso_alpha2,iso_alpha3,iso_numeric,fips_code,country,capital,areainsqkm,population,continent,tld,currency_code,currency_name,phone,postal,postal_regex,languages,geoname_id,neighbours,equivalent_fips_code) TO '${WORKPATH_DB}/geocountry.csv' null as '';
	COPY geoaltname_tmp (id,geoname_id,geoaltnameid,iso_language,name,is_preferred_name,is_short_name,unknow1,unknow2) TO '${WORKPATH_DB}/geoaltname.csv' null as '';

EOT

echo "'----- DONE"