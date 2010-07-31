#!/bin/bash
for i in `git diff master --name-only`
do
	cp ./$i /media/bankmanager/$i
done;
