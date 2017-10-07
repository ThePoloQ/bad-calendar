<?php

error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);

define('EOL',"\r\n");

date_default_timezone_set('Europe/London');

/** Include PHPExcel_IOFactory */
require_once dirname(__FILE__) . '/classes/PHPExcel/IOFactory.php';

require_once dirname(__FILE__) . '/inc/functions.inc.php';

if (!$_POST || !isset($_POST['typefile']) || !isset($_POST['saison']) || !$_FILES || !isset($_FILES['file'])) {
  exit("ERREUR: Please call page with good parameters" . EOL);
}


$type = array();
if (isset($_POST['type'])){
  $type = $_POST['type'];
}

$fType = $_POST['typefile'];
switch($fType){
  case 'reg':
    break;
  case 'dep':
    break;
  default:
    exit("ERREUR: Please call page with good parameters" . EOL);
}

$tmp_file = $_FILES['file']['tmp_name'];


$objPHPExcel = PHPExcel_IOFactory::load($tmp_file);

switch($fType){
  case 'reg':
    $cal = new regionalCal($objPHPExcel,$type);
    $cal->parserCalendrier();
    break;
  case 'dep':
    $cal = new departementalCal($objPHPExcel,$type);
    $cal->parserCalendrier();
    break;
}

header('Content-type: text/calendar; charset=utf-8');
header('Content-Disposition: attachment; filename=tournois.ics');

echo $cal->getVCalendar();
