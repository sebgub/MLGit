<?php
$dsn = "mysql:host=localhost:3307;dbname=gitbase";
$user = "root";
$passwd = "";

$pdo = new PDO($dsn, $user, $passwd);

$sFileName = "commit_full";
$start_time = 1514768913;
$end_time = 1581642513;
$ONE_DAY = 90000;   // can't use 86400 because some days have one hour more or less
  for ( $each_timestamp = $start_time ; $each_timestamp <= $end_time ; $each_timestamp +=  $ONE_DAY) {

    /*  force midnight to compensate for daylight saving time  */
    $tabDate = getdate( $each_timestamp );
	
	$sDate = $tabDate['year']."-".str_pad($tabDate['mon'],2,"0",STR_PAD_LEFT )."-".str_pad($tabDate['mday'],2,"0",STR_PAD_LEFT ); 
    
	echo $sDate."\r\n";
	
	$each_timestamp = mktime ( 0 , 0 , 0 , $tabDate['mon'] , $tabDate['mday'] , $tabDate['year'] );

	$sReq = "SELECT
committer_when,
commit_hash,
    JSON_UNQUOTE(JSON_EXTRACT(stats, '$.Path')) AS file_path,
    JSON_UNQUOTE(JSON_EXTRACT(stats, '$.Language')) AS file_language,
    JSON_EXTRACT(stats, '$.Code.Additions') AS code_lines_added,
    JSON_EXTRACT(stats, '$.Code.Deletions') AS code_lines_removed
FROM (
    SELECT
        committer_when,commit_hash,EXPLODE(COMMIT_FILE_STATS(repository_id, commit_hash)) as stats from commits 
		where committer_when like '".$sDate."%' 
		) t
where 1";

		$stm = $pdo->query($sReq);
		
		
		$iCpt = 0;
		while($ligne = $stm->fetch()) {
	
			$str = $ligne[0].";".$ligne[1].";".$ligne[2]."\r\n";
	
	        $iCpt++;
			file_put_contents($sFileName, $str, FILE_APPEND);
	    }
		
		echo "  > ".$iCpt." commits.\r\n";
	
}

	
  

die();


$stm = $pdo->query($sReq);
$sFileName = "commit_".time();
file_put_contents($sFileName, "");
while($ligne = $stm->fetch()) {
	
	$str = $ligne[0].";".$ligne[1].";".$ligne[2]."\r\n";
	
	file_put_contents($sFileName, $str, FILE_APPEND);
	
}


 
die();
$test = "OD311601";
if(preg_match("/OD311601/", $test)) {
	echo "TROUVE";
}

die();

$s = "0003479";
echo ltrim($s, "0");
die();

var_dump(parse_url('ftp://xpo:g15dgd531g@ftp.xpo.com/preannonces'));
die();


$oP = new stdClass;
$oP->a = 1;
$oP->b = 2;



$tab=array($oP);

echo json_encode($tab);


die();

$sUrl = "http://integrateur.station-chargeur.com/integrateur/scripts/integration/integration_auto.php?json=1&idCompte=20036&transid=000002&nomfichier=0015738798&base64=1";
$sPost = "data=UEFSQU07SU5URUdSQVRJT049T1VJO0VUSVE9T1VJO1RZUEVfSU1QUkVTU0lPTj1aUEwNCkJMOzAwMTU3Mzg3OTg7PkNDQS0yMDE5MDMxOC0wMTE7OzkyUFlMT05FUztTVEUgUFlMT05FUzsyLzMgUlVFIERFIE1PTEUtQkFUSU1FTlQgQzQwIEJJUztQWUxPTkVTIEFEUkVTU0UgMjs5MjIzMDtHRU5ORVZJTExJRVJTOzs7Ozs7Mjs7OzEwLjA7Ozs7Ozs7Ozs7QjJCOzs7RVhXOzsyOzs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7DQpVTTs7Ozs7Ozs7O251bGw7O0E7MDAxNTczODc5ODs+Q0NBLTIwMTkwMzE4LTAxMTsxOzs7Ozs7DQpVTTs7Ozs7Ozs7O251bGw7O0E7MDAxNTczODc5ODs+Q0NBLTIwMTkwMzE4LTAxMTsxOzs7Ozs7DQpDT007SU1QOzAwMTU3Mzg3OTg7PkNDQS0yMDE5MDMxOC0wMTE7Ozs7";
echo callCurl($sUrl, $sPost, 'POST');



die();

$str = "10SD1 qsdqsqs qqsqa";

preg_match('/(^.{0,3})/', $str, $matches);

print_r($matches);




die();


$sJson = '';
echo callREST("https://api.insee.fr/entreprises/sirene/V3/siret?q=codeCommuneEtablissement:69001", $sJson, 'GET');

//$tab = array(
/*array("sAction" => "POINTAGE", 
    "sSousAction" => "EVENEMENT",
    "iNumero" => 357661083821719, // 357917080384658,
    "iTimeStamp" => '1552669295',
    "sModeEnvoi" => "DIRECT",
    "sVersion" => "3.2.15",
    "sVersionCode" => "5015",
    "idUtilisateur" => 1111,
    "sUtilisateurPassword" => "1111",
    "sNomConstructeur" => "Zebra Technologies",
    "sTournee" => "220",
    "sNomAppareil" => "TC25",
    "tabComplement" => '[{"DateHeure":"20190318180135","Matricule":"","Immatriculation":"","numRecep":"*33000212224000SEB00011712000300","Statut":"PCHCFM","Latitude":43.42656389,"Longitude":5.23918434,"DateHeureGPS":"20190315180135","Transmis":"N","Altitude":112.33673095703125,"iTypeScan":1,"sDateRdv":"","sKey":"133084105266737001_20190315180135","iNbPhoto":"0","tabSPhoto":"[]"}]'
    ));*/
    
