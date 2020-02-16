<?php


$options = array(
    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
);
$pdo = new PDO("mysql:host=rmd1:3306;dbname=gitbase", "root", "teliae96", $options);


echo "test";



$sReq = "select commit.sHash, commitdetail.sScript, artefactconsolide.sType, commit.sCommitter from committuleap
join commit using(idcommit)
join commitdetail using(sHash)
join artefactconsolide on(artefactconsolide.id = committuleap.idArtefact)
where artefactconsolide.sType='Bug'";


//$tabCorrespondance = array();
/*while($ligne = $stm->fetch()) {
  $tabCorrespondance[$ligne[1]] = $ligne [0];
}*/

chdir("../../teliway.com/appli/Teliway_Trunk");

$sScript = "autres/gestionGED.php";
$sHash = "e11a24c85a5748d5c20899e94b6efa5b51192478";
$sDate = "2020-01-22 17:37:14";


$tabAnalyse = blame($sScript, $sHash);


var_dump($tabAnalyse);


$sReq = "select commit.sHash from commitdetail
join commit on(commit.sHash = commitdetail.sHash)
join committuleap on (committuleap.idCommit = commit.idCommit)
join artefactconsolide on (committuleap.idArtefact = artefactconsolide.id)
where artefactconsolide.sType = 'Demande' and sScript = ". $pdo->quote($sScript)." AND dDate <= ".$pdo->quote($sDate);

$stm = $pdo->query($sReq);

$tabDemandes = array();
while($ligne = $stm->fetch()) {
  $tabDemandes[$ligne[0]] = blame($sScript, $ligne[0]);

}

var_dump($tabDemandes);

$bActif = true;
//while($bActif) {

  foreach($tabAnalyse as $oAnalyse) {

    var_dump($oAnalyse);

    echo 1;


  }

//}



function blame($p_sScript, $p_sHash) {

  exec("git blame -s -w --line-porcelain ". $p_sHash ." ". $p_sScript ." > analyse.txt");

  $sRet = file_get_contents("analyse.txt");

  $tabLignes = explode("\n", $sRet);

  $tabAnalyse = array();
  $iCpt = 0;
  $bFinBloc = false;
  foreach($tabLignes as $sLigne) {

    //echo $sLigne." ".$iCpt."<br/>";

    if($iCpt == 0) {
      $tabTmp = explode(" ", $sLigne);
      if(!isset($tabTmp[2])) break;

      $sHashLine = $tabTmp[0];
      $iOldLine = $tabTmp[1];
      $iCurrentLine = $tabTmp[2];
    }

    // echo $sHash." ".$iOldLine." ". $iCurrentLine ."<br/>";

    if($bFinBloc) {

      if( $sHashLine == $p_sHash) {
        $tabAnalyse[] = array($iOldLine, $iCurrentLine, $sLigne);
      }

      $bFinBloc = false;
      $iCpt = 0;


    } else {

      $iCpt++;
    }


    if(substr($sLigne, 0, 8) == "filename") {
      $bFinBloc = true;
    }

  }

  return $tabAnalyse;

}


?>