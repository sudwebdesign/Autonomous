#!/bin/bash
WORKPATH="${HOME}/tmp/GIS/gisnames/geodata"
WORKPATH_DB="/var/www/geoname-data"
TMPPATH="tmp"
PCPATH="pc"
PREFIX="_"
FILES="allCountries.zip alternateNames.zip userTags.zip admin1CodesASCII.txt admin2Codes.txt countryInfo.txt featureCodes_en.txt iso-languagecodes.txt timeZones.txt"

# check if needed directories do already exsist
if [ -d "$WORKPATH" ]; then
	echo "$WORKPATH exists..."
	sleep 0
else
	echo "$WORKPATH and subdirectories will be created..."
	mkdir -p $WORKPATH/{$TMPPATH,$PCPATH}
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