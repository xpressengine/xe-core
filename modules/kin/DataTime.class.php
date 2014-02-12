<?php
class DataTime{
	public static function getMonday($pTimeStamp=0, $pFlag=10)
	{
		$theTime = $pTimeStamp==0 ? time() : $pTimeStamp;

		$arr = getdate($theTime);
		$theTime = $theTime-86400*(($arr["wday"]+6)%7);
		return self::getDateTime($pFlag, $theTime);
	}

	public static function getSunday($pTimeStamp=0, $pFlag=10){
		$theTime = $pTimeStamp==0 ? time() : $pTimeStamp;

		$arr = getdate($theTime);
		$theTime = $theTime+86400*((7-$arr["wday"])%7);
		return self::getDateTime($pFlag, $theTime);
	}

	public static function getDateTime($pFlag=10, $pTimeStamp=-1)
	{
		$date_format = "Y-m-d";
		switch($pFlag)
		{
			case 8://20030604
			$date_format = "Ymd";
			break;
			case 10://2003-06-04
			$date_format = "Y-m-d";
			break;
			case 14://20030604201300
			$date_format = "YmdHis";
			break;
			case 19://2003-06-04 20:13:00
			$date_format = "Y-m-d H:i:s";
			break;
			case 21:////2003??06??04??
			$date_format = "Y??m??d??";
			break;
		}
		if($pTimeStamp>=0)
		{
			return date($date_format, $pTimeStamp);
		}
		else
		{
			return date($date_format);
		}
	}
}
?>