<?php

//test : http://10.0.21.43/MLGit/traitements/analyzeBug.php?idCommit=225
// http://10.0.21.43/MLGit/traitements/analyzeBug.php?idCommitDetail=47202   ====> bug: f246b470fa1a4543bd858fb1ad96e98c7fdd7b24     evol: c72922b9c3f07a161ab03e035ef964a0082b5f2c
// http://10.0.21.43/MLGit/traitements/analyzeBug.php?idCommitDetail=46435


$idCommit = false;
if(isset($_GET['idCommit'])) {
  $idCommit = $_GET['idCommit'];
}

$idCommitDetail = false;
if(isset($_GET['idCommitDetail'])) {
  $idCommitDetail = $_GET['idCommitDetail'];
}


chdir("../../teliway.com/appli/Teliway_Trunk");


CAnalyzeGit::execute($idCommit, $idCommitDetail);


/*$sScript = "autres/gestionGED.php";
$sHash = "e11a24c85a5748d5c20899e94b6efa5b51192478";
$sDate = "2020-01-22 17:37:14";
*/

class CAnalyzeGit {

  static $sMode = "console";


  public static function execute($p_idCommit = false, $p_idCommitDetail = false) {

    file_put_contents("analyse.log", "");

    $options = array(
        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
    );
    $pdo = new PDO("mysql:host=rmd1:3306;dbname=gitbase", "root", "teliae96", $options);

    $sReq = "select commitdetail.idCommitDetail, commit.idCommit, commit.sHash, commitdetail.sScript, commit.dDate from committuleap
join commit using(idcommit)
join commitdetail using(sHash)
join artefactconsolide on(artefactconsolide.id = committuleap.idArtefact)
where artefactconsolide.sType='Bug' and sScript NOT IN ('sql/tabProps.php', 'sql/tabSQL.php')";

    if($p_idCommit !== false) {
      $sReq .= " AND commit.idCommit = ".(int) $p_idCommit;
    }

    if($p_idCommitDetail !== false) {
      $sReq .= " AND commitdetail.idCommitDetail = ".(int) $p_idCommitDetail;
    }

    //$sReq .= " LIMIT 300";

    self::trace($sReq);
    $stm = $pdo->query($sReq);

    //$tabCorrespondance = array();

    while($ligne = $stm->fetch()) {
      CAnalyzeGit::analyzeBug($ligne[0], $ligne[1], $ligne[2], $ligne[3], $ligne[4]);
    }


  }



  public static function trace($p_sMessage) {

    switch(self::$sMode) {

      case "web": echo $p_sMessage."<br/>"; break;
      case "console": echo $p_sMessage."\r\n"; break;
    }

    file_put_contents("analyse.log", date('Y-m-d h:i:s')." : ".$p_sMessage."\r\n", FILE_APPEND);

  }

