<?php

namespace Subhh\Libconnect\Lib;

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
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 *
 * documentation: http://www.bibliothek.uni-regensburg.de/ezeit/vascoda/vifa/doku_xml_ezb.html
 * documentation: http://rzblx1.uni-regensburg.de/ezeit/vascoda/vifa/doku_xml_ezb.html
 * @author niklas guenther
 * @author Torsten Witt
 */

use Subhh\Libconnect\Service\Request;

class Ezb
{

    // document search meta infos
    private $title;

    // general config
    private $overview_requst_url = 'https://ezb.ur.de/ezeit/fl.phtml';
    private $detailview_request_url = 'https://ezb.ur.de/ezeit/detail.phtml';
    private $search_url = 'https://ezb.ur.de/ezeit/search.phtml';
    //private $qsearch_url = 'https://ezb.ur.de/ezeit/searchres.phtml?xmloutput=1&';
    //private $journal_link_url = 'https://ezb.ur.de/ezeit/warpto.phtml?bibid=SUBHH&colors=7&lang=de&jour_id=';
    private $search_result_page = 'https://ezb.ur.de/ezeit/searchres.phtml';
    //private $search_result_page = 'https://ezb.ur.de/ezeit/searchres.phtml?&xmloutput=1&bibid=SUBHH&colors=7&lang=de&';
    //private $search_result_page = 'https://ezb.ur.de/searchres.phtml?xmloutput=1&bibid=SUBHH&colors=7&lang=de';
    private $participants_url = 'https://ezb.ur.de/ezeit/where.phtml';
    private $participants_xml_url = 'https://ezb.ur.de/ezeit/where.phtml';
    private $contact_url = 'https://ezb.ur.de/ezeit/kontakt.phtml';
    private $ezb_domain = 'ezb.ur.de';

    private $lang = 'de';
    private $colors = 7;

    public $notation;
    public $sc;
    public $lc;
    public $sindex;

    // typoscript config
    private $bibID;

    // access info
    private $shortAccessInfos = [];
    private $longAccessInfos = [];

    // search types
    public $jq_type = 	[
        'KT' => 'Titelwort(e)',
        'KS' => 'Titelanfang',
        'IS' => 'ISSN',
        'PU' => 'Verlag',
        'KW' => 'Schlagwort(e)',
        'ID' => 'Eingabedatum',
        'LC' => 'Letzte Änderung',
        'ZD' => 'ZDB-Nummer'];

    /**
     * constructor
     */
    public function __construct()
    {
        //set configurations
        $this->setBibID();
        $this->setLanguage();
    }

    /**
     * sets ID of the library
     */
    private function setBibID()
    {
        $this->bibID = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_libconnect.']['ezbbibid'];
    }

    /**
     * returns the bibID
     *
     * @return string
     */
    public function getBibID()
    {
        return $this->bibID;
    }

    /**
     * get subjects
     *
     * @return array()
     */
    public function getFachbereiche()
    {
        $fachbereiche = [];

        $xml_response = $this->setRequest($this->overview_requst_url, ['colors' => $this->colors, 'lang' => $this->lang]);

        if (isset($xml_response->ezb_subject_list->subject)) {
            foreach ($xml_response->ezb_subject_list->subject as $key => $value) {
                $fachbereiche[(string)$value['notation'][0]] = ['title' => (string)$value[0], 'journalcount' => (int)$value['journalcount'], 'notation' => (string)$value['notation'][0]];
            }
        }

        return $fachbereiche;
    }

