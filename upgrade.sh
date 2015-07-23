#!/bin/sh
# Generic upgrade script by wkpark at gmail.com
# from the MoniWiki upgrade script.
#
# Since 2006/01/03
# License LGPL/GPL
#
# Currently support tar/7z/zip formats
#

CHECKSUM=
PACKAGE=xe

if [ -z "$1" ]; then
	cat <<HELP
Usage: $0 ${PACKAGE}.zip
HELP
	exit 0
fi

SUCCESS="printf \033[1;32m"
FAILURE="printf \033[1;31m"
WARNING="printf \033[1;33m"
MESSAGE="printf \033[1;34m"
NORMAL="printf \033[0;39m"
MAGENTA="printf \033[1;35m"

NAME="XE"

$SUCCESS
echo
echo "+-------------------------------+"
echo "|       $NAME upgrade script       |"
echo "+-------------------------------+"
echo "| This script compare all files |"
echo "| between the current & the new |"
echo "|     All different files are   |"
echo "|  backuped in the backup       |"
echo "|  directory and so you can     |"
echo "|  restore old one by manually. |"
echo "+-------------------------------+"
echo
$WARNING
echo -n " Press "
$MAGENTA
echo -n ENTER
$WARNING
echo -n " to continue or "
$MAGENTA
echo -n Control-C
$WARNING
echo -n " to exit "
$NORMAL
read dummy

for arg; do

        case $# in
        0)
                break
                ;;
        esac

        option=$1
        shift

        case $option in
        -show|-s)
		show=1
                ;;
	*)
		if [ -z "$TAR" ]; then
			TAR=$option
		else
			TAR1=$option
		fi
	esac
done

