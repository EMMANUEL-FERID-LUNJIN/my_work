File: test.php
<?php

include_once"dosql.php";
#
#   Put your own database information here.  I'm using my log file's data.
#
$hostname="myhost";
$username="myUser";
$password="myPassword";
$database="myDatabase";

$mysqli=newmysqli($hostname,$username,$password,$database);

if($mysqli->connect_errno){
echo"Failed to connect to MySQL: (",$mysqli->connect_errno,") ",$mysqli->connect_error;
exit;
}
echo"SQL INJECTION - Plain\n";
$sql="SELECT * FROM log WHERE log_id='2' OR 1=1; #'";
$res=dosql($sql);
foreach($res[0]as$k=>$v){
echo"RES[$k] = $v\n";
}

echo"\n\nSQL INJECTION = Hexadecimal\n";
$sql="SELECT * FROM log WHERE log_id=unhex('".bin2hex("2' or 1=1; #'")."')";
$res=dosql($sql);
foreach($res[0]as$k=>$v){
echo"RES[$k] = $v\n";
}

exit;
?>

File: dosql.php
<?php

################################################################################
#   dosql(). Do the SQL command.
################################################################################
functiondosql($sql)
{
global$mysqli;

$cmd="INSERT INTO log (date,entry) VALUES (NOW(), unhex('".bin2hex($sql)."'))";
$res=$mysqli->query($cmd);

$res=$mysqli->query($sql);
if(!$res){
$array=debug_backtrace();
if(isset($array[1])){$a=$array[1]['line'];}
elseif(isset($array[0])){$a=$array[0]['line'];}
else{$a="???";}

echo"ERROR @ ",$a," : (",$mysqli->errno,")\n",$mysqli->error,"\n\n";
echo"SQL = $sql\n";
exit;
}

if(preg_match("/INSERT/i",$sql)){return$mysqli->insert_id;}
if(preg_match("/DELETE/i",$sql)){returnnull;}
if(!is_object($res)){returnnull;}

$count=-1;
$array=array();
$res->data_seek(0);
while($row=$res->fetch_assoc()){
$count++;
foreach($rowas$k=>$v){$array[$count][$k]=$v;}
}

return$array;
}
Program output
SQL INJECTION - Plain
RES[log_id] = 1
RES[date] = 2015-03-25 10:40:18
RES[entry] = SHOW full columns FROM log

SQL INJECTION = Hexadecimal
RES[log_id] = 2
RES[date] = 2015-03-25 10:40:18
RES[entry] = SELECT * FROM log ORDER BY title ASC
