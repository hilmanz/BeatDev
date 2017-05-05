#!/bin/sh
#########################################################
## Deploy A Exchange to Production Server              ##
## inong@kana.co.id                                    ##
## 06 May 2013                                         ##
#########################################################

#cd /home/amild/public_html/staging/tools

rsync -avz --delete --exclude-from=conf/exclude-production.txt -e "ssh -l milad" /Users/inong/Documents/repository/a360/trunk/athreesix/ 117.54.1.81:/home/milad/
