<?php

class eventCal {
  protected $nom = "";
  protected $dateDeb = "";
  protected $dateFin = "";
  protected $organisateur = "";
  protected $type = "";
  
  public function __construct($arrInit){
    if (isset($arrInit)){
      if (isset($arrInit['nom'])) $this->nom = $arrInit['nom'];
      if (isset($arrInit['date_deb'])) $this->dateDeb = $arrInit['date_deb'];
      if (isset($arrInit['date_fin'])) $this->dateFin = $arrInit['date_fin'];
      if (isset($arrInit['organisateur'])) $this->organisateur = $arrInit['organisateur'];
      if (isset($arrInit['type'])) $this->type = $arrInit['type'];
    }
  }
  
	public function getNom(){
		return $this->nom;
	}

	public function setNom($nom){
		$this->nom = $nom;
	}

	public function getDateDeb(){
		return $this->dateDeb;
	}

	public function setDateDeb($dateDeb){
		$this->dateDeb = $dateDeb;
	}

	public function getDateFin(){
		return $this->dateFin;
	}

	public function setDateFin($dateFin){
		$this->dateFin = $dateFin;
	}

	public function getOrganisateur(){
		return $this->organisateur;
	}

	public function setOrganisateur($organisateur){
		$this->organisateur = $organisateur;
	}

	public function getType(){
		return $this->type;
	}

	public function setType($type){
		$this->type = $type;
	}

  protected function getArrDate($date){
    $date = explode("-",$date);
    if (count($date)!=2) exit("ERREUR: Mauvais format de date : ".$date.EOL);
    $date[1] = $GLOBALS['mois'][preg_replace("/\./","",$date[1])];
    if ($date[1]>8)
      $date[2] = (intval($GLOBALS['saison'])-1);
    else
      $date[2] = intval($GLOBALS['saison']);
    
    return $date;
  }
  
  public function getICSEvent(){
    $ret = "";
    
    $ret.= 'BEGIN:VEVENT'.EOL;
    $ret.= 'CLASS:PUBLIC'.EOL;
    $summary = preg_replace( "/\r|\n/", "", $this->getNom());
    $summary = preg_replace('/\s\s+/', ' - ', $summary);
    $ret.= 'SUMMARY:'.$summary.EOL;
    
    $dateD = $this->getArrDate($this->getDateDeb());
    $dateD = sprintf("%04d%02d%02d",intval($dateD[2]),intval($dateD[1]),intval($dateD[0]));
    $ret.= 'DTSTART;VALUE=DATE:'.$dateD.EOL;
    
    
    if ($this->getDateFin()){
      $dateF = $this->getArrDate($this->getDateFin());
      $ret.= 'DTEND;VALUE=DATE:'.sprintf("%04d%02d%02d",intval($dateF[2]),intval($dateF[1]),(intval($dateF[0])+1)).EOL;
    }
    
    $uid = preg_replace( "/[ \/()']/", "", $summary);
    
    $ret.= 'UID:'.$uid.'-'.$dateD.'@bcl59'.EOL;
    
    $ret.= 'LOCATION:'.$this->getOrganisateur().EOL;
    
    $ret.= 'DESCRIPTION:'.EOL;
    $ret.= 'END:VEVENT'.EOL;
    return $ret;
  }  
}