    /**
     * gets all journals of a subject
     *
     * @param string $jounal
     * @param string $letter
     * @param string $lc
     * @param $sindex int
     *
     * @return array()
     */
    public function getFachbereichJournals($jounal, $sindex = 0, $sc = 'A', $lc = '')
    {
        $journals = [];

        $xml_response = $this->setRequest($this->overview_requst_url, ['colors' => $this->colors, 'lang' => $this->lang, 'sc' => $sc, 'sindex' => $sindex, 'notation' => $jounal]);

        if ($xml_response->page_vars) {
            $this->notation = (string)$xml_response->page_vars->notation->attributes()->value;
            $this->sc = (string)$xml_response->page_vars->sc->attributes()->value;
            $this->lc = (string)$xml_response->page_vars->lc->attributes()->value;
            $this->sindex = (string)$xml_response->page_vars->sindex->attributes()->value;
        }

        //navigation list
        if ($xml_response->ezb_alphabetical_list) {
            $journals['subject'] = (string)$xml_response->ezb_alphabetical_list->subject;
            $journals['navlist']['current_page'] = (string)$xml_response->ezb_alphabetical_list->navlist->current_page;
            $journals['current_title'] = (string)$xml_response->ezb_alphabetical_list->current_title;

            foreach ($xml_response->ezb_alphabetical_list->navlist->other_pages as $key2 => $value2) {
                foreach ($value2->attributes() as $key3 => $value3) {
                    $journals['navlist']['pages'][(string)$value2[0]][(string)$key3] = (string)$value3;
                }
                // set title
                $journals['navlist']['pages'][(string)$value2[0]]['title'] = (string)$value2[0];
            }
        }
        $journals['navlist']['pages'][$journals['navlist']['current_page']] = $journals['navlist']['current_page'];
        ksort($journals['navlist']['pages']);

        //entries
        if (isset($xml_response->ezb_alphabetical_list->alphabetical_order->journals->journal)) {
            foreach ($xml_response->ezb_alphabetical_list->alphabetical_order->journals->journal as $key => $value) {
                $journals['alphabetical_order']['journals'][(int)$value->attributes()->jourid]['title'] = (string)$value->title;
                $journals['alphabetical_order']['journals'][(int)$value->attributes()->jourid]['jourid'] = (int)$value->attributes()->jourid;
                $journals['alphabetical_order']['journals'][(int)$value->attributes()->jourid]['color_code'] = (int)$value->journal_color->attributes()->color_code;
                $journals['alphabetical_order']['journals'][(int)$value->attributes()->jourid]['color'] = (string)$value->journal_color->attributes()->color;
                $journals['alphabetical_order']['journals'][(int)$value->attributes()->jourid]['detail_link'] = '';
                $journals['alphabetical_order']['journals'][(int)$value->attributes()->jourid]['warpto_link'] = $this->journal_link_url . $value->attributes()->jourid;
            }
        }

        $i = 0;

        if (isset($xml_response->ezb_alphabetical_list->next_fifty)) {
            foreach ($xml_response->ezb_alphabetical_list->next_fifty as $key => $value) {
                $journals['alphabetical_order']['next_fifty'][$i]['sc'] = (string)$value->attributes()->sc;
                $journals['alphabetical_order']['next_fifty'][$i]['lc'] = (string)$value->attributes()->lc;
                $journals['alphabetical_order']['next_fifty'][$i]['sindex'] = (string)$value->attributes()->sindex;
                $journals['alphabetical_order']['next_fifty'][$i]['next_fifty_titles'] = (string)$value->next_fifty_titles;
                $i++;
            }
        }

        $i = 0;

        if (isset($xml_response->ezb_alphabetical_list->first_fifty)) {
            foreach ($xml_response->ezb_alphabetical_list->first_fifty as $key => $value) {
                $journals['alphabetical_order']['first_fifty'][$i]['sc'] = (string)$value->attributes()->sc;
                $journals['alphabetical_order']['first_fifty'][$i]['lc'] = (string)$value->attributes()->lc;
                $journals['alphabetical_order']['first_fifty'][$i]['sindex'] = (string)$value->attributes()->sindex;
                $journals['alphabetical_order']['first_fifty'][$i]['first_fifty_titles'] = (string)$value->first_fifty_titles;
                $i++;
            }
        }

        return $journals;
    }