  public static function analyzeBug($p_idCommitDetail, $p_idCommit, $p_sHash, $p_sScript, $p_dDate) {

   self::trace('-------------------------------------------------------');
   self::trace( "#".$p_idCommitDetail." / ".$p_sHash." / ".$p_sScript." / ".$p_dDate);

   $tabAnalyse = self::blame($p_sScript, $p_sHash);

   //var_dump($tabAnalyse);

   $options = array(
       PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
   );

   $pdo = new PDO("mysql:host=rmd1:3306;dbname=gitbase", "root", "teliae96", $options);
   $sReq = "select commit.sHash from commitdetail
            join commit on(commit.sHash = commitdetail.sHash)
            join committuleap on (committuleap.idCommit = commit.idCommit)
            join artefactconsolide on (committuleap.idArtefact = artefactconsolide.id)
            where artefactconsolide.sType = 'Demande' and sScript = ". $pdo->quote($p_sScript)." AND dDate <= ".$pdo->quote($p_dDate);

   $stm = $pdo->query($sReq);

   $tabDemandes = array();
   while($ligne = $stm->fetch()) {
     //$tabDemandes[$ligne[0]] = blame($sScript, $ligne[0]);
     $tabDemandes[$ligne[0]] = false;
   }

   //var_dump($tabDemandes);

   $tabBugs = array();
   foreach($tabAnalyse as $oBlame) {

    //    echo $oBlame->sPrevious.'<br/>';

        $tabImpacts = self::blame($p_sScript, $oBlame->sPrevious, $oBlame->iCurrentLine, true);

        //var_dump($tabImpacts);

        if(!is_array($tabImpacts) || empty($tabImpacts)) continue;

        if(isset($tabDemandes[$tabImpacts[0]->sHash])) {
          $tabDemandes[$tabImpacts[0]->sHash] = true;
          self::trace('   > Impact: '.$tabImpacts[0]->sHash." / Ligne: ".$oBlame->iCurrentLine);

          $tabBugs[] = $tabImpacts[0]->sHash;
        }

   }

   //self::trace('-------------------------------------------------------');
   // var_dump($tabDemandes);

   $stm = $pdo->query("UPDATE commitdetail set bProcessed = 1 WHERE sHash = ".$pdo->quote($p_sHash)." AND sScript = ".$pdo->quote($p_sScript));

   foreach($tabBugs as $sHash) {

     $stm = $pdo->query("UPDATE commitdetail set bBug = 1, bProcessed = 1 WHERE sHash = ".$pdo->quote($sHash)." AND sScript = ".$pdo->quote($p_sScript));

   }


 }


 public static function blame($p_sScript, $p_sHash, $p_iLigne = false, $p_bAllLines = false) {

  $sCommand = "git blame -s -w --line-porcelain ". $p_sHash ." ". $p_sScript;

  if($p_iLigne !== false) {
    $sCommand .= " -L ".$p_iLigne.",".$p_iLigne;
  }
  $sRet = shell_exec($sCommand);

  //self::trace($sCommand);
  //$sRet = file_get_contents("analyse.txt");

  $tabLignes = explode("\n", $sRet);

  $tabAnalyse = array();
  $iCpt = 0;
  $bFinBloc = false;
  $sPrevious = '';
  foreach($tabLignes as $sLigne) {

    //echo $sLigne." ".$iCpt."<br/>";

    if($iCpt == 0) {
      $tabTmp = explode(" ", $sLigne);
      if(!isset($tabTmp[2])) break;

      $sHashLine = $tabTmp[0];
      $iOriginalLine = $tabTmp[1];
      $iCurrentLine = $tabTmp[2];
      $sPrevious = '';
    }

    // echo $sHash." ".$iOldLine." ". $iCurrentLine ."<br/>";

    if($bFinBloc) {

      if( $sHashLine == $p_sHash || $p_bAllLines) {
        $oBlame = new CBlameObjet($sHashLine, $iOriginalLine, $iCurrentLine, $sLigne, $sPrevious);
        $tabAnalyse[] = $oBlame;
      }

      $bFinBloc = false;
      $iCpt = 0;


    } else {

      $iCpt++;
    }


    if(substr($sLigne, 0, 8) == "previous") {
      $tabPrevious = explode(" ", $sLigne);
      $sPrevious = $tabPrevious[1];
    }

    if(substr($sLigne, 0, 8) == "filename") {
      $bFinBloc = true;
    }

  }

  return $tabAnalyse;

}

}

class CBlameObjet {

  public function __construct($sHash, $iOriginalLine, $iCurrentLine, $sLigne, $sPrevious) {

    $this->sHash = $sHash;
    $this->sLine = $sLigne;
    $this->sPrevious = $sPrevious;
    $this->iCurrentLine = $iCurrentLine;
    $this->iOriginalLine = $iOriginalLine;

  }

  public $sHash;
  public $sLine;
  public $iCurrentLine;
  public $iOriginalLine;
  public $sPrevious;

}

?>