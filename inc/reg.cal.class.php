<?php

class regionalCal extends parseCalendrier {

  public function parserCalendrier(){
    $objPHPExcel = $this->objPHPExcel;
    $retEvents = $this->getFormationsEvents($objPHPExcel);
    $retEvents = array_merge($retEvents,$this->getNationalEvents($objPHPExcel));
    $retEvents = array_merge($retEvents,$this->getInterclubsEvents($objPHPExcel));
    $retEvents = array_merge($retEvents,$this->getJeunesEvents($objPHPExcel));
    $retEvents = array_merge($retEvents,$this->getSeniorEvents($objPHPExcel));

    $this->arrEvents = $retEvents;
  }

  protected function getDivisions($cell){
    $objPHPExcel = $this->objPHPExcel;
    $row = $cell->getRow();

    $col = PHPExcel_Cell::columnIndexFromString($cell->getColumn())-1;
    $range = $cell->getMergeRange();

    $ret=array();
    do{
      $ret[] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($col,IC_ENTETE)->getCalculatedValue();
      $col++;
      $nextCell = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($col,$row);
    }
    while($nextCell->isInRange($range));

    return $ret;

  }


  protected function getSeniorEvents(){
    $objPHPExcel = $this->objPHPExcel;
    $NB_FEUILLES=$objPHPExcel->getSheetCount();
    $retEvents = array();
    for($j=0;$j<$NB_FEUILLES;$j++){
      $objPHPExcel->setActiveSheetIndex($j);
      $sheet = $objPHPExcel->getActiveSheet();
      $DERNIER_LIGNE=$this->getLastRow($objPHPExcel);

      for ($col = SEN_COL; $col<=SEN_COL_FIN;$col+=2){
        //if($col % 2 == 1) continue;
        for ($i = PREMIERE_LIGNE; $i<=$DERNIER_LIGNE; $i++){
          $cell = $sheet->getCellByColumnAndRow($col, $i);
          $val = $cell->getCalculatedValue();

          if ($val!=''){
            $event = array();

            if ($cell->isMergeRangeValueCell()){
              $event['categorie'] = 'national';
              $event ['type'] = 'tnational';
              $event['nom'] = $val;
              $nbJours = $this->getNumberOfDays($cell,$objPHPExcel);
              $event['date_deb'] = $sheet->getCellByColumnAndRow(DATE_COL, $i)->getCalculatedValue();
              if ($nbJours > 1){
                $event['date_fin'] = $sheet->getCellByColumnAndRow(DATE_COL, ($i-1+$nbJours))->getCalculatedValue();
              }
              $retEvents[] = new eventCal($event);
              continue;
            }
            $event['categorie'] = 'regional';
            $event['type'] = 'tregional';
            $event['tableaux'] = $val;
            $event['organisateur'] = $sheet->getCellByColumnAndRow($col+1,$i)->getCalculatedValue();
            if (strcmp($event['organisateur'],"?")==0) continue;
            $event['nom'] = $event['organisateur'].' - '.$event['tableaux'];
            $event['date_deb'] = $sheet->getCellByColumnAndRow(DATE_COL, $i)->getCalculatedValue();
            $retEvents[] = new eventCal($event);
          }
        }
      }
    }
    return $retEvents;
  }



  protected function getInterclubsEvents(){
    $objPHPExcel=$this->objPHPExcel;
    $NB_FEUILLES=$objPHPExcel->getSheetCount();
    $retEvents = array();
    for($j=0;$j<$NB_FEUILLES;$j++){
      $objPHPExcel->setActiveSheetIndex($j);
      $sheet = $objPHPExcel->getActiveSheet();
      $DERNIER_LIGNE=$this->getLastRow($objPHPExcel);

      for ($col = IC_COL; $col<=IC_COL_FIN;$col++){
        for ($i = PREMIERE_LIGNE; $i<=$DERNIER_LIGNE; $i++){
          $cell = $sheet->getCellByColumnAndRow($col, $i);
          $val = $cell->getCalculatedValue();

          if ($val!=''){
            $event = array('type'=>'interclubs');
            $event['journee'] = $val;

            if ($cell->isMergeRangeValueCell()){
              $nbJours = $this->getNumberOfDays($cell,$objPHPExcel);
              $cell = $sheet->getCellByColumnAndRow($col, $i);
              $divisions = $this->getDivisions($cell,$objPHPExcel);
              $event['divisions'] = $sheet->getCellByColumnAndRow($col, IC_ENTETE)->getCalculatedValue();
              $event['nom'] = $event['journee'].' - '.$event['divisions'];
              $event['date_deb'] = $sheet->getCellByColumnAndRow(DATE_COL, $i)->getCalculatedValue();
              if ($nbJours > 1){
                $event['date_fin'] = $sheet->getCellByColumnAndRow(DATE_COL, ($i-1+$nbJours))->getCalculatedValue();
              }
              $retEvents[] = new eventCal($event);
              continue;
            }
            $event['divisions'] = $sheet->getCellByColumnAndRow($col, IC_ENTETE)->getCalculatedValue();
            $event['nom'] = $event['journee'].' - '.$event['divisions'];
            $event['date_deb'] = $sheet->getCellByColumnAndRow(DATE_COL, $i)->getCalculatedValue();
            $retEvents[] = new eventCal($event);
            //var_dump($col);
          }
        }
      }
    }
    return $retEvents;
  }