    /**
     * gets details of a journal
     *
     * @param journalId int
     *
     * @return string
     */
    public function getJournalDetail($journalId)
    {
        $journal = [];
        $xml_response = $this->setRequest($this->detailview_request_url, ['jour_id' => $journalId, 'colors' => $this->colors, 'lang' => $this->lang]);

        //$xml_request = $this->XMLPageConnection->getDataFromXMLPage($url);

        if (!is_object($xml_response->ezb_detail_about_journal->journal)) {
            return false;
        }

        $journal['id'] = (int)$xml_response->ezb_detail_about_journal->journal->attributes()->jourid;
        $journal['title'] = (string)$xml_response->ezb_detail_about_journal->journal->title;
        $journal['color'] = (string)$xml_response->ezb_detail_about_journal->journal->journal_color->attributes()->color;
        $journal['color_code'] = (int)$xml_response->ezb_detail_about_journal->journal->journal_color->attributes()->color_code;
        $journal['publisher'] = (string)$xml_response->ezb_detail_about_journal->journal->detail->publisher;
        $journal['ZDB_number'] = (string)@$xml_response->ezb_detail_about_journal->journal->detail->ZDB_number;
        $journal['ZDB_number_link'] = (string)@$xml_response->ezb_detail_about_journal->journal->detail->ZDB_number->attributes()->url;
        $journal['subjects'] = [];
        if (isset($xml_response->ezb_detail_about_journal->journal->detail->subjects->subject)) {
            foreach ($xml_response->ezb_detail_about_journal->journal->detail->subjects->subject as $subject) {
                $journal['subjects'][] = (string)$subject;
            }
        }
        $journal['subjects_join'] = implode(', ', $journal['subjects']);
        $journal['pissns'] = [];
        if (isset($xml_response->ezb_detail_about_journal->journal->detail->P_ISSNs->P_ISSN)) {
            foreach ($xml_response->ezb_detail_about_journal->journal->detail->P_ISSNs->P_ISSN as $pissn) {
                $journal['pissns'][] = (string)$pissn;
            }
        }
        $journal['pissns_join'] = implode(', ', $journal['pissns']);
        $journal['eissns'] = [];
        if (isset($xml_response->ezb_detail_about_journal->journal->detail->E_ISSNs->E_ISSN)) {
            foreach ($xml_response->ezb_detail_about_journal->journal->detail->E_ISSNs->E_ISSN as $eissn) {
                $journal['eissns'][] = (string)$eissn;
            }
        }
        $journal['eissns_join'] = implode(', ', $journal['eissns']);
        $journal['keywords'] = [];
        if (isset($xml_response->ezb_detail_about_journal->journal->detail->keywords->keyword)) {
            foreach ($xml_response->ezb_detail_about_journal->journal->detail->keywords->keyword as $keyword) {
                $journal['keywords'][] = (string)$keyword;
            }
        }

        $journal['fulltext'] = (string)$xml_response->ezb_detail_about_journal->journal->detail->fulltext;

        if (isset($xml_response->ezb_detail_about_journal->journal->detail->fulltext)) {
            $i = 1;
            $warpto = urlencode((string)$xml_response->ezb_detail_about_journal->journal->detail->fulltext->attributes()->url);
            $journal['fulltext_link'] = 'https%3A%2F%2F'.$this->ezb_domain.'%2Fezeit%2Fwarpto.phtml?bibid=' . $this->bibID . '&colors=' . $this->colors . '&lang=' . $this->lang . '&jour_id=' . $journalId . '&url=' . $warpto;
        }

        $journal['homepages'] = [];
        if (isset($xml_response->ezb_detail_about_journal->journal->detail->homepages->homepage)) {
            foreach ($xml_response->ezb_detail_about_journal->journal->detail->homepages->homepage as $homepage) {
                $journal['homepages'][] = [
                    'linktext' => (string)$homepage,
                    'url' => (string)$homepage->attributes()->url
                ];
            }
        }
        $journal['first_fulltext'] = [
            'volume' => (int)$xml_response->ezb_detail_about_journal->journal->detail->first_fulltext_issue->first_volume,
            'issue' => (int)$xml_response->ezb_detail_about_journal->journal->detail->first_fulltext_issue->first_issue,
            'date' => (int)$xml_response->ezb_detail_about_journal->journal->detail->first_fulltext_issue->first_date
        ];
        if ($xml_response->ezb_detail_about_journal->journal->detail->last_fulltext_issue) {
            $journal['last_fulltext'] = [
                'volume' => (int)$xml_response->ezb_detail_about_journal->journal->detail->last_fulltext_issue->last_volume,
                'issue' => (int)$xml_response->ezb_detail_about_journal->journal->detail->last_fulltext_issue->last_issue,
                'date' => (int)$xml_response->ezb_detail_about_journal->journal->detail->last_fulltext_issue->last_date
            ];
        }
        if (isset($xml_response->ezb_detail_about_journal->journal->detail->moving_wall)) {
            $temp = (string)$xml_response->ezb_detail_about_journal->journal->detail->moving_wall;
            $journal['moving_wall'] = $this->getMovingwall($temp);
        }
        $journal['appearence'] = (string)$xml_response->ezb_detail_about_journal->journal->detail->appearence;
        $journal['costs'] = (string)$xml_response->ezb_detail_about_journal->journal->detail->costs;
        $journal['remarks'] = trim((string)$xml_response->ezb_detail_about_journal->journal->detail->remarks);

        if (empty($journal['remarks'])) {
            unset($journal['remarks']);
        }

        //check institutions having access to this journal
        $journal['participants'] = $this->getParticipants($journalId);

        $color_map = [
            'green' => 1,
            'yellow' => 2,
            'red' => 4,
            'yellow_red' => 6
        ];
        $journal['periods'] = [];
        if (isset($xml_response->ezb_detail_about_journal->journal->periods->period)) {
            foreach ($xml_response->ezb_detail_about_journal->journal->periods->period as $period) {
                $i = 1;
                $warpto = '';
                $url = '';

                if (@$period->warpto_link->attributes()->url) {
                    $warpto = urlencode((string)$period->warpto_link->attributes()->url);
                }

                //check for uncomplete link
                $readme_link = (string)@$period->readme_link->attributes()->url;

                if (!empty($readme_link)) {
                    if (!preg_match('/^http/', $readme_link)) {
                        $url = 'https://'.$this->ezb_domain.'/ezeit/';
                    }
                }

                $journal['periods'][] = [
                    'label' => (string)$period->label,
                    'color' => (string)@$period->journal_color->attributes()->color,
                    'color_code' => $color_map[(string)@$period->journal_color->attributes()->color],
                    'link' => 'https%3A%2F%2F'.$this->ezb_domain.'%2Fezeit%2Fwarpto.phtml?bibid=' . $this->bibID . '%26colors=' . $this->colors . '%26lang=' . $this->lang . '%26jour_id=' . $journalId . '%26url=' . $warpto,
                    'readme' => $url . (string)@$period->readme_link->attributes()->url
                ];
            }
        }

        //$journal['moreDetails'] = $this->getMoreDetails($journalId);

        return $journal;
    }

