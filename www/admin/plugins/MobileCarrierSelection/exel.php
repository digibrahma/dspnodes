<?php

// Require the initialisation file
require_once '../../../../init.php';

// Include required files
require_once MAX_PATH . '/lib/pear/Spreadsheet/Excel/Writer.php';
//(campaignid,banner,Statustext,Comments,keyword,bannertext,bannerid,	url1,weight1,url2,weight2,url3,weight3,url4,weight4,Ads,Reports
if(isset($_POST['exl']))
{
$final='';
$det=$_POST['data'];

$data=explode('=>',$det);


$title=strtoupper("Mobile Carrier Uploaded details");


$rows=array('start_ip','end_ip','country','carriername','StatusText');

$workbook = new Spreadsheet_Excel_Writer();
$workbook->send('Carrier_upload_'.$type.'_Reports'.'.xls'); 
$worksheet =& $workbook->addWorksheet("My Worksheet"); 
$fmt_title =& $workbook->addFormat();
$fmt_title->setBold();
$fmt_title->setSize(12);
$fmt_title->setFgColor(44);
$fmt_title->setPattern(1);
$fmt_title->setTop(2);
$fmt_title->setLeft(2);
$fmt_title->setBottom(2);


$fmt_title1 =& $workbook->addFormat();
$fmt_title1->setBold();
$fmt_title1->setSize(10);
$fmt_title1->setFgColor(44);
$fmt_title1->setPattern(1);
$fmt_title1->setTop(2);
$fmt_title1->setBottom(2);

$fmt_title2 =& $workbook->addFormat();
$fmt_title2->setBold();
$fmt_title2->setSize(10);
$fmt_title2->setFgColor(44);
$fmt_title2->setPattern(1);
$fmt_title2->setTop(2);
$fmt_title2->setRight(2);
$fmt_title2->setBottom(2);


$fmt_titled3 =& $workbook->addFormat();
$fmt_titled3->setBold();
$fmt_titled3->setSize(10);
$fmt_titled3->setFgColor(44);
$fmt_titled3->setTop(2);
$fmt_titled3->setPattern(2);
$fmt_titled3->setAlign('center');
//$fmt_titled3->setTop(2);
$fmt_titled3->setLeft(2);
$fmt_titled4 =& $workbook->addFormat();
$fmt_titled4->setBold();
$fmt_titled4->setSize(10);
$fmt_titled4->setFgColor(44);
$fmt_titled4->setPattern(2);
$fmt_titled4->setAlign('center');
$fmt_titled4->setTop(2);
$fmt_titled5 =& $workbook->addFormat();
$fmt_titled5->setBold();
$fmt_titled5->setSize(10);
$fmt_titled5->setFgColor(44);
$fmt_titled5->setPattern(2);
$fmt_titled5->setRight(2);
$fmt_titled5->setAlign('center');
$fmt_titled5->setTop(2);




$fmt_title3 =& $workbook->addFormat();

$fmt_title3->setSize(10);
$fmt_title3->setFgColor('white');
//$fmt_title3->setTop(2);
$fmt_title3->setPattern(2);
$fmt_title3->setAlign('center');
$fmt_title3->setBottom(1);
//$fmt_title3->setTop(2);
$fmt_title3->setLeft(2);
$fmt_title4 =& $workbook->addFormat();

$fmt_title4->setSize(10);
$fmt_title4->setFgColor('white');
$fmt_title4->setPattern(2);
$fmt_title4->setAlign('center');
$fmt_title4->setBottom(1);
//$fmt_title4->setTop(2);
$fmt_title5 =& $workbook->addFormat();

$fmt_title5->setSize(10);
$fmt_title5->setFgColor('white');
$fmt_title5->setPattern(2);
$fmt_title5->setRight(2);
$fmt_title5->setBottom(1);
$fmt_title5->setAlign('center');
//$fmt_title5->setTop(2);
$fmt_title6 =& $workbook->addFormat();

$fmt_title6->setSize(10);
$fmt_title6->setFgColor('');
$fmt_title6->setPattern(2);
$fmt_title6->setBottom(2);
$fmt_title6->setAlign('center');
$fmt_title7 =& $workbook->addFormat();

$fmt_title7->setSize(10);
$fmt_title7->setFgColor('white');
$fmt_title7->setPattern(2);
$fmt_title7->setBottom(2);
$fmt_title7->setLeft(2);
$fmt_title7->setAlign('center');
$fmt_title8 =& $workbook->addFormat();

$fmt_title8->setSize(10);
$fmt_title8->setFgColor('white');
$fmt_title8->setPattern(2);
$fmt_title8->setBottom(2);
$fmt_title8->setRight(2);
$fmt_title8->setAlign('center');
// Write using the Title format
/*if($type=='Weight')
{
$worksheet->write(2,2,$title,$fmt_title); 
for($i=3;$i<19;$i++)
{
if($i!=18)
{
$worksheet->write(2,$i,'',$fmt_title1);
}
else
{
$worksheet->write(2,$i,'',$fmt_title2);
}
}
$rw=6;
$cl=2;
for($l=0;$l<17;$l++)
{
if($cl==2)
{
$worksheet->write($rw,$cl,$rows[$l],$fmt_titled3);
}
else if($cl==18)
{
$worksheet->write($rw,$cl,$rows[$l],$fmt_titled5);
}
else
{
$worksheet->write($rw,$cl,$rows[$l],$fmt_titled4);

}
$cl++;
}
$num=count($data);
$drw=7;
$totd=$num+$drw;
for($m=0;$m<$num-1;$m++)
{
$cm=2;
$vdata=explode(',',$data[$m]);
$inum=count($vdata);
for($n=0;$n<$inum;$n++)
{

if($n==0)
{
if($drw==($totd-2))
{
$worksheet->write($drw,$cm,stripslashes($vdata[$n]),$fmt_title7);
}
else
{
$worksheet->write($drw,$cm,stripslashes($vdata[$n]),$fmt_title3);
}
}else if($n==$inum-1)
{
if($drw==($totd-2) )
{
$worksheet->write($drw,$cm,stripslashes($vdata[$n]),$fmt_title8);
}
else
{
$worksheet->write($drw,$cm,stripslashes($vdata[$n]),$fmt_title5);
}
}else if($drw==($totd-2))
{
$worksheet->write($drw,$cm,stripslashes($vdata[$n]),$fmt_title6);
}
else
{
$worksheet->write($drw,$cm,stripslashes($vdata[$n]),$fmt_title4);
}
$cm++;
}
$drw++;
}

}
*/


$worksheet->write(2,2,$title,$fmt_title); 
for($i=3;$i<7;$i++)
{
if($i!=6)
{
$worksheet->write(2,$i,'',$fmt_title1);
}
else
{
$worksheet->write(2,$i,'',$fmt_title2);
}
}
$rw=6;
$cl=2;
for($l=0;$l<5;$l++)
{
if($cl==2)
{
$worksheet->write($rw,$cl,$rows[$l],$fmt_titled3);
}
else if($cl==6)
{
$worksheet->write($rw,$cl,$rows[$l],$fmt_titled5);
}
else
{
$worksheet->write($rw,$cl,$rows[$l],$fmt_titled4);

}
$cl++;
}
$num=count($data);
$drw=7;
$totd=$num+$drw;
for($m=0;$m<$num-1;$m++)
{
$cm=2;
$vdata=explode(',',$data[$m]);
$inum=count($vdata);
for($n=0;$n<$inum;$n++)
{

if($n==0)
{
if($drw==($totd-2))
{
$worksheet->write($drw,$cm,$vdata[$n],$fmt_title7);
}
else
{
$worksheet->write($drw,$cm,$vdata[$n],$fmt_title3);
}
}else if($n==($inum-1))
{
if($drw==($totd-2))
{
$worksheet->write($drw,$cm,$vdata[$n],$fmt_title8);
}
else
{
$worksheet->write($drw,$cm,$vdata[$n],$fmt_title5);
}
}else if($drw==($totd-2))
{
$worksheet->write($drw,$cm,$vdata[$n],$fmt_title6);
}
else
{
$worksheet->write($drw,$cm,$vdata[$n],$fmt_title4);
}
$cm++;
}
$drw++;
}






$workbook->close(); 

}
?>
