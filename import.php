<?php
include('config.php');

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$link = new mysqli($DB_HOST, $DB_USER, $DB_PASS, "audit");

if ($link->connect_error) {
   echo "Not connected. error: ". $link->connect_error;
}

if($_POST['export1']) {
   $result = $link->query("SELECT * FROM Computer");
   $temp_str = mb_convert_encoding("IP,單位,姓名,類型\r\n","BIG5","UTF-8");
   while($temp_arr = $result->fetch_array()){
      foreach($temp_arr as $k=>$v) $temp_arr[$k] = mb_convert_encoding($v,"BIG5","UTF-8");
      $temp_str .= $temp_arr['Host'].",".$temp_arr['Unit'].",".$temp_arr['Name'].",".$temp_arr['Property']."\r\n";
   }
   header("Content-type: text/x-csv");
   header("Content-Disposition: attachment; filename=Computer.csv");
   echo $temp_str;
   exit; 

} elseif($_POST['export2']) {
   $result = $link->query("SELECT * FROM Detail");
   $temp_str = "Risk,Host,Protocol,Port,Name\r\n";
   while($temp_arr = $result->fetch_array()){
      foreach($temp_arr as $k=>$v) $temp_arr[$k] = mb_convert_encoding($v,"BIG5","UTF-8");
      $temp_str .= $temp_arr['Risk'].",".$temp_arr['Host'].",".$temp_arr['Protocol'].",".$temp_arr['Port'].",".$temp_arr['Name']."\r\n";
   }
   header("Content-type: text/x-csv");
   header("Content-Disposition: attachment; filename=Detail.csv");
   echo $temp_str;
   exit;

} elseif($_POST['demo1']) {
   header("Content-type: text/x-csv");
   header("Content-Disposition: attachment; filename=Demo1.csv");
   header("Pragma: no-cache");
   header("Expires: 0");
   readfile('Computer_demo.csv');
   exit;

} elseif($_POST['demo2']) {
   header("Content-type: text/x-csv");
   header("Content-Disposition: attachment; filename=Demo2.csv");
   header("Pragma: no-cache");
   header("Expires: 0");
   readfile('Detail_demo.csv');
   exit;

}

include('header.php');
echo <<<HTML
<h1>資料匯入</h1>
<h3>請先選檔案再點選匯入檔案類別：</h3>
<form action="import.php" method="post" enctype="multipart/form-data">
<input type="file" name="filename"> 
<input type="submit" value="上傳使用者清單" name="user"> 
<input type="submit" value="上傳Nessus結果" name="detail">
<BR /><hr style="border: 1px dashed grey;">
範例檔：
<input type="submit" value="使用者清單範例" name="demo1"> 
<input type="submit" value="Nessus結果範例" name="demo2">
</form><BR />
<h1>資料重整</h1>
<form action="import.php" method="post">
<input type="submit" value="去除資料IP的前置0" name="erase0">
<input type="submit" value="去除通信埠為0的掃描資料" name="erase1">
</form><BR />
<h1>資料匯出</h1>
<form action="import.php" method="post">
<input type="submit" value="使用者電腦資料匯出" name="export1">
<input type="submit" value="弱點資料匯出" name="export2">
</form><BR />
HTML;

if($_POST['erase0']) {
   $result = $link->query("SELECT * FROM Computer");
   while($temp_arr = $result->fetch_array()){
      $temp_arr2 = explode(".", $temp_arr[3]);
      $IP = "";
      foreach($temp_arr2 as $k => $v) $temp_arr2[$k] =intval($v);
      $IP = implode(".",$temp_arr2);
      $result2 = $link->query("UPDATE Computer SET Host='".$IP."' where ID='".$temp_arr[0]."'");
   }
   echo "資料重整完成";

} elseif ($_POST['erase1']) {
   $result = $link->query("DELETE FROM Detail where Port='0'");
   echo "資料重整完成";

} elseif($_FILES['filename']['error'] == 0 && $_FILES['filename']['size'] > 0) {
   $csvToRead = fopen($_FILES['filename']['tmp_name'], 'r');
//   $file_encoding = mb_detect_encoding($csvToRead);
//   echo $file_encoding."\n";

   $p_arr = array("Critical" => 0, "High" => 1, "Medium" => 2, "Low" => 3, "None" => 4);
   while ($temp_arr = my_fgetcsv($csvToRead, 1000, ',')) {
      foreach($temp_arr as $k=>$v) $temp_arr[$k] = mb_convert_encoding($v,"UTF-8","BIG5");
      if ($_POST['user']=="上傳使用者清單") {
         if(trim($temp_arr[0])=="IP") continue;
         $stmt = mysqli_prepare($link, "INSERT INTO Computer (Unit, Name, Host, Property) VALUES (?, ?, ?, ?)");
         mysqli_stmt_bind_param($stmt, 'ssss', $temp_arr[1], $temp_arr[2], trim($temp_arr[0]), $temp_arr[3]);
      } elseif ($_POST['detail']=="上傳Nessus結果") {
         $stmt = mysqli_prepare($link, "INSERT INTO Detail (Risk, Host, Protocol, Port, Name, Priority) VALUES (?, ?, ?, ?, ?, ?)");
         if($temp_arr[1]=="Host") continue;
         mysqli_stmt_bind_param($stmt, 'sssssi', $temp_arr[0], $temp_arr[1], $temp_arr[2], $temp_arr[3], $temp_arr[4], $p_arr[$temp_arr[0]]);
      }
      mysqli_stmt_execute($stmt);

      print_r($temp_arr); echo "<BR />\n";
      $temp_arr2[] = $temp_arr;
   }
   fclose($csvToRead);
}

//print_r($_POST);
//print_r($_FILES);
//prrint_r($temp_arr2);

function my_fgetcsv(&$handle, $length = null, $d = ",", $e = '"') {
    $d = preg_quote($d);
    $e = preg_quote($e);
    $_line = "";
    $eof=false;
    while ($eof != true) {
        $_line .= (empty ($length) ? fgets($handle) : fgets($handle, $length));
        $itemcnt = preg_match_all('/' . $e . '/', $_line, $dummy);
        if ($itemcnt % 2 == 0)
            $eof = true;
    }
   $_csv_line = preg_replace('/(?: |[ ])?$/', $d, trim($_line));
    $_csv_pattern = '/(' . $e . '[^' . $e . ']*(?:' . $e . $e . '[^' . $e . ']*)*' . $e . '|[^' . $d . ']*)' . $d . '/';
    preg_match_all($_csv_pattern, $_csv_line, $_csv_matches);
    $_csv_data = $_csv_matches[1];


    for ($_csv_i = 0; $_csv_i < count($_csv_data); $_csv_i++) {
        $_csv_data[$_csv_i] = preg_replace("/^" . $e . "(.*)" . $e . "$/s", "$1", $_csv_data[$_csv_i]);
        $_csv_data[$_csv_i] = str_replace($e . $e, $e, $_csv_data[$_csv_i]);
    }
    return empty ($_line) ? false : $_csv_data;
}
