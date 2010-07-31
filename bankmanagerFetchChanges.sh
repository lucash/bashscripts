#!/bin/bash
for i in `git diff $1 --name-only`
do
	cp /media/bankmanager/$i ./$i
done;
