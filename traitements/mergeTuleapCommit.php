<?php

$options = array(
  PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
);
$pdo = new PDO("mysql:host=rmd1:3306;dbname=gitbase", "root", "teliae96", $options);


$sReq = "select distinct idCommit, sHash from commit";
$stm = $pdo->query($sReq);

$tabCorrespondance = array();
while($ligne = $stm->fetch()) {
  $tabCorrespondance[$ligne[1]] = $ligne [0];
}


$sReq = "SELECT sHash, sTexte  FROM commit;";
$stm = $pdo->query($sReq);

while($ligne = $stm->fetch()) {

  $tabResult = array();
  preg_match_all('/#(\d+)/',  $ligne[1], $tabResult);

  if (count($tabResult[1]) == 0) continue;

  echo $ligne[0] ."\r\n";
  $idArtefact = $tabResult[1][0];

	echo $idArtefact ."\r\n";

	$pdo->exec("INSERT INTO committuleap VALUES (".$tabCorrespondance[$ligne[0]].",".$idArtefact.");");

	echo "INSERT INTO committuleap VALUES (".$tabCorrespondance[$ligne[0]].",".$idArtefact.");" ."\r\n";

}



?>;