<?php
    
$EM_CONF[$_EXTKEY] = array (
  'title' => 'libconnect',
  'description' => 'Diese Extension ist von Avonis im Auftrag der Staats- und Universitaetsbibliothek Hamburg entwickelt worden. Mit ihr lassen sich Ergebnisse aus den Informationssystemen EZB und DBIS der Universitaet Regensburg direkt in das TYPO3-System einbinden.',
  'category' => 'plugin',
  'author' => 'Avonis New Media / SUB Hamburg',
  'author_email' => 'torsten.witt@sub.uni-hamburg.de',
  'author_company' => '',
  'module' => '',
  'state' => 'stable',
  'internal' => '',
  'uploadfolder' => 0,
  'createDirs' => '',
  'clearCacheOnLoad' => 0,
  'version' => '7.0.7',
  'constraints' => 
  array (
    'depends' => 
    array (
      'typo3' => '8.7.0-9.5.99',
      'extbase' => '',
      'fluid' => '',
    ),
    'conflicts' => 
    array (
    ),
    'suggests' => 
    array (
    ),
  ),
  'autoload' => 
  array (
    'psr-4' => 
    array (
      'Sub\\Libconnect\\' => 'Classes',
    ),
  ),
  '_md5_values_when_last_written' => 'a:0:{}',
  'comment' => 'DBIS: fixed Unknown Namespace in top datases; DBIS+EZB: changed servernames rcblx1 and rcblx10 to ezb and dbis.',
  'user' => 'subunihhdevteam',
);

?>
