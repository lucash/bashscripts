#!/bin/bash
killall trayer
trayer --edge top --align right --SetDockType true --SetPartialStrut true  --expand true --width $1 --transparent true --tint 0x191970 --height 17 &
