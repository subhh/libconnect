<?php

namespace Subhh\Libconnect\Domain\Repository;

/***************************************************************
* Copyright notice
*
* (c) 2009 by Avonis - New Media Agency
*
* All rights reserved
*
* This script is part of the EZB/DBIS-Extension project. The EZB/DBIS-Extension project
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

use TYPO3\CMS\Core\Site\Entity\SiteLanguage;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Subhh\Libconnect\Lib\Ezb;
use Subhh\Libconnect\Lib\Zdb;

/**
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 */
class EzbRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{
    private $ezb_to_t3_subjects = [];
    private $t3_to_ezb_subjects = [];

    private $longAccessInfos = [];

    /**
     * @var SubjectRepository
     */
    protected $subjectRepository;

    /**
     * @var Ezb
     */
    protected $ezb;

    /**
     * @var Zdb
     */
    protected $zdb;

    /**
     * get list for start page
     *
     * @return array $list
     */
    public function loadOverview()
    {
        $subjectsOnline = $this->ezb->getFachbereiche();

        return $subjectsOnline;
    }

    /**
     * fill variable $ezb_to_t3_subjects with list of subjects
     */
    private function loadSubjects()
    {
        $res =  $this->subjectRepository->findAll();

        foreach ($res as $row) {
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
    public function loadList($subject_id, $options = ['sindex' =>0, 'sc' => 'A', 'lc' => ''], $config)
    {
        $sindex = $options['sindex'];
        $sc = $options['sc'];
        $lc = $options['lc'];

        //get subject
        $subject = $this->loadOverview();

        //list of subject or all
        if($options['notation'] == 'All'){
            $subject['notation'] = 'All';
        }else{
            $subject = $subject[$subject_id];
        }

        //filter list by access list
        if (!empty($options['colors'])) {
            $colors = $this->getColors($options['colors']);
            $this->ezb->setColors($colors);

            $colorList = $options['colors'];
        } else {
            $colorList = [
                1 => 1,
                2 => 2,
                4 => 4,
                6 => 6
            ];
        }

        $journals = $this->ezb->getFachbereichJournals($subject['notation'], $sindex, $sc, $lc);

        //get access information
        $journals['selected_colors'] = $this->getAccessInfos();
        $journals['colors'] = $colorList;

        /**
         * create links
         */
        //navigation - letters
        foreach (array_keys($journals['navlist']['pages']) as $page) {
            if (is_array($journals['navlist']['pages'][$page])) {
                $journals['navlist']['pages'][$page]['link']['libconnect'] = array(
                    'sindex' => 0,
                    'sc' => $journals['navlist']['pages'][$page]['sc']? $journals['navlist']['pages'][$page]['sc'] : 'A',
                    'lc' => $journals['navlist']['pages'][$page]['lc'],
                    'notation' => $subject['notation'],
                    'colors' => $journals['colors']
                );
            }
        }

        //navigation - sections in letters
        if (isset($journals['alphabetical_order']['first_fifty'])) {
            foreach (array_keys($journals['alphabetical_order']['first_fifty']) as $section) {
                $journals['alphabetical_order']['first_fifty'][$section]['link']['libconnect'] = array(
                    'sindex' => $journals['alphabetical_order']['first_fifty'][$section]['sindex'],
                    'sc' => $journals['alphabetical_order']['first_fifty'][$section]['sc']? $journals['alphabetical_order']['first_fifty'][$section]['sc'] : 'A',
                    'lc' => $journals['alphabetical_order']['first_fifty'][$section]['lc'],
                    'notation' => $subject['notation'],
                    'colors' => $journals['colors']
                );
            }
        }
        if (isset($journals['alphabetical_order']['journals'])) {
            foreach (array_keys($journals['alphabetical_order']['journals']) as $journal) {
                $journals['alphabetical_order']['journals'][$journal]['jourid'] = $journals['alphabetical_order']['journals'][$journal]['jourid'];
            }
        }
        //navigation - sections in letters
        if (isset($journals['alphabetical_order']['next_fifty'])) {
            foreach (array_keys($journals['alphabetical_order']['next_fifty']) as $section) {
                $journals['alphabetical_order']['next_fifty'][$section]['link']['libconnect'] = array(
                    'sindex' => $journals['alphabetical_order']['next_fifty'][$section]['sindex'],
                    'sc' => $journals['alphabetical_order']['next_fifty'][$section]['sc']? $journals['alphabetical_order']['next_fifty'][$section]['sc'] : 'A',
                    'lc' => $journals['alphabetical_order']['next_fifty'][$section]['lc'],
                    'notation' => $subject['notation'],
                    'colors' => $journals['colors']
                );
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
     * @return bool
     */
    public function loadDetail($journalId, $config)
    {
        $journal = $this->ezb->getJournalDetail($journalId);

        if (! $journal) {
            return false;
        }

        /*BEGIN get access information*/

        //get default texts
        $LongAccessInfos =  $this->ezb->getLongAccessInfos();

        $colortext = [];
        if ((!empty($LongAccessInfos['longAccessInfos'])) && ($LongAccessInfos['longAccessInfos']!= false)) {
            foreach ($LongAccessInfos as $key =>$text) {
                $colortext[$key] = $text;
            }
        }

        //get texts from the web
        $form =  $this->ezb->detailSearchFormFields();
        $journal['selected_colors'] = $form['selected_colors'];

        $color = $journal['color_code'];
        unset($journal['color_code']);
        $journal['color_code'] = [];

        if ((!isset($journal['selected_colors'][$color])) or (empty($journal['selected_colors'][$color])) or ($LongAccessInfos['force'] == 'true')) {
            $journal['color_code']['text'] = $colortext['longAccessInfos'][$color];
        } else {
            $journal['color_code']['text'] = $journal['selected_colors'][$color];
        }
        $journal['color_code']['color'] = $color;
        /*END get access information*/

        //generate link to institutions having access to this journal
        if ($journal['participants'] == true) {
            if ($config['participantsPid'] and $config['participantsPid'] != 0) {
                $journal['participants'] = $journalId;
            }
        }

        //get links for subjects
        if (!empty($config['listPid'])) {
            $allSubjects = $this->loadOverview();
            
            foreach ($allSubjects as $OneOfAllSubjects) {
                
                if (in_array($OneOfAllSubjects['title'], $journal['subjects'])) {
                    $journal['subjects_link'][] = array(
                        'notation' => $OneOfAllSubjects['notation'],
                        'title' => $OneOfAllSubjects['title']
                    );
                }
            }
        }

        //get keywords
        if (!empty($config['listPid'])) {
            $journal['keywords_for_links'] = $this->getKeywords4Links($journal['keywords']);
        }

        //get title history
        if (!empty($journal['ZDB_number'])) {
            $journal['title_history'] = $this->getTitleHistory($journal['ZDB_number']);
        }

        return $journal;
    }

    /**
     * creates parameter for links
     *
     * @param array $keywods list of keywods
     *
     * @return string
     */
    public function getKeywords4Links($keywords)
    {
        //example http://rzblx1.uni-regensburg.de/ezeit/searchres.phtml?bibid=SUBHH&colors=7&lang=de&jq_type1=KW&jq_term1=Radiologie

        $tempKeywords = [];
        foreach ($keywords as $keyword) {
            $tempKeywords[] = array( 'search' => array(
                    'colors' => '7',
                    'jq_term1' =>  $keyword,
                    'jq_type1' => 'KW'
            ), 'keyword' => $keyword);
        }

        return $tempKeywords;
    }

    /**
     * search
     *
     * @param array $searchVars
     * @param array $colors
     *
     * @return array $journals
     */
    public function loadSearch($searchVars, $colors)
    {
        unset($searchVars['colors']);

        $journals = $this->search($searchVars, $colors);

        $journals['searchDescription'] = $this->getSearchDescription($searchVars);
        //sometimes there is an empty element
        if(empty(end($journals['searchDescription']))){
            array_pop($journals['searchDescription']);
        }
        //remove last "AND" or "OR" in the text, if it is the last or only element
        $text = $journals['searchDescription'][count( $journals['searchDescription']) ];
        $text = trim($text, " AND");
        $text = trim($text, " OR");
        $journals['searchDescription'][count( $journals['searchDescription']) ] = $text;

        //get access information
        $journals['selected_colors'] = $this->getAccessInfos();
        $journals['AccessInfos'] = $journals['selected_colors'];
        $journals['colors'] = $colors;

        //create additional parameters for links
        $linkParams = [];
        foreach ($searchVars as $key => $value) {
            $linkParams['link']['libconnect'][$key] = $value;
        }

        if (!empty($searchVars['notation'])) {
            $linkParams['notation'] = $searchVars['notation'];
        }

        $journals = $this->getLinks($journals, $linkParams);

        return $journals;
    }

     /**
     * search
     *
     * @param array $searchVars
     * @param array $colors
     *
     * @return array $journals
     */
    public function Search($searchVars, $colors)
    {
        unset($searchVars['colors']);

        //search of sidebar
        if (!empty($searchVars['sword'])) {
            $searchVars['jq_type1'] = 'QS';
            $searchVars['jq_term1'] = $searchVars['sword'];

            unset($searchVars['sword']);//no need
        }

        $ezbColors = $this->getColors($colors);
        $this->ezb->setColors($ezbColors);

        $journals =  $this->ezb->search($searchVars);

        return $journals;
    }


    /**
     * get links for navigation, precise hits and paging
     *
     * @param array $journals
     * @param array $config configuration
     * @param array $linkParams parameter for links
     *
     * @return array with navigation, precise hits, and paging,
     */
    public function getLinks($journals, $linkParams)
    {
        //merging of parameter: keep search at paging alive

        //@todo: parameter lang

        //navigation - letters
        if (!empty($journals['navlist']['pages'])) {
            foreach (array_keys($journals['navlist']['pages']) as $page) {
                if (is_array($journals['navlist']['pages'][$page])) {
                    $journals['navlist']['pages'][$page]['link']['libconnect'] = array_merge($linkParams['link']['libconnect'], array(
                        'sc' => $journals['navlist']['pages'][$page]['id'],
                        'colors' => $journals['colors'],
                        'sindex' => 0
                    ));
                }
            }
        }

        //precise hits
        if (!empty($journals['precise_hits'])) {
            foreach (array_keys($journals['precise_hits']) as $precise_hit) {
                if (is_array($journals['precise_hits'][$precise_hit])) {
                    $journals['precise_hits'][$precise_hit]['jourid'] = $journals['precise_hits'][$precise_hit]['jourid'];
                }
            }
        }

        //navigation - sections in letters
        if (!empty($journals['alphabetical_order']['first_fifty'])) {
            foreach (array_keys($journals['alphabetical_order']['first_fifty']) as $section) {
                $journals['alphabetical_order']['first_fifty'][$section]['link']['libconnect'] = array_merge($linkParams['link']['libconnect'], array(
                    'sindex' => $journals['alphabetical_order']['first_fifty'][$section]['sindex'],
                    'sc' => $journals['alphabetical_order']['first_fifty'][$section]['sc'],
                    'colors' => $journals['colors']
                ));
            }
        }

        if (!empty($journals['alphabetical_order']['journals'])) {
            foreach (array_keys($journals['alphabetical_order']['journals']) as $journal) {
                $journals['alphabetical_order']['journals'][$journal]['jourid'] = $journals['alphabetical_order']['journals'][$journal]['jourid'];
            }
        }

        if (!empty($journals['alphabetical_order']['next_fifty'])) {
            foreach (array_keys($journals['alphabetical_order']['next_fifty']) as $section) {
                $journals['alphabetical_order']['next_fifty'][$section]['link']['libconnect'] = array_merge($linkParams['link']['libconnect'], array(
                    'sindex' => $journals['alphabetical_order']['next_fifty'][$section]['sindex'],
                    'sc' => $journals['alphabetical_order']['next_fifty'][$section]['sc'],
                    'colors' => $journals['colors']
                ));
            }
        }

        return $journals;
    }

    /**
     * create search form
     *
     * @return array
     */
    public function loadForm()
    {
        $form =  $this->ezb->detailSearchFormFields();

        //Zugriffsinformationen holen
        $form['colors'] = $this->getAccessInfos();

        return $form;
    }

    /**
     * get BibID
     *
     * @return string
     */
    public function getBibid()
    {
        return $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_libconnect.']['ezbbibid'];
    }

    /**
     * BOF ZDB LocationData
     *
     * get information about the location for the print version
     *
     * @param array $journal
     */
    public function loadLocationData($journal)
    {
        if(empty($journal['ZDB_number'])){
            $journal['ZDB_number'] = NULL;
        }

        if(count($journal['pissns'])){
            $locationData = $this->zdb->getJournalLocationDetails( array('issn' => reset($journal['pissns']) ), $journal['ZDB_number'] );
        } elseif(count($journal['eissns'])){
            $locationData = $this->zdb->getJournalLocationDetails( array('eissn' => reset($journal['eissns']) ), $journal['ZDB_number'] );
        }

        if (!isset($locationData)) {

            return false;
        }

        return $locationData;
    }

    /**
     * set detailed access information
     *
     * @param array $longAccessInfos
     */
    public function setLongAccessInfos($longAccessInfos)
    {
        $this->longAccessInfos = $longAccessInfos;
    }

    /**
     * get detailed access information
     *
     * @return array
     */
    public function getLongAccessInfos()
    {
        return $this->longAccessInfos;
    }

    /**
     * get licence information
     *
     * @return array
     */
    public function getAccessInfos()
    {
        //get default texts
        $AccessInfos = $this->ezb->getLongAccessInfos();

        $colortext = [];
        if ((!empty($AccessInfos['longAccessInfos'])) && ($AccessInfos['longAccessInfos']!= false)) {
            foreach ($AccessInfos['longAccessInfos'] as $key =>$text) {
                $colortext[$key] = $text;
            }
        }

        //reorginize array
        $return = array();
        foreach ($colortext as $colorkey => $value) {
            if ($colorkey != 6) {
                $key = $colorkey;
            } else {
                $key = 3;
            }
            $return[$key] = [
                'colorkey' => $colorkey,
                'value' => $value
            ];
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
    private function getSearchDescription($searchVars)
    {
        $list = [];

        //search terms and their categories
        $jq = '';

        if (!empty($searchVars['sword'])) {
            $searchVars['jq_type1'] = 'QS';
            $searchVars['jq_term1'] = $searchVars['sword'];

            unset($searchVars['sword']);//no need
        }

        for ($i=1;$i<=4;$i++) {
            if ((!empty($searchVars['jq_type' . $i])) && (!empty($searchVars['jq_term' . $i]))) {
                $jq .= $this->ezb->jq_type[$searchVars['jq_type' . $i]] . ' "' . $searchVars['jq_term' . $i] . '" ';

                if (!empty($searchVars['jq_type2'])) {
                    $jq.= ' ' . $searchVars['jq_bool' . $i] . ' ';
                }
            }
        }
        if (!empty($jq)) {
            $list = [1 =>$jq];
        }

        //subjects
        if (!empty($searchVars['Notations'])) {

            $subjects = $this->loadOverview();

            foreach ($searchVars['Notations'] as $notation) {
                $list[] = $subjects[$notation]['title'];
            }
        }

        return $list;
    }

    /**
     * returns a subject
     *
     * @param int $notation id of subject
     * @return Array subject
     */
    public function getSubject($notation)
    {
        $subject = $this->loadOverview();
        
        return $subject[$notation];
    }

    /**
     * get list of participants
     *
     * @param int $journalId
     * @return array
     */
    public function getParticipantsList($journalId)
    {
        $list = $this->ezb->getParticipantsList($journalId);

        $bibID = $this->ezb->getBibID();
        $list['BibID'] = $bibID;

        $list['participants_url'] = $this->ezb->getParticipants_url();

        return $list;
    }

    /**
     * get contact information
     *
     * @return array contact information: person, email
     */
    public function getContact()
    {
        $contact = $this->ezb->getContact();

        return $contact;
    }

    /**
     * returns a single value for parameter colors.
     *
     * @param array $colors
     *
     * @return array $sum
     */
    private function getColors($colors)
    {
        $sum = 0;

        if (!empty($colors)) {
            foreach ($colors as $color) {
                $sum += (int)$color;
            }
        }
        //0 is equal to all
        if ($sum == 0) {
            $sum = 7;
        }

        return $sum;
    }

    /**
     * returns the title history
     *
     * @param string $zdbId
     * @return mixed
     */
    private function getTitleHistory($zdbId)
    {
        $precursor = $this->zdb->getPrecursor($zdbId, true);

        krsort($precursor);

        $successor = $this->zdb->getSuccessor($zdbId);

        if ((empty($precursor)) && (empty($successor))) {
            return false;
        }

        return ['precursor' => $precursor, 'zdbData' => $this->zdb->getZdbData(), 'successor' => $successor];
    }

    /**
     * @param \Subhh\Libconnect\Domain\Repository\SubjectRepository $subjectRepository
     */
    public function injectSubjectRepository(SubjectRepository $subjectRepository)
    {
        $this->subjectRepository = $subjectRepository;
    }

    /**
     * @param \Subhh\Libconnect\Lib\Ezb $ezb
     */
    public function injectEzb(Ezb $ezb) {
        $this->ezb = $ezb;
    }

    /**
     * @param \Subhh\Libconnect\Lib\Zdb $zdb
     */
    public function injectZdb(Zdb $zdb) {
        $this->zdb = $zdb;
    }

    /**
    * @param string $languageCode
    **/
    public function setLanguageCode($languageCode)
    {
        $this->ezb->setLanguage($languageCode);
    }
    
    /**
    * @return string $languageCode
    **/
    public function getLanguageCode()
    {
        return $this->ezb->getLanguage();
    }
}