    /**
     * returns form for detailed search
     *
     * @return array
     */
    public function detailSearchFormFields()
    {
        $xml_response = $this->setRequest($this->search_url, ['colors' => $this->colors, 'lang' => $this->lang]);

        foreach ($xml_response->ezb_search->option_list as $key => $value) {
            foreach ($value->option as $key2 => $value2) {
                $form[(string)$value->attributes()->name][(string)$value2->attributes()->value] = (string)$value2;
            }
        }

        return $form;
    }

    /**
     * creates search url, contains of the doamin and an array of parameter
     *
     * @param term string
     * @param searchVars array
     *
     * @return array
     */
    private function createSearchUrl($searchVars)
    {

        //if search was redirected from original website of EZB
        /*if(isset($searchVars['jq_type1']) && $searchVars['jq_type1'] == 'ZD'){

            return array('url' => $this->search_zd_id, 'searchParams' => array('jq_term1' => $searchVars['jq_term1'], 'lang' => $this->lang));
        }else{*/
        $searchParams['colors'] = $this->colors;
        $searchParams['lang'] = $this->lang;
        $searchParams['xmlv'] = 3;
        //}

        if (!$searchVars['sc']) {
            $searchParams['sc'] = 'A';
        }

        foreach ($searchVars as $var => $values) {
            if (!is_array($values)) {
                $values = utf8_decode($values);

                $searchParams[$var] = urlencode($values);
            } else {
                $utf8Values = [];

                foreach ($values as $value) {
                    if (is_string($value)) {
                        $utf8Values[] = @utf8_decode($value);
                    }
                }
                $values = $utf8Values;

                foreach ($values as $value) {
                    if (is_string($value)) {
                        $searchParams[$var] = $values;
                    }
                }
            }
        }

        if (isset($searchParams['colors'])) {
            $searchParams['colors'] = $this->getColorSum($searchParams['colors']);
        }

        return ['url' => $this->search_result_page, 'searchParams' => $searchParams];
    }

    /**
     * Caclulate the sum of all colors
     *
     * @param mixed $colors
     * @return intger sum
     */
    private function getColorSum($colors)
    {
        $sum = 0;

        if (!is_array($colors)) {
            return $colors;
        }
        foreach ($colors as $value) {
            $sum += $value;
        }

        if ($sum == 0) {
            $sum = 7;
        }

        return $sum;
    }

