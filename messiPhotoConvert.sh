#!/bin/bash
# Program to convert pictures for our homepage.
# Author:  Lucas Heuvelmann
# Last Modification: 11.02.2007

mkdir $2
mkdir $2"/thumbs"

cd $1
for i in *.jpg; do
 convert -resize x400 -quality 40 $i ../$2$i
 convert -resize x100 -quality 50 $i ../$2"thumbs"/$i
 echo $i
done
cd -
ncftpput -R -u messdienerkleve -p bahlmann messdienerkleve.me.funpic.de /fotos/album/ $2

#Ausführen mit:
#messi_bilder pfad/zu/den/fotos ./inetordner/
# Bsp.:  ./messihp_bilder_upload_script Elterngrillen ./elterngrillen/
# Beachten: Bilder mit .jpg und nicht .JPG enden!