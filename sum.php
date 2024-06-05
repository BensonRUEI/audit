<?php
include('config.php');
include('header.php');
echo <<<HTML
<h1>資料彙總</h1>
<FORM method="POST" action="sum.php">
<input type="submit" name="all" value="列出全部"> 
<input type="submit" name="onlyHM" value="僅列中高"> 
<input type="submit" name="onlyTotal" value="僅列統計"> 
<input type="submit" name="sortByIP" value="依IP排序統計">
<BR /><BR />
<table style="width: 100%; font-size: 14pt; border-collapse:collapse;">
<tr style="background-color:#009879; height: 30pt; color: white;">
HTML;

if($_POST['onlyTotal'] || $_POST['sortByIP']) 
echo <<<HTML
<th>No.</th>
<th>Unit</th>
<th>Host</th>
<th>High</th>
<th>Medium</th>
</tr>
HTML;
else
echo <<<HTML
<th>No.</th>
<th>Unit</th>
<th>Host</th>
<th>Risk</th>
<th>Port</th>
<th>Name</th>
</tr>
HTML;

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$link = new mysqli($DB_HOST, $DB_USER, $DB_PASS, "audit");
if ($_POST['sortByIP'])
   $result = $link->query("SELECT * FROM Detail ORDER BY Host, Priority");
else 
   $result = $link->query("SELECT * FROM Detail ORDER BY Priority, Host");
while($temp_arr = $result->fetch_array()){
   $sum_arr[$temp_arr['Host']] = array();
}

$result = $link->query("SELECT Detail.*,Computer.Unit FROM Detail LEFT JOIN Computer ON Detail.Host = Computer.Host ORDER BY Detail.Priority");
while($temp_arr = $result->fetch_array()){
   $sum_arr[$temp_arr['Host']][$temp_arr['Risk']][] = array('port' => $temp_arr['Port'], 'name' => $temp_arr['Name']);
   $sum_arr[$temp_arr['Host']]['Unit'] = $temp_arr['Unit'];
   if(in_array($temp_arr['Risk'], array('Critical','High'))) $sum_arr[$temp_arr['Host']]['HSUM'] += 1; 
   elseif($temp_arr['Risk'] == "Medium") $sum_arr[$temp_arr['Host']]['MSUM'] += 1;
}

$i = 1;
foreach($sum_arr as $h => $v) {
   if ($_POST['onlyTotal'] || $_POST['sortByIP']) {
      echo "<TR style=\"text-align: center;\"><TD>".$i."</TD><TD>".$v['Unit']."</TD><TD>".$h."</TD><TD>".intval($v['HSUM'])."</TD><TD>".intval($v['MSUM'])."</TD></TR>\n";
      $i++;
   } else {
      foreach($v as $r => $vv) {
         if(in_array($r, array('Unit', 'HSUM', 'MSUM'))) continue;
         foreach($vv as $n => $d) {
            if($_POST['onlyHM'] && in_array($r, array('Low', 'None'))) continue;
            echo "<TR style=\"text-align: center;\"><TD>".$i."</TD><TD>".$v['Unit']."</TD><TD>".$h."</TD><TD>".$r."</TD><TD>".$d['port']."</TD><TD>".$d['name']."</TD></TR>\n";
         }
      }
      $i++;
   }
}

echo "</TABLE></FORM><BR />";

$result = $link->query("SELECT distinct(Host) FROM Detail GROUP BY Host");
echo "<span style=\"font-size: 14pt;\">裝置總數：".$result->num_rows;
if($result->num_rows == 0) echo ", 請<a href=\"import.php\">匯入資料</a>";
echo "</span>";
