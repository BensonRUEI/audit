<?php
include('config.php');
include('header.php');
echo <<<HTML
<h1>Nessus掃描結果</h1>
<FORM method="POST" action="detail.php">
<input type="submit" name="delCheck" value="刪除勾選"> <input type="submit" name="delAll" value="刪除所有"> 
<input type="checkbox" name="relation">刪除同IP資料<BR /><BR />
<table style="width: 100%; font-size: 14pt; border-collapse:collapse;">
<tr style="background-color:#009879; height: 30pt; color: white;">
<th> </th>
<th>Risk</th>
<th>Host</th>
<th>Protocol</th>
<th>Port</th>
<th>Name</th>
</tr>

HTML;

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$link = new mysqli($DB_HOST, $DB_USER, $DB_PASS, "audit");

if($_POST['delCheck']) {
   foreach($_POST['id'] as $k=>$v) {
      if($_POST['relation']) {
         $result = $link->query("SELECT Host FROM Detail where ID='".$k."'");
         $temp_arr = $result->fetch_array();
         $result = $link->query("DELETE FROM Detail where Host='".$temp_arr[0]."'");
      } 
      $result = $link->query("DELETE FROM Detail where ID='".$k."'");
   }
}

if($_POST['delAll']) {
   $result = $link->query("DELETE FROM Detail");  
}

$result = $link->query("SELECT * FROM Detail ORDER BY Risk,Host");
while($temp_arr = $result->fetch_array()){
print_r($obj);
   echo "<TR style=\"text-align: center;\"><TD><input type=\"checkbox\" name=\"id[".$temp_arr[0]."]\"></TD><TD>".$temp_arr[1]."</TD><TD>".$temp_arr[2]."</TD><TD>".$temp_arr[3]."</TD><TD>".$temp_arr[4]."</TD><TD>".$temp_arr[5]."</TD></TR>\n";
}
echo "</TABLE></FORM><BR />";

$result = $link->query("SELECT distinct(Host) FROM Detail GROUP BY Host");
echo "裝置總數：".$result->num_rows;
if($result->num_rows == 0) echo ", 請<a href=\"import.php\">匯入資料</a>";
