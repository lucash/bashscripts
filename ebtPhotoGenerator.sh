#!/bin/bash
if [ ! -d gallery ]; then 
       mkdir gallery 
fi
for i in *.jpg *.JPG; do
        size=`identify -verbose "$i" | grep -i Geometry | cut -d':' -f 2`;
      xsize=`echo $size | cut -d'x' -f 1`;
      ysize=`echo $size | cut -d'x' -f 2| cut -d'+' -f 1`;
       if [ $xsize -lt 800 -a $ysize -lt 800 ]; then
            cp $i gallery/${i};
        else 
        echo $xsize':'$ysize
        if [ $xsize -gt $ysize ]; then
                convert -resize '800x600' -quality '50' "$i" "gallery/${i}";
        else 
            convert -resize '600x800' -quality '50' "$i" "gallery/${i}";
        fi
       fi
done
