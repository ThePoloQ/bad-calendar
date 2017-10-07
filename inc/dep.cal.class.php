<?php

class departementalCal extends parseCalendrier {
  
  
  public function parserCalendrier(){
    $objPHPExcel = $this->objPHPExcel;
    $retEvents = $this->getEvents($objPHPExcel);    
    $this->arrEvents = $retEvents;
  }
  
  protected function getMois($ligne){
    $sheet = $this->objPHPExcel->getActiveSheet();
    $cell = $sheet->getCellByColumnAndRow(0,$ligne);
    
    $arr = explode('-',$this->getRangeValue($cell));
    return $arr[0];
  }
  
  protected function getEvents(){
    $objPHPExcel = $this->objPHPExcel;
    $NB_FEUILLES=$objPHPExcel->getSheetCount();
    $retEvents = array();  
    for($j=0;$j<$NB_FEUILLES;$j++){
      $objPHPExcel->setActiveSheetIndex($j);
      $sheet = $objPHPExcel->getActiveSheet();
      $DERNIER_LIGNE=$this->getLastRow($objPHPExcel);
      
      for ($col = DEP_PREMIERE_COL; $col<=DEP_DERNIERE_COL;$col++){
        for ($i = DEP_PREMIERE_LIGNE; $i<=$DERNIER_LIGNE; $i++){
          $cell = $sheet->getCellByColumnAndRow($col, $i);
          $val = $cell->getCalculatedValue();
          
          if (!$cell->isInMergeRange() && $val =='') continue;
          
          $event = array();
          
          if (preg_match('/INTERCLUBS/',$val)){
            $event['type'] = 'interclubs';
            if (preg_match('/SENIORS/',$val)){
              $event['nom'] = substr($val, -2)." - IC DEP SENIORS";
            }else if (preg_match('/VETERANS/',$val)){
              $event['nom'] = substr($val, -2)." - IC DEP VETERANTS";
            }else{
              continue;
            }
          }
          
          
          if ($cell->isInMergeRange()){
            $val = $this->getRangeValue($cell);

            if (preg_match("/CHAMPIONNAT DEPARTEMENTAL/",$val)) {
              continue;
            }
            
            if (!isset($event['type'])){
              $event['type'] = strtolower($val);
            }
          }
          
          $jour = substr($this->getRangeValue($sheet->getCellByColumnAndRow(1,$i)),-2);
          $mois = $this->getMois($i);
          
          
          if (!isset($event['nom'])){
            $event['type'] = strtolower($val);
            $event['organisateur'] = $this->getRangeValue($sheet->getCellByColumnAndRow($col+3,$i));
            $event['nom'] = $val.' - '.$this->getRangeValue($sheet->getCellByColumnAndRow($col+3,$i)).' - '.preg_replace("/ /","",$this->getRangeValue($sheet->getCellByColumnAndRow($col+2,$i))).' - '.preg_replace("/ /","",$this->getRangeValue($sheet->getCellByColumnAndRow($col+1,$i)));
          }
          $event['date_deb'] = $jour.'-'.$mois;
          
          $retEvents[] = new eventCal($event);
        }
      }
    }
    return $retEvents;
  }
 
}