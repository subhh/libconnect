<?php
namespace Sub\Libconnect\Domain\Repository;
/***************************************************************
* Copyright notice
*
* (c) 2009 by Avonis - New Media Agency
*
* All rights reserved
*
* This script is part of the EZB/DBIS-Extention project. The EZB/DBIS-Extention project
* is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
*
* The GNU General Public License can be found at
* http://www.gnu.org/copyleft/gpl.html.
*
* This script is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
*
* This copyright notice MUST APPEAR in all copies of the script!
*
* Project sponsored by:
*  Avonis - New Media Agency - http://www.avonis.com/
***************************************************************/

/**
 *
 *
 * @package libconnect
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 *
 */
require_once(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('libconnect') . 'Resources/Private/Lib/Ezb.php');
require_once(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('libconnect') . 'Resources/Private/Lib/Zdb.php');

Class EzbRepository extends \TYPO3\CMS\Extbase\Persistence\Repository {
    private $ezb_to_t3_subjects = array();
    private $t3_to_ezb_subjects = array();

    private $longAccessInfos = array();

    /**
     * subjectRepository
     *
     * @var \Sub\Libconnect\Domain\Repository\SubjectRepository
     * @inject
     */
    protected $subjectRepository;
    
    /**
     * get list for start page
     * 
     * @return array $list
     */
    public function loadOverview() {
        $this->loadSubjects();
        
        $ezb =  \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('Tx_libconnect_Resources_Private_Lib_Ezb');

        $subjectsOnline = $ezb->getFachbereiche();

        foreach($subjectsOnline as $el) {
            $subject = $this->ezb_to_t3_subjects[$el['id']];

            if(!empty($subject['uid'])){
                $el['link'] = $GLOBALS['TSFE']->cObj->getTypolink_URL($GLOBALS['TSFE']->id, array( 'libconnect[subject]' => $subject['uid']));
                $list[$el['id']] = $el;
            }
        }

        return $list;
    }

    /**
     * fill variable $ezb_to_t3_subjects with list of subjects
     */
    private function loadSubjects() {
        //$objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
        //$repository = $objectManager->get('Sub\\Libconnect\\Domain\\Repository\\SubjectRepository');

        $res =  $this->subjectRepository->findAll();

        foreach($res as $row){
            $this->ezb_to_t3_subjects[$row->getEzbnotation()]['ezbnotation'] = $row->getEzbnotation();
            $this->ezb_to_t3_subjects[$row->getEzbnotation()]['title'] = $row->getTitle();
            $this->ezb_to_t3_subjects[$row->getEzbnotation()]['uid'] = $row->getUid();
            
            $this->t3_to_ezb_subjects[$row->getUid()]['uid'] = $row->getUid();
            $this->t3_to_ezb_subjects[$row->getUid()]['ezbnotation'] = $row->getEzbnotation();
            $this->t3_to_ezb_subjects[$row->getUid()]['title'] = $row->getTitle();
        }

    }
    
    /**
     * get list of a subject or letter
     * 
     * @param int $subject_id
     * @param array $options
     * @param array $config
     *
     * @return array
     */
    public function loadList($subject_id, $options = array('index' =>0, 'sc' => 'A', 'lc' => ''), $config) {
        $index = $options['index'];
        $sc = $options['sc'];
        $lc = $options['lc'];

        $this->loadSubjects();

        //get notation for subject
        $subject = $this->t3_to_ezb_subjects[$subject_id];

        $ezb =  \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('Tx_libconnect_Resources_Private_Lib_Ezb');

        if($options['notation'] == 'All'){
            $subject['ezbnotation'] = 'All';
        }
        
        //filter list by access list
        if(!empty($options['colors'])){
            $colors = $this->getColors($options['colors']);
            $ezb->setColors($colors);
            
            $colorList = $options['colors'];
        }else{
            $colorList = array(
                1 => 1,
                2 => 2,
                4 => 4,
                6 => 6
            );
        }

        $journals = $ezb->getFachbereichJournals($subject['ezbnotation'], $index, $sc, $lc);
        
        //get access information
        $journals['selected_colors'] = $this->getAccessInfos();
        $journals['colors'] = $colorList;

        /**
         * create links
         */
        //navigation - letters
        foreach(array_keys($journals['navlist']['pages']) as $page) {
            if (is_array($journals['navlist']['pages'][$page])) {
                $journals['navlist']['pages'][$page]['link'] = $GLOBALS['TSFE']->cObj->getTypolink_URL($GLOBALS['TSFE']->id, array(
                    'libconnect[subject]' => $subject['uid'],
                    'libconnect[index]' => 0,
                    'libconnect[sc]' => $journals['navlist']['pages'][$page]['sc']? $journals['navlist']['pages'][$page]['sc'] : 'A',
                    'libconnect[lc]' => $journals['navlist']['pages'][$page]['lc'],
                    'libconnect[notation]' => $subject['ezbnotation'],
                    'libconnect[colors]' => $journals['colors']
                ));
            }
        }
        
        //navigation - sections in letters
        if(isset($journals['alphabetical_order']['first_fifty'])){
            foreach(array_keys($journals['alphabetical_order']['first_fifty']) as $section) {
                $journals['alphabetical_order']['first_fifty'][$section]['link'] = $GLOBALS['TSFE']->cObj->getTypolink_URL($GLOBALS['TSFE']->id, array(
                        'libconnect[subject]' => $subject['uid'],
                        'libconnect[index]' => $journals['alphabetical_order']['first_fifty'][$section]['sindex'],
                        'libconnect[sc]' => $journals['alphabetical_order']['first_fifty'][$section]['sc']? $journals['alphabetical_order']['first_fifty'][$section]['sc'] : 'A',
                        'libconnect[lc]' => $journals['alphabetical_order']['first_fifty'][$section]['lc'],
                        'libconnect[notation]' => $subject['ezbnotation'],
                        'libconnect[colors]' => $journals['colors']
                ));
            }
        }
        if(isset($journals['alphabetical_order']['journals'])){
            foreach(array_keys($journals['alphabetical_order']['journals']) as $journal) {
                $journals['alphabetical_order']['journals'][$journal]['detail_link'] = $GLOBALS['TSFE']->cObj->getTypolink_URL(
                        intval($config['detailPid']),
                        array(
                            'libconnect[jourid]' => $journals['alphabetical_order']['journals'][$journal]['jourid']
                        )
                );
            }
        }
        //navigation - sections in letters
        if(isset($journals['alphabetical_order']['next_fifty'])){
            foreach(array_keys($journals['alphabetical_order']['next_fifty']) as $section) {
                $journals['alphabetical_order']['next_fifty'][$section]['link'] = $GLOBALS['TSFE']->cObj->getTypolink_URL($GLOBALS['TSFE']->id, array(
                        'libconnect[subject]' => $subject['uid'],
                        'libconnect[index]' => $journals['alphabetical_order']['next_fifty'][$section]['sindex'],
                        'libconnect[sc]' => $journals['alphabetical_order']['next_fifty'][$section]['sc']? $journals['alphabetical_order']['next_fifty'][$section]['sc'] : 'A',
                        'libconnect[lc]' => $journals['alphabetical_order']['next_fifty'][$section]['lc'],
                        'libconnect[notation]' => $subject['ezbnotation'],
                        'libconnect[colors]' => $journals['colors']
                ));
            }
        }

        return $journals;
    }

    /**
     * get detail information of a journal
     * 
     * @param type $journalId
     * @param type $config
     *
     * @return boolean
     */
    public function loadDetail($journalId, $config) {
        $ezb =  \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('Tx_libconnect_Resources_Private_Lib_Ezb');

        $journal = $ezb->getJournalDetail($journalId);

        if (! $journal ){
            return FALSE;
        }

        /*BEGIN get access information*/
        
        //get default texts
        $LongAccessInfos = $ezb->getLongAccessInfos();
        
        $colortext = array();
        if((!empty($LongAccessInfos['longAccessInfos'])) && ($LongAccessInfos['longAccessInfos']!= FALSE)){
            foreach($LongAccessInfos as $key =>$text){
                 $colortext[$key] = $text;
            }
        }
        
        //get texts from the web
        $form = $ezb->detailSearchFormFields();
        $journal['selected_colors'] = $form['selected_colors'];

        $color = $journal['color_code'];//Farbangabe
        unset($journal['color_code']);
        $journal['color_code'] = array();

        if((!isset($journal['selected_colors'][$color])) or (empty($journal['selected_colors'][$color])) or ($LongAccessInfos['force'] == 'true')){
            $journal['color_code']['text'] = $colortext['longAccessInfos'][$color];
        }else{
            $journal['color_code']['text'] = $journal['selected_colors'][$color];
        }
        $journal['color_code']['color'] = $color;
        /*END get access information*/

        //generate link to institutions having access to this journal
        if($journal['participants'] == TRUE){
            if($config['participantsPid'] and $config['participantsPid'] != 0){
                $journal['participants'] = $GLOBALS['TSFE']->cObj->getTypolink_URL(
                    intval($config['participantsPid']),
                    array(
                        'libconnect[jourid]' => $journalId
                    )
                );
            }
        }
        
        //setSubjectLinks but only it is configured
        if(!empty($config['listPid'])){
            $this->loadSubjects();
            foreach($this->t3_to_ezb_subjects as $subject){
                if($subject['title'] == $journal['subjects_join']){
                    $journal['subjects_join_link'][] = array(
                        'link' => $GLOBALS['TSFE']->cObj->getTypolink_URL(
                            intval($config['listPid']), 
                            array(
                                'libconnect[subject]' => $subject['uid']
                            )
                        ), 
                        'title' => $subject['title']
                    );
                }
            }
        }

        //http://rzblx1.uni-regensburg.de/ezeit/searchres.phtml?bibid=SUBHH&colors=7&lang=de&jq_type1=KW&jq_term1=Radiologie
        //creates links of keywords 
        $tempKeywords = array();
        foreach( $journal['keywords'] as $keyword ){
            $temp[] = $GLOBALS['TSFE']->cObj->getTypoLink(
                            $keyword, 
                            10, 
                            array(
                                'libconnect[search][jq_term1]' =>  $keyword,
                                'libconnect[search][jq_type1]' => 'KW'
                            ));
        }
        $journal['keywords_join'] = join(', ', $tempKeywords);
        
        return $journal;
    }
    
    /**
     * search
     * 
     * @param array $searchVars
     * @param array $config
     *
     * @return array $journals
     */
    public function loadSearch($searchVars, $colors, $config) {
        $this->loadSubjects();

        //search of sidebar
        if (strlen($searchVars['sword'])) {
            $searchVars['jq_type1'] = 'QS';
            $searchVars['jq_term1'] = $searchVars['sword'];
        }
        unset($searchVars['sword']);//in weiterer Verarbeitung nicht sinnvoll

        $linkParams = array();
        foreach ($searchVars as $key => $value) {
            $linkParams["libconnect[search][$key]"] = $value;
        }

        $ezb =  \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('Tx_libconnect_Resources_Private_Lib_Ezb');

        //filter list by access list
        $ezbColors = $this->getColors($colors);
        $ezb->setColors($ezbColors);

        $journals = $ezb->search($term, $searchVars);

        if (! $journals){
            return FALSE;
        }

        $journals['searchDescription'] = $this->getSearchDescription($searchVars);
        $journals['selected_colors'] = $colors;
    
        /**
         * create links
         */
        //navigation
        if (is_array($journals['navlist']['pages'])) {

            foreach(array_keys($journals['navlist']['pages']) as $page) {
                if (is_array($journals['navlist']['pages'][$page])) {
                    $journals['navlist']['pages'][$page]['link'] = $GLOBALS['TSFE']->cObj->getTypolink_URL($GLOBALS['TSFE']->id,
                        array_merge($linkParams, array(
                            'libconnect[search][sc]' => $journals['navlist']['pages'][$page]['id'],
                            'libconnect[search][selected_colors]' => $journals['selected_colors']
                        )));
                }
            }
        }
        
        //precise hits
        if (is_array($journals['precise_hits'])) {
            
            foreach(array_keys($journals['precise_hits']) as $precise_hit) {
                if (is_array($journals['precise_hits'][$precise_hit])) {
                    $journals['precise_hits'][$precise_hit]['detail_link'] = $GLOBALS['TSFE']->cObj->getTypolink_URL(
                        intval($config['detailPid']),
                        array(
                            'libconnect[jourid]' => $journals['precise_hits'][$precise_hit]['jourid'],
                        )
                    );
                }
            }
        }
        
        //results
        if (is_array($journals['alphabetical_order']['first_fifty'])) {

            foreach(array_keys($journals['alphabetical_order']['first_fifty']) as $section) {
                $journals['alphabetical_order']['first_fifty'][$section]['link'] = $GLOBALS['TSFE']->cObj->getTypolink_URL($GLOBALS['TSFE']->id,
                    array_merge($linkParams, array(
                        'libconnect[search][sindex]' => $journals['alphabetical_order']['first_fifty'][$section]['sindex'],
                        'libconnect[search][sc]' => $journals['alphabetical_order']['first_fifty'][$section]['sc'],
                        'libconnect[search][selected_colors]' => $journals['selected_colors']
                    )));
            }
        }

        if (is_array($journals['alphabetical_order']['journals'])) {

            foreach(array_keys($journals['alphabetical_order']['journals']) as $journal) {
                $journals['alphabetical_order']['journals'][$journal]['detail_link'] = $GLOBALS['TSFE']->cObj->getTypolink_URL(
                    intval($config['detailPid']),
                    array(
                        'libconnect[jourid]' => $journals['alphabetical_order']['journals'][$journal]['jourid'],
                    )
                );
            }
        }

        if (is_array($journals['alphabetical_order']['next_fifty'])) {

            foreach(array_keys($journals['alphabetical_order']['next_fifty']) as $section) {
                $journals['alphabetical_order']['next_fifty'][$section]['link'] = $GLOBALS['TSFE']->cObj->getTypolink_URL($GLOBALS['TSFE']->id,
                    array_merge($linkParams, array(
                        'libconnect[search][sindex]' => $journals['alphabetical_order']['next_fifty'][$section]['sindex'],
                        'libconnect[search][sc]' => $journals['alphabetical_order']['next_fifty'][$section]['sc'],
                        'libconnect[search][selected_colors]' => $journals['selected_colors']
                    )));
            }
        }
        
        //get access information
        $journals['AccessInfos'] = $this->getAccessInfos();

        return $journals;
    }
    
    /**
     * create search form
     * 
     * @return array
     */
    public function loadForm() {
        $ezb = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('Tx_libconnect_Resources_Private_Lib_Ezb');
        $form = $ezb->detailSearchFormFields();

        //Zugriffsinformationen holen
        $form['selected_colors'] = $this->getAccessInfos(true);

        return $form;
    }
    
    /**
     * get BibID
     * 
     * @return string
     */
    public function getBibid(){
        return $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_libconnect.']['ezbbibid'];
    }
    
//BOF ZDB LocationData
    /**
     * get information about the location for the print version
     * 
     * @param array $journal
     */
    public function loadLocationData($journal) {
        $zdb = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('Tx_Libconnect_Resources_Private_Lib_Zdb');

        if(!empty($journal['ZDB_number'])){
            $locationData = $zdb->getJournalLocationDetails( NULL, $journal['ZDB_number']);
        } else {
            if(count($journal['pissns'])){
                $locationData = $zdb->getJournalLocationDetails( "issn=" . reset($journal['pissns']), NULL );
            } elseif(count($journal['eissns'])){
                $locationData = $zdb->getJournalLocationDetails( "eissn=" . reset($journal['eissns']), NULL );
            }
        }

        if (! $locationData ){
            return FALSE;
        }
    
        return $locationData; 
    }
//EOF ZDB LocationData

    /**
     * set detailed access informations
     * 
     * @param array $longAccessInfos
     */
    public function setLongAccessInfos($longAccessInfos) {
        $this->longAccessInfos = $longAccessInfos;
    }
    
    /**
     * get detailed access informations
     * 
     * @return array
     */
    public function getLongAccessInfos(){
        return $this->longAccessInfos;
    }
    
    public function getAccessInfos($short = false){
        $ezb = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('Tx_libconnect_Resources_Private_Lib_Ezb');

        //get default texts
        $LongAccessInfos = $ezb->getLongAccessInfos();

        $colortext = array();
        if((!empty($LongAccessInfos['longAccessInfos'])) && ($LongAccessInfos['longAccessInfos']!= FALSE)){
            foreach($LongAccessInfos as $key =>$text){
                 $colortext[$key] = $text;
            }
        }

        //get text from web
        $form = $ezb->detailSearchFormFields();
        $AccessInfos = array();

        //own texts or from web
        if((!isset($form['selected_colors'])) or (empty($form['selected_colors'])) or ($LongAccessInfos['force'] == 'true')){
            $AccessInfos = $colortext['longAccessInfos'];
        }else{
            $AccessInfos = $form['selected_colors'];

            if($short){
                //if shorter form is will
                $ShortAccessInfos = $ezb->getShortAccessInfos();

                if((!empty($ShortAccessInfos)) && ($ShortAccessInfos!= FALSE)){
                    foreach($ShortAccessInfos['shortAccessInfos'] as $key => $text){
                        if(empty($AccessInfos[$key])){
                            $AccessInfos[$key] = $ShortAccessInfos['shortAccessInfos'][$key];
                        }
                    }
                }
            }else{
                //if licence informations are missing
                foreach($colortext['longAccessInfos'] as $key => $text){
                    if(empty($AccessInfos[$key])){
                        $AccessInfos[$key] = $colortext['longAccessInfos'][$key];
                    }
                }
            }
        }
        
        //reorginize array
        foreach($AccessInfos as $colorkey => $value){
            if ( $colorkey != 6 ){
                $key = $colorkey;
            } else {
                $key = 3;
            }
            $return[$key] = array(
                            'colorkey' => $colorkey,
                            'value' => $value
                        );
        }
        
        ksort($return);

        return $return;
    }
    
    /**
     * get data about the search
     * 
     * @param array $searchVars
     *
     * @return array
     */
    private function getSearchDescription($searchVars){
        $list = array();
        $ezb = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('Tx_libconnect_Resources_Private_Lib_Ezb');
        
        //search terms and theire categories
        $jq = "";
        
        for($i=1;$i<=4;$i++){
            if((!empty($searchVars['jq_type'.$i])) && (!empty($searchVars['jq_term'.$i]))){
                    
                $jq.=$ezb->jq_type[$searchVars['jq_type'.$i]]. ' "'. $searchVars['jq_term'.$i].'" ';
                
                if(!empty($searchVars['jq_type2'])){
                    $jq.= ' '.$searchVars['jq_bool'.$i].' ';
                }
            }
        }
        if(!empty($jq)){
            $list = array(1 =>$jq);
        }

        //subjects
        if(!empty($searchVars['Notations'])){
            foreach($searchVars['Notations'] as $notation){
                if((!empty($this->ezb_to_t3_subjects[$notation])) && ($notation != '-')){
                    $list[] = $this->ezb_to_t3_subjects[$notation]['title'];
                }
            }    
            
        }
        
        //licence
        /*if(!empty($searchVars['selected_colors'])){
            $accessInfos = $this-> getAccessInfos();
            
            foreach($searchVars['selected_colors'] as $key=>$color){
                if($accessInfos[$color]){
                    $list[]=$accessInfos[$color]['value'];
                    
                    //if color is 2, 6 must be displayed too
                    if($color == 2){
                        $list[]=$accessInfos[3]['value'];
                    }
                }
            }
        }*/
        
        return $list;
    }
    
    /**
     * returns a subject
     * 
     * @param integer $subjectId Id des Faches
     */
    public function getSubject($subjectId){
        $this->loadSubjects();
        
        return $this->t3_to_ezb_subjects[$subjectId];
    }
    
    /**
     * get lit of participants
     * 
     * @param int $journalId
     * @return array
     */
    public function getParticipantsList($journalId) {
        $ezb = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('Tx_libconnect_Resources_Private_Lib_Ezb');
        $list = $ezb->getParticipantsList($journalId);

        $bibID = $ezb->getBibID();
        $list['BibID'] = $bibID;

        $list['detailURL'] = $ezb->getDetailviewRequestUrl() . '&jour_id=' . $journalId;

        return $list;
    }
    
    /**
     * get contact information
     * 
     * @return array contact information: person, email
     */
    public function getContact(){
        $ezb = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('Tx_libconnect_Resources_Private_Lib_Ezb');
        $contact = $ezb->getContact();
        
        return $contact;
    }
    
    /**
     * returns a value for parameter colors. 
     * 
     * @param array $colors
     *
     * @return array $sum
     */
    private function getColors($colors){
        $sum = 0;
        
        if(!empty($colors)){
            foreach($colors as $color){
                $sum += (int) $color;
            }
        }
        //0 is equal to all
        if($sum == 0){
            $sum = 7;
        }
        
        return $sum;
    }
}
?>