for T in $TAR $TAR1; do
#
TMP=.tmp$$
# get file extension
ext=${T#*.}
$MESSAGE
if [  "$ext" != "zip" ] && [ "$ext" != "tgz" ] && [ "$ext" != "tar.gz" ] && [ "$ext" != "7z" ]; then
	# busybox does not support ${FOO#*.}
	ext=$(echo $T | sed 's@^.*\.\(7z\|zip\|tgz\|tar.gz\)$@\1@')
	if [ "$ext" != "zip" ] && [ "$ext" != "tgz" ] && [ "$ext" != "tar.gz" ] && [ "$ext" != "7z" ]; then
		echo "*** FATAL: unrecognized extension ***"
		exit
	fi
fi
[ "$ext" = "zip" ] && echo "*** Extract Zip ***"
[ "$ext" = "tgz" -o "$ext" = "tar.gz" ] && echo "*** Extract tarball ***"
[ "$ext" = "7z" ] && echo "*** Extract 7z ***"
$NORMAL

if [ ! -d $TMP/$PACKAGE ]; then
	mkdir -p $TMP/$PACKAGE
else
	mv $TMP/$PACKAGE $TMP/$PACKAGE.orig
	mkdir -p $TMP/$PACKAGE
fi

if [ $ext = "tgz" -o "$ext" = "tar.gz" ]; then
echo tar xzf $T --strip-components=1 -C$TMP/$PACKAGE
tar xzf $T --strip-components=1 -C$TMP/$PACKAGE
fi

if [ $ext = "zip" ]; then
[ $(which unzip) = "" ] && echo "*** FATAL: unzip command not found ***" && exit
echo unzip -d $TMP $T
unzip -d $TMP $T
fi

if [ $ext = "7z" ]; then
[ $(which 7zr) = "" ] && echo "*** FATAL: 7zr command not found ***" && exit
echo 7zr x $T -o$TMP
7zr x $T -o$TMP
fi

done

$MESSAGE

echo "*** Check new upgrade.sh script ***"
DIFF=
[ -f $TMP/$PACKAGE/upgrade.sh ] && DIFF=$(diff $0 $TMP/$PACKAGE/upgrade.sh)
if [ ! -z "$DIFF" ]; then
	$FAILURE
	echo "WARN: new upgrade.sh script found ***"
	$NORMAL
	cp -f $TMP/$PACKAGE/upgrade.sh up.sh
	$WARNING
	echo " new upgrade.sh file was copied as 'up.sh'"
	echo " Please execute following command"
	echo
	$MAGENTA
	echo " sh up.sh $TAR"
	echo
	$WARNING
	echo -n "Ignore it and try to continue ? (y/N) "
	read YES
	if [ x$YES != xy ]; then
		rm -r $TMP
		$NORMAL
		exit;
	fi
fi

$MESSAGE
echo "*** Make the checksum list for the new version ***"
$NORMAL

FILELIST=$(find $TMP/$PACKAGE -type f | sort | sed "s@^$TMP/$PACKAGE/@@")

if [ -d $TMP/$PACKAGE.orig ]; then
	SRC=$TMP/$PACKAGE.orig
else
	SRC=.
fi

(cd $TMP/$PACKAGE; for x in $FILELIST; do test -f $x && md5sum $x;done) > checksum-new

if [ ! -f "$CHECKSUM" ];then
	$MESSAGE
	echo "*** Make the checksum for current version ***"
	$NORMAL
	(cd $SRC; for x in $FILELIST; do test -f $x && md5sum $x;done) > checksum-current
	CHECKSUM=checksum-current
fi

UPGRADE=`diff -U0 checksum-current checksum-new |grep '^-'|cut -d' ' -f3`
NEW=`diff -U0 checksum-current checksum-new |grep '^\(-\|+\)' | cut -d' ' -f3|sort |uniq`

if [ -z "$UPGRADE" ] && [ -z "$NEW" ] ; then
	rm -r $TMP
	$FAILURE
	echo "No difference found!! You have already installed the latest version"
	$NORMAL
	exit
fi


if [ $SRC != '.' ]; then
	$MESSAGE
	echo "*** Make $PACKAGE-changes.tgz file... ***"
	CHANGES=$(diff -U0 checksum-current checksum-new |grep '^\(-\|+\)' | cut -d' ' -f3| sed "s@^@$PACKAGE/@" | sort |uniq)
	if [ -z "$CHANGES" ]; then
		$FAILURE
		echo "No difference found!!"
		$NORMAL

		exit
	fi
	(cd $TMP;tar czf ../$PACKAGE-changes.tgz $CHANGES)
	$SUCCESS
	echo
	echo "$PACKAGE-changes.tgz is made successfully"
	echo

	rm -r $TMP
	$NORMAL
	exit
fi

$MESSAGE
echo "*** Backup the old files ***"
$NORMAL

TYPES=B/t
$WARNING
echo -n " What type of backup do you want to ? ("
$MAGENTA
echo -n B
$WARNING
echo -n "ackup(default)/"
$MAGENTA
echo -n t
$WARNING
echo -n "ar/"
if [ $(which zip) != "" ]; then
$MAGENTA
echo -n z
$WARNING
echo -n "ip/"
TYPES=$TYPES/z
fi
if [ $(which 7zr) != "" ]; then
$MAGENTA
echo -n 7
$WARNING
echo -n "z/"
TYPES=$TYPES/7
fi
$MAGENTA
echo -n p
$WARNING
echo "atch) "
TYPES=$TYPES/p
$NORMAL

echo "   (Type '$TYPES')"
read TYPE

DATE=`date +%Y%m%d-%s`
if [ x$TYPE != xt ] && [ x$TYPE != xp ] && [ x$TYPE != x7 ]; then
        BACKUP=backup/$DATE
else
        BACKUP=$TMP/$PACKAGE-$DATE
fi
$MESSAGE

if [ ! -z "$UPGRADE" ]; then
	echo "*** Backup the old files ***"
	$NORMAL
	mkdir -p backup
	mkdir -p $BACKUP
	tar cf - $UPGRADE|(cd $BACKUP;tar xvf -)

	if [ x$TYPE = xt ]; then
		SAVED="backup/$DATE.tar.gz"
        	(cd $TMP; tar czvf ../backup/$DATE.tar.gz $PACKAGE-$DATE)
        	$MESSAGE
        	echo "   Old files are backuped as a backup/$DATE.tar.gz"
        	$NORMAL
	elif [ x$TYPE = xz ]; then
		SAVED="backup/$DATE.zip"
		(cd $TMP; zip -r ../backup/$DATE.zip $PACKAGE-$DATE)
		$MESSAGE
		echo "   Old files are backuped as a backup/$DATE.zip"
		$NORMAL
	elif [ x$TYPE = x7 ]; then
		SAVED="backup/$DATE.7z"
		(cd $TMP; 7zr a ../backup/$DATE.7z $PACKAGE-$DATE)
		$MESSAGE
		echo "   Old files are backuped as a backup/$DATE.7z"
		$NORMAL
	elif [ x$TYPE = xp ]; then
		SAVED="backup/$PACKAGE-$DATE.diff"
        	(cd $TMP; diff -ruN $PACKAGE-$DATE $PACKAGE > ../backup/$PACKAGE-$DATE.diff )
        	$MESSAGE
        	echo "   Old files are backuped as a backup/$PACKAGE-$DATE.diff"
        	$NORMAL
	else
		SAVED="$BACKUP/ dir"
        	$MESSAGE
        	echo "   Old files are backuped to the $SAVED"
        	$NORMAL
	fi
else
	$WARNING
	echo " You don't need to backup files !"
	$NORMAL
fi

$WARNING
echo " Are you really want to upgrade $PACKAGE ?"
$NORMAL
echo -n "   (Type '"
$MAGENTA
echo -n yes
$NORMAL
echo -n "' to upgrade or type others to exit)  "
read YES
if [ x$YES != xyes ]; then
	rm -r $TMP
	echo -n "Please type '"
	$MAGENTA
	echo -n yes
	$NORMAL
	echo "' to real upgrade"
	exit 1
fi
(cd $TMP/$PACKAGE;tar cf - $NEW|(cd ../..;tar xvf -))
rm -r $TMP
$SUCCESS
echo
echo "$PACKAGE is successfully upgraded."
echo
echo "   All different files are       "
echo "       backuped in the           "
echo "       $SAVED now. :)       "
$NORMAL