//echo json_encode($tab);

//echo callCurl("http://eurocoop.teliway.com/appli/veurocoop/ws/entrant/Telimobile/v01/pointage.php", $tab, 'POST');

//echo callCurl("http://10.0.21.43/teliway.com/appli/Teliway_Trunk/ws/entrant/Telimobile/v01/pointage.php", $tab, 'POST');

die();




function callCurl($p_sUrl, $p_sJson = false, $p_sMethod = 'POST') {
  
  $headers = array();
  
  
  $oCurlRequest = curl_init();
  
  curl_setopt($oCurlRequest, CURLOPT_URL, $p_sUrl);
  curl_setopt($oCurlRequest, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($oCurlRequest, CURLOPT_CONNECTTIMEOUT, 60);
  curl_setopt($oCurlRequest, CURLOPT_TIMEOUT, 60);
  curl_setopt($oCurlRequest, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($oCurlRequest, CURLOPT_SSL_VERIFYPEER, false);
  
  $oHeaders = array();
  
  
  if($p_sJson != false) {
    curl_setopt($oCurlRequest, CURLOPT_POSTFIELDS, $p_sJson);
    curl_setopt($oCurlRequest, CURLOPT_HTTPGET, false);
    
    if($p_sMethod == 'PUT') {
      curl_setopt($oCurlRequest, CURLOPT_CUSTOMREQUEST, 'PUT');
      
    } else {
      curl_setopt($oCurlRequest, CURLOPT_POST, true);
    }
    
  } else {
    
    if($p_sMethod == 'DELETE') {
      curl_setopt($oCurlRequest, CURLOPT_POST, false);
      curl_setopt($oCurlRequest, CURLOPT_HTTPGET, false);
      curl_setopt($oCurlRequest, CURLOPT_CUSTOMREQUEST, "DELETE");
    } else {
      // GET
      curl_setopt($oCurlRequest, CURLOPT_POST, false);
      curl_setopt($oCurlRequest, CURLOPT_HTTPGET, true);
    }
  }
  
  $sRet = curl_exec($oCurlRequest);
  if(!$sRet) {
   
    
    echo $sRet;

  }
  
  curl_close($oCurlRequest);
  
  return $sRet;
  
}









function callREST( $p_sUrl, $p_sParameters = '', $p_sMethod = 'POST') {
  
 
  

  if($p_sParameters != '') {
    switch ($p_sMethod) {
      
      case 'POST' :
        $opts = array('http' =>
        array(
        'method'  => 'POST',
        'header'  => "Content-Type: application/json\r\n".
        "login: wstelimobileteliway\r\n".
        "password: g8fdjgh3f8kfpoxaza16k1a7\r\n",
        'content' =>$p_sParameters
        )
        );
        break;
      case 'PUT' :
        $opts = array('http' =>
        array(
        'method'  => 'PUT',
        'header'  => "Content-Type: application/json\r\n".
        "login: eurocoop\r\n".
        "password: HYgQBdXE\r\n",
        'content' => $p_sParameters
        )
        );
        break;
      default:
    }
  } else {
    $opts = array('http' =>
        array(
            'method'  => 'GET',
            'header'  => "Content-Type: application/json\r\n".
                         "Authorization: a6da3a9f-d77d-3882-9df1-0f849c7de07a"
        )
    );
  }
  
  $Gcontext  = stream_context_create($opts);
  
  $result = file_get_contents($p_sUrl, false, $Gcontext);
  
  return $result;
  
}








$str = 'L\'ARB EN JASSANS(';

//preg_match('/(?P<name>\w+): (?P<digit>\d+)/', $str, $matches);

echo preg_match('/(?P<localite>[a-zA-Z-é\'èçàù\-_\s]+)(\(.|[\sa-zA-Z]*)/', $str, $matches);

print_r($matches['localite']);
die();















$sJson = file_get_contents('json.txt');


$tab = json_decode($sJson);
var_dump($tab->assets);
if(!is_array($tab->assets)) print "KO";

$tab1 = array();
$tab1 = array_merge($tab, $tab1);

die();














$sLigne = '12d32 54777 azand';
$sPID = preg_match("/^[0-9]+/", $sLigne, $match);

var_dump($match);

/*
$t = array('sImei' => '1145456458',
           'idVoyage' => '123',
		   'idDo', => 137,
		   'sCodeLigne' => 'T01',
		   'iIndiceLigne' => '2',
		   'sCodeChauffeur' => 'C1',
		   'traces' => array (
		      array('fLatitude' => 2.464,
			        'fLongitude' => 1.45646,
					'fPrecision' => 10.0,
					'fVitesse' => 20.7,
					'iStatellite' => 3,
					'fCap' => 111.3,
					'sDateHeure' => '2018-10-26T15:42:00+02:00'					
			        ),
					
				array('fLatitude' => 2.470,
				'fLongitude' => 1.476,
				'fPrecision' => 11.0,
				'fVitesse' => 30.1,
				'iStatellite' => 3,
				'fCap' => 110.1,
				'sDateHeure' => '2018-10-26T15:43:00+02:00'					
				)
		     )
		   
		   );
		   
		   echo json_encode($t);

*/
?>