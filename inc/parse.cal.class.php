<?php

class parseCalendrier {
  protected $objPHPExcel;
  protected $typeEvent = array();
  protected $arrEvents = array();

  public function __construct($objPHPExcel, $arrTypeEvent = array()) {
    if (!isset($objPHPExcel)) die('Error: Object PHPExcel is not set.');
    $this->objPHPExcel = $objPHPExcel;
    $this->typeEvent = $arrTypeEvent;
  }

  protected function parserCalendrier(){
    die('Error: Function parserCalendrier must be override');
  }

  protected function getLastRow(){
    $objPHPExcel = $this->objPHPExcel;
    $i=1;
    while ( $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0,$i)->getValue() != ''
            || $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0,$i)->isInMergeRange()){
      $i++;
    }
    return ($i-1);
  }

  protected function getRangeValue($cell){
    if (!$cell->isInMergeRange()) return $cell->getCalculatedValue();

    $sheet = $this->objPHPExcel->getActiveSheet();
    $range = $cell->getMergeRange();


    $firstCell = explode(':',$range);
    $firstCell = $sheet->getCell($firstCell[0]);


    return $firstCell->getCalculatedValue();
  }

  protected function getNumberOfDays($cellEvent){
    $objPHPExcel = $this->objPHPExcel;
    $row = $cellEvent->getRow();
    $col = PHPExcel_Cell::columnIndexFromString($cellEvent->getColumn())-1;
    $range = $cellEvent->getMergeRange();
    $ret=0;

    do{
      $ret++;
      $nextCell = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($col,$row+$ret);
    }
    while($nextCell->isInRange($range));

    return $ret;
  }

  public function getVCalendar(){

    if (count($this->arrEvents)<1) die("ERREUR: Aucun évènement retenu".EOL);

    $output =  null;

    $output .= 'BEGIN:VCALENDAR'.EOL;
    $output .= 'PRODID:-//BCT59//PAUL C//FR'.EOL;
    $output .= 'VERSION:2.0'.EOL;
    $output .= 'CALSCALE:GREGORIAN'.EOL;
    $output .= 'METHOD:PUBLISH'.EOL;
    $output .= 'X-WR-CALNAME:Tournois'.EOL;
    $output .= 'X-WR-TIMEZONE:Europe/Paris'.EOL;
    $output .= 'X-WR-CALDESC:Liste de tournoi dans la region Haut de France pour la saison '.STR_SAISON.EOL;


    foreach($this->arrEvents as $event){

      if(!in_array($event->getType(),$this->typeEvent)) continue;

      $output .= $event->getICSEvent();
    }
    $output .= 'END:VCALENDAR'.EOL;
    return $output;
  }
}
