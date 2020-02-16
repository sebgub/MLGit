<?php
$dsn = "mysql:host=localhost:3307;dbname=gitbase";
$user = "root";
$passwd = "";

$pdo = new PDO($dsn, $user, $passwd);

$options = array(
  PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
);
$pdoLocal = new PDO("mysql:host=rmd1:3306;dbname=gitbase", "root", "teliae96", $options);

$sReq = "SELECT commit_hash, committer_when, committer_name, commit_message  FROM commits";

$stm = $pdo->query($sReq);
		
	
while($ligne = $stm->fetch()) {

	$str = "INSERT INTO commit (sHash, dDate, sCommitter, sTexte) ";
	$str.= "VALUES (".$pdoLocal->quote($ligne[0]).",";
	$str.= $pdoLocal->quote($ligne[1]).",";
	$str.= $pdoLocal->quote($ligne[2]).",";
	$str.= $pdoLocal->quote($ligne[3]).")";

	echo $str."\r\n";
	$pdoLocal->exec($str);
	
}
	

	
?>;