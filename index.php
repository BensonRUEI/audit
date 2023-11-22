<?php
include('config.php');
include('header.php');
echo <<<HTML
<h1>使用者電腦列表</h1>
<FORM method="POST" action="index.php">
<input type="submit" name="delCheck" value="刪除勾選"> <input type="submit" name="delAll" value="刪除所有"><BR /><BR />
<table style="width: 100%; font-size: 14pt; border-collapse:collapse;">
<tr style="background-color:#009879; height: 30pt; color: white;">
<th> </th>
<th>IP</th>
<th>姓名</th>
<th>單位</th>
<th>類型</th>
</tr>

HTML;

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$link = new mysqli($DB_HOST, $DB_USER, $DB_PASS, "audit");

if($_POST['delCheck']) {
   foreach($_POST['id'] as $k=>$v) {
      $result = $link->query("DELETE FROM Computer where ID='".$k."'");
   }
}

if($_POST['delAll']) {
   $result = $link->query("DELETE FROM Computer");  
}

$i = 0;
$result = $link->query("SELECT * FROM Computer");
while($temp_arr = $result->fetch_array()){
   $i++;
   echo "<TR style=\"text-align: center;\"><TD><input type=\"checkbox\" name=\"id[".$temp_arr[0]."]\"></TD><TD>".$temp_arr[3]."</TD><TD>".$temp_arr[2]."</TD><TD>".$temp_arr[1]."</TD><TD>".$temp_arr[4]."</TD></TR>\n";
}
if($i==0) echo "<TR><TD colSpan=5 style=\"text-align: center;\">請<a href=\"import.php\">匯入資料</a></TD></TR>";
echo "</TABLE></FORM><BR />";

