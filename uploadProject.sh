#!/bin/bash

while getopts s:d: option
do
	case $option in
		s)
			SOURCE=$OPTARG
			;;
		d)
			DEST=$OPTARG
			;;
	esac
done

if [ ! -d "$SOURCE" ]
then
	echo "Source directory given is not a directory!"
	exit 1;
fi

if [ ! -d "$DEST" ]
then
	echo "Target directory given is not a directory!"
	exit 1;
fi

if [ ! -f "$SOURCE/.lastTime" ]
then
	USERINPUT=""
	while [ "$USERINPUT" != "j" -a "$USERINPUT" != "n" ]
	do
		echo "File ".lastTime" doesn't exist in source dir."
		echo "Assuming that files has never been copied. Continue? (j/n)"
		read USERINPUT
	done
	if [ "$USERINPUT" = "n" ]
	then
		exit 1;
	else
		excludeNumber=0
		INPUT=" "
		echo "Are there files which should be excluded? List them divided by enter. Empty line to finish."
		while [ "$excludeNumber" != "END" ]
		do
			read INPUT
			if [ "$INPUT" != "" ]
			then
				EXCLUDE[$excludeNumber]=$INPUT
				((excludeNumber+=1))
			else
				excludeNumber="END"
			fi
		done
	fi
	LASTTIME=0
else
	source "$SOURCE/.lastTime"
fi

THISTIME=`date +%s`
DIFFTIME=`echo "$THISTIME-$LASTTIME" | bc`
DIFFTIMEMIN=`echo "$DIFFTIME/60" | bc`

excludeString=""
for ((i=0; i<${#EXCLUDE[*]}; i++)) do
	excludeString="$excludeString -a ! -wholename *${EXCLUDE[$i]}*"
done

findCmd="find $SOURCE -mmin -$DIFFTIMEMIN $excludeString"
$findCmd

# saving of vars to SOURCE/.lastTime
excludeSaveString="("
for ((i=0; i<${#EXCLUDE[*]}; i++)) do
	excludeSaveString="$excludeSaveString \"${EXCLUDE[$i]}\"";
done
excludeSaveString=${excludeSaveString}\)

echo LASTTIME=`date +%s` > $SOURCE/.lastTime
echo EXCLUDE=$excludeSaveString >> $SOURCE/.lastTime
