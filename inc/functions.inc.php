<?php
$saison = $_POST['saison'];

require_once dirname(__FILE__) . '/const.inc.php';
require_once dirname(__FILE__) . '/event.cal.class.php';
require_once dirname(__FILE__) . '/parse.cal.class.php';
require_once dirname(__FILE__) . '/reg.cal.class.php';
require_once dirname(__FILE__) . '/dep.cal.class.php';

if ($saison == 2018){
  require_once dirname(__FILE__) . '/2018.inc.php';
}elseif ($saison == 2017) {
  require_once dirname(__FILE__) . '/2017.inc.php';
}else{
  exit("ERREUR: Please call page with good parameters" . EOL);
}
  

/*
function convertDep($objPHPExcel){

  $retEvents = getDepartementalEvents($objPHPExcel);  

  return $retEvents;
}

*/


