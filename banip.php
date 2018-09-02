<?php
$ip=$_SERVER["REMOTE_ADDR"]; 
$ban=file_get_contents("blacklist"); 
if(stripos($ban,$ip)) 
{ 
  die("你的IP:$ip,已经被列入黑名单");   
} 
?>