  protected function getFormationsEvents(){
    $objPHPExcel=$this->objPHPExcel;
    $NB_FEUILLES=$objPHPExcel->getSheetCount();
    $retEvents = array();
    for($j=0;$j<$NB_FEUILLES;$j++){
      $objPHPExcel->setActiveSheetIndex($j);
      $sheet = $objPHPExcel->getActiveSheet();
      $col = FORMATION_COL;
      $DERNIER_LIGNE=$this->getLastRow($objPHPExcel);

      for ($i = PREMIERE_LIGNE; $i<=$DERNIER_LIGNE; $i++){
        $cell = $sheet->getCellByColumnAndRow($col, $i);
        $val = $cell->getCalculatedValue();

        if ($val!=''){
          $event = array('type'=>'formation');
          $event['nom'] = $val;

          if ($cell->isMergeRangeValueCell()){
            $nbJours = $this->getNumberOfDays($cell,$objPHPExcel);
            $event['date_deb'] = $sheet->getCellByColumnAndRow(DATE_COL, $i)->getCalculatedValue();
            if ($nbJours > 1){
              $event['date_fin'] = $sheet->getCellByColumnAndRow(DATE_COL, ($i-1+$nbJours))->getCalculatedValue();
            }
            $retEvents[] = new eventCal($event);
            continue;
          }
          $event['date_deb'] = $sheet->getCellByColumnAndRow(DATE_COL, $i)->getCalculatedValue();
          $retEvents[] = new eventCal($event);
        }
      }
    }
    return $retEvents;
  }


  protected function getNationalEvents(){
    $objPHPExcel=$this->objPHPExcel;
    $NB_FEUILLES=$objPHPExcel->getSheetCount();
    $retEvents = array();
    for($j=0;$j<$NB_FEUILLES;$j++){
      $objPHPExcel->setActiveSheetIndex($j);
      $sheet = $objPHPExcel->getActiveSheet();
      $col = NAT_COL;
      $DERNIER_LIGNE=$this->getLastRow($objPHPExcel);

      for ($i = PREMIERE_LIGNE; $i<=$DERNIER_LIGNE; $i++){
        $cell = $sheet->getCellByColumnAndRow($col, $i);
        $val = $cell->getCalculatedValue();

        if ($val!=''){
          $event = array('type'=>'national');
          $event['nom'] = $val;

          if ($cell->isMergeRangeValueCell()){
            $nbJours = $this->getNumberOfDays($cell,$objPHPExcel);
            $event['date_deb'] = $sheet->getCellByColumnAndRow(DATE_COL, $i)->getCalculatedValue();
            if ($nbJours > 1){
              $event['date_fin'] = $sheet->getCellByColumnAndRow(DATE_COL, ($i-1+$nbJours))->getCalculatedValue();
            }
            $retEvents[] = new eventCal($event);
            continue;
          }
          $event['date_deb'] = $sheet->getCellByColumnAndRow(DATE_COL, $i)->getCalculatedValue();
          $retEvents[] = new eventCal($event);
        }
      }
    }
    return $retEvents;
  }

  protected function getJeunesEvents(){
    $objPHPExcel = $this->objPHPExcel;
    $NB_FEUILLES=$objPHPExcel->getSheetCount();
    $retEvents = array();
    for($j=0;$j<$NB_FEUILLES;$j++){
      $objPHPExcel->setActiveSheetIndex($j);
      $sheet = $objPHPExcel->getActiveSheet();
      $col = JEUNE_COL;
      $DERNIER_LIGNE=$this->getLastRow($objPHPExcel);

      for ($i = PREMIERE_LIGNE; $i<=$DERNIER_LIGNE; $i++){
        $cell = $sheet->getCellByColumnAndRow($col, $i);
        $val = $cell->getCalculatedValue();

        if ($val!=''){
          $event = array('type'=>'jeunes');
          $event['nom'] = $val;

          if ($cell->isMergeRangeValueCell()){
            $nbJours = $this->getNumberOfDays($cell,$objPHPExcel);
            $event['date_deb'] = $sheet->getCellByColumnAndRow(DATE_COL, $i)->getCalculatedValue();
            if ($nbJours > 1){
              $event['date_fin'] = $sheet->getCellByColumnAndRow(DATE_COL, ($i-1+$nbJours))->getCalculatedValue();
            }
            $retEvents[] = new eventCal($event);
            continue;
          }
          $event['date_deb'] = $sheet->getCellByColumnAndRow(DATE_COL, $i)->getCalculatedValue();
          $retEvents[] = new eventCal($event);
        }
      }
    }
    return $retEvents;
  }

}
