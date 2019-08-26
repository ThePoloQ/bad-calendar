<?php

date_default_timezone_set('UTC');
setlocale(LC_TIME, 'fr_FR.utf8');

define('DATE_COL',0);
define('FORMATION_COL',1);
define('NAT_COL',2);
define('IC_COL',4);

define('IC_ENTETE',3);
define('PREMIERE_LIGNE',4);

$GLOBALS['mois']=array(
  'janv' => 1,
  'févr' => 2,
  'fév' => 2,
  'mars' => 3,
  'avr'  => 4,
  'mai'  => 5,
  'juin' => 6,
  'juil' => 7,
  'sept' => 9,
  'oct'  => 10,
  'nov'  => 11,
  'déc'  => 12,
);