    /**
     * search
     *
     * @param string Suchstring
     *
     * @return array
     */
    public function search($searchVars = [])
    {
        $searchArray = $this->createSearchUrl($searchVars);

        $xml_response = $this->setRequest($searchArray['url'], $searchArray['searchParams']);

        if (!$xml_response) {
            return false;
        }

        $result = ['page_vars'];
        foreach ($xml_response->page_vars->children() as $key => $value) {
            $result = ['page_vars' => [$key => (string)$value->attributes()->value]];
        }

        foreach ($xml_response->page_vars->children() as $key => $value) {
            $result['page_vars'][$key] = (string)$value->attributes()->value;
        }

        //to edit search redirect of original EZB-website
        if (isset($xml_response->ezb_alphabetical_list)) {
            if (isset($xml_response->ezb_alphabetical_list->alphabetical_order->journals->journal)) {
                foreach ($xml_response->ezb_alphabetical_list->alphabetical_order->journals->journal as $key => $value) {
                    $result['alphabetical_order']['journals'][(int)$value->attributes()->jourid]['title'] = (string)$value->title;
                    $result['alphabetical_order']['journals'][(int)$value->attributes()->jourid]['jourid'] = (int)$value->attributes()->jourid;
                    $result['alphabetical_order']['journals'][(int)$value->attributes()->jourid]['color_code'] = (int)$value->journal_color->attributes()->color_code;
                    $result['alphabetical_order']['journals'][(int)$value->attributes()->jourid]['color'] = (string)$value->journal_color->attributes()->color;
                    $result['alphabetical_order']['journals'][(int)$value->attributes()->jourid]['detail_link'] = '';
                    $result['alphabetical_order']['journals'][(int)$value->attributes()->jourid]['warpto_link'] = $this->journal_link_url . $value->attributes()->jourid;
                }

                //count of search results
                $result['page_vars']['search_count'] = count($xml_response->ezb_alphabetical_list->alphabetical_order->journals->journal);
            }

            return $result;
        }

        //precise hits
        if (isset($xml_response->ezb_alphabetical_list_searchresult->precise_hits->journals->journal)) {
            foreach ($xml_response->ezb_alphabetical_list_searchresult->precise_hits->journals->journal as $key => $precise_hit) {
                $result['precise_hits'][] = [
                    'jourid' => (string)$precise_hit->attributes()->jourid,
                    'title' => (string)$precise_hit->title,
                    'color_code' => (int)$precise_hit->journal_color->attributes()->color_code,
                    'color' => (int)$precise_hit->journal_color->attributes()->color
                ];
            }
        }

        //count hits
        $result['page_vars']['search_count'] = (int)$xml_response->ezb_alphabetical_list_searchresult->search_count;

        if (isset($xml_response->ezb_alphabetical_list_searchresult->navlist->other_pages)) {
            foreach ($xml_response->ezb_alphabetical_list_searchresult->navlist->other_pages as $key2 => $value2) {
                foreach ($value2->attributes() as $key3 => $value3) {
                    $result['navlist']['pages'][(string)$value3] = [
                        'id' => (string)$value3,
                        'title' => (string)$value2
                    ];
                }
            }
        }
        $current_page = (string)$xml_response->ezb_alphabetical_list_searchresult->navlist->current_page;

        if ($current_page) {
            $result['navlist']['pages'][$current_page] = $current_page;
        }
        if (is_array($result['navlist']['pages'])) {
            ksort($result['navlist']['pages']);
        }

        if ($xml_response->ezb_alphabetical_list_searchresult->current_title) {
            $result['current_title'] = (string)$xml_response->ezb_alphabetical_list_searchresult->current_title;
        }

        if (isset($xml_response->ezb_alphabetical_list_searchresult->alphabetical_order->journals->journal)) {
            foreach ($xml_response->ezb_alphabetical_list_searchresult->alphabetical_order->journals->journal as $key => $value) {
                $result['alphabetical_order']['journals'][(int)$value->attributes()->jourid]['title'] = (string)$value->title;
                $result['alphabetical_order']['journals'][(int)$value->attributes()->jourid]['jourid'] = (int)$value->attributes()->jourid;
                $result['alphabetical_order']['journals'][(int)$value->attributes()->jourid]['color_code'] = (int)$value->journal_color->attributes()->color_code;
                $result['alphabetical_order']['journals'][(int)$value->attributes()->jourid]['color'] = (string)$value->journal_color->attributes()->color;
                $result['alphabetical_order']['journals'][(int)$value->attributes()->jourid]['detail_link'] = '';
                $result['alphabetical_order']['journals'][(int)$value->attributes()->jourid]['warpto_link'] = $this->journal_link_url . $value->attributes()->jourid;
            }
        }

        $i = 0;
        foreach ($xml_response->ezb_alphabetical_list_searchresult->next_fifty as $key => $value) {
            $result['alphabetical_order']['next_fifty'][$i]['sc'] = (string)$value->attributes()->sc;
            $result['alphabetical_order']['next_fifty'][$i]['sindex'] = (string)$value->attributes()->sindex;
            $result['alphabetical_order']['next_fifty'][$i]['next_fifty_titles'] = (string)$value->next_fifty_titles;
            $i++;
        }

        $i = 0;
        foreach ($xml_response->ezb_alphabetical_list_searchresult->first_fifty as $key => $value) {
            $result['alphabetical_order']['first_fifty'][$i]['sc'] = (string)$value->attributes()->sc;
            $result['alphabetical_order']['first_fifty'][$i]['sindex'] = (string)$value->attributes()->sindex;
            $result['alphabetical_order']['first_fifty'][$i]['first_fifty_titles'] = (string)$value->first_fifty_titles;
            $i++;
        }

        return $result;
    }

