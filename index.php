<?php

$file=fopen("sms-20120708160401.xml", "w");
$xml=simplexml_load_file("messages.xml");
echo $xml->getName()."<br/><br/>";
$begin="<?xml version='1.0' encoding='UTF-8' standalone='yes' ?>\n";
$begin.='<?xml-stylesheet type="text/xsl" href="sms.xsl"?>'."\n";
$begin.='<smses count="">'."\n";
fwrite($file,$begin);
$start='  <sms protocol="0" ';
$finish='/>';
$i=1;
$j=0;

foreach($xml->children()->children() as $table) {
	echo $i++."  ";
	$read=1;
	$msg_status=$table->column[5];
	$msg_folder=$table->column[2];
	if($msg_status=="34") {
		$type=1;
		$read=0;
	}
	else if($msg_status=="36") {
		if($msg_folder=="4294967295") {
			$type=3;
		}
		else {
			$type=1;
		}
	}
	else if($msg_status=="1" || $msg_status=="5") {
		if($msg_folder=="4294967295") {
			$type=3;
		}
		else {
			$type=2;
		}
	}
	else {
		echo "<br/>".$msg_status."; ".$msg_folder."<br/><br/>";
	}
	$body=htmlspecialchars($table->column[0]);
	$body=str_replace("\n", '&#10;', $body);
	$address=$table->column[1];
	if(strlen($address)==11) {
		$address="+91".substr($address,1);
	}
	else if(strlen($address)==10) {
		$address="+91".$address;
	}
	else if($address=="") {
		$i--;
		continue;
	}

	$sms=$start;
	$sms.='address="'.$address.'" ';
	$sms.='date="'.$table->column[3].'000" ';
	$sms.='type="'.$type.'" ';
	$sms.='subject="null" ';
	$sms.='body="'.$body.'" ';
	$sms.='toa="null" ';
	$sms.='sc_toa="null" ';
	$sms.='service_center="null" ';
	$sms.='read="'.$read.'" ';
	$sms.='status="-1" ';
	$sms.='locked="0" ';
	$sms.='date_sent="null" ';
	$sms.=$finish."\n";
	
	fwrite($file, $sms);
}

$end='</smses>';
fwrite($file, $end);
fclose($file);

?>