    /**
     * sets access information
     */
    public function setShortAccessInfos()
    {
        $this->shortAccessInfos = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_libconnect.']['settings.']['ezbshortaccessinfos.'][$this->lang . '.'];
    }

    /**
     * retruns access information
     *
     * @return array $return
     */
    public function getShortAccessInfos()
    {
        $this->setShortAccessInfos();

        $return = ['shortAccessInfos' => $this->shortAccessInfos];

        return $return;
    }

    /**
     * sets more access information
     */
    public function setLongAccessInfos()
    {
        $this->longAccessInfos = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_libconnect.']['settings.']['ezblongaccessinfos.'][$this->lang . '.'];
    }

    /**
     * returns more access information
     *
     * @return array $return
     */
    public function getLongAccessInfos()
    {
        $this->setLongAccessInfos();

        $return = ['longAccessInfos' => $this->longAccessInfos, 'force' => $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_libconnect.']['settings.']['ezblongaccessinfos.']['force']];

        return $return;
    }

    /**
     * returns list where journal at partners available
     *
     * @param type $jour_id
     *
     * @return array full list of Partner with Journal
     */
    public function getParticipantsList($jour_id)
    {
        $xml_response = $this->setRequest($this->participants_xml_url, ['jour_id' => $jour_id]);

        $participantsList = [];

        if (!$xml_response) {
            return false;
        }

        if ($xml_response->ezb_where_journal_at_partners->partner_selection->countries->country) {
            foreach ($xml_response->ezb_where_journal_at_partners->partner_selection->countries->country as $country) {
                $participantsList['countries'][(string)$country->attributes()->ID] = (string)$country[0];
            }

            foreach ($xml_response->ezb_where_journal_at_partners->partner_selection->categories->category as $category) {
                $participantsList['categories'][(string)$category->attributes()->ID]['countryrefs'] = (string)$category->attributes()->countryrefs;
                $participantsList['categories'][(string)$category->attributes()->ID]['category_name'] = (string)$category->category_name;
            }

            foreach ($xml_response->ezb_where_journal_at_partners->partner_selection->institutions->institution  as $institution) {
                $participantsList['institutions'][(string)$institution->attributes()->ID]['catrefs'] = (string)$institution->attributes()->catrefs;
                $participantsList['institutions'][(string)$institution->attributes()->ID]['countryref'] = (string)$institution->attributes()->countryref;
                $participantsList['institutions'][(string)$institution->attributes()->ID]['name'] = (string)$institution->name;
                $participantsList['institutions'][(string)$institution->attributes()->ID]['city'] = (string)$institution->city;
            }
        }

        return $participantsList;
    }

    /**
     * set colors
     *
     * @param int $colors sum of colors
     */
    public function setColors($colors)
    {
        $this->colors = $colors;
    }

    /**
     * gets colors
     *
     * @param int $colors sum of colors
     */
    public function getColors()
    {
        return $this->colors;
    }

    /**
     * checks institutions having access to this journal
     *
     * @param int $jour_id
     *
     * @return bool
     */
    public function getParticipants($jour_id)
    {
        $participants = false;
        $xml_response = $this->setRequest($this->participants_xml_url, ['colors' => $this->colors, 'lang' => $this->lang, 'jour_id' => $jour_id]);

        if ($xml_response->ezb_where_journal_at_partners->partner_selection->institutions->institution) {
            foreach ($xml_response->ezb_where_journal_at_partners->partner_selection->institutions->institution->children() as $childs) {
                $participants = "{$this->participants_url}?bibid={$this->bibID}&colors={$this->colors}&lang={$this->lang}&jour_id={$jour_id}";
                break;
            }
        }

        return $participants;
    }

    /**
     * return contact information
     *
     * @return array
     */
    public function getContact()
    {
        $contact = false;
        $xml_response = $this->setRequest($this->contact_url, ['colors' => $this->colors, 'lang' => $this->lang]);

        if ($xml_response->library) {
            $contact['library']['library'] = (string)$xml_response->library;
            $contact['library']['www'] = (string)$xml_response->library ->attributes()->href;
        }

        if ($xml_response->ezb_contact) {
            $contact['email'] = (string)$xml_response->ezb_contact->attributes()->href;
            $contact['person'] = (string)$xml_response->ezb_contact;
        }

        return $contact;
    }

    /**
     * sets the movin wall
     *
     * @param string $value
     *
     * @return array
     */
    private function getMovingwall($value)
    {
        $value = trim($value);

        //prefix
        $prefixTemp = rtrim(rtrim($value, 'YMDIV'), '0123456789');
        if ($prefixTemp == '-') {
            $prefix = 'not';
        } else {
            $prefix = 'yes';
        }

        //number
        $number = (int)rtrim(ltrim($value, '+-'), 'YMDIV');

        //duration
        $duration = strtolower(ltrim(ltrim($value, '+-'), '0123456789'));

        $moving_wall = ['prefix' => [$prefix => true], 'number' => $number, 'duration' => [$duration => true]];

        return $moving_wall;
    }

    /**
     * sets language
     */
    private function setLanguage()
    {
        //get current language
        $lang = $GLOBALS['TSFE']->config['config']['language'];

        //only de and en is allowed
        if (($lang != 'de') && ($lang != 'en')) {
            $lang = 'de';
        }

        $this->lang = $lang;
    }

    /**
    * returns detailview_request_url
    *
    * @return string
    */
    public function getDetailviewRequestUrl()
    {
        $url = $this->detailview_request_url . '?colors=' . $this->colors . '&lang=' . $this->lang;

        return $url;
    }

    /**
     * get some inforemation from the HTML page
     *
     * @param int
     *
     * @return array
     */
    private function getMoreDetails($journalId)
    {
        $params = ['bibid' => $this->bibID, 'jour_id' =>$journalId, 'colors' => $this->colors, 'lang' => $this->lang, 'xmloutput' => 0];
        $htmlResponse = $this->setRequest('http://'.$this->ezb_domain.'/ezeit/detail.phtml', $params);

        $moreDetails = [];

        //replace double white space in single
        $htmlResponse = trim(preg_replace('/\s\s+/', ' ', $htmlResponse));

        //start "Preistyp Anmerkung"
        $searchString = 'Preistyp Anmerkung';

        if ($this->lang == 'en') {
            $searchString = 'Pricetype annotation';
        }

        preg_match('/' . $searchString . ':\s*<\/dt>\s*<dd class="defListContentDefinition">(.*)<\/dd>/mU', $htmlResponse, $matches);

        //preparing string
        if ($matches[1]) {
            $price_type_annotation = str_replace('<br />', '', $matches[1]);
            $price_type_annotation = str_replace(':', '', $price_type_annotation);

            $price_type_annotation = utf8_encode(trim($price_type_annotation));
        }
        $moreDetails['price_type_annotation'] = $price_type_annotation;

        return $moreDetails;
    }

    /**
     * @param type string
     * @param type array
     * @return SimpleXMLElement
     */
    private function setRequest($url, $params = [])
    {
        $request = new \Subhh\Libconnect\Service\Request();

        $request->setUrl($url);
        $request->setQuery(['bibid' => $this->bibID, 'xmloutput' => 1 ]);

        //set empty valus as NULL for Guzzle
        //foreach ($params as $key => $value) {
        //    if (empty($value)) {
        //        $params[$key] = null;
        //    }
        //}

        if (!empty($params)) {
            $request->setQuery($params);
        }

        $xml_response = $request->request();

        return $xml_response;
    }
}

