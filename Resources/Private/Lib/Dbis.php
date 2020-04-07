<?php

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

use \Sub\Libconnect\Service\Request;

/**
 *
 * @package libconnect
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 *
 * @author niklas guenther
 * @author Torsten Witt
 *
 */

if (!defined('TYPO3_COMPOSER_MODE') && defined('TYPO3_MODE')) {
    require_once(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('libconnect') . 'Resources/Private/Lib/Xmlpageconnection.php');
    require_once(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('libconnect') . 'Resources/Private/Lib/Httppageconnection.php');
}


class Tx_Libconnect_Resources_Private_Lib_Dbis {

    //such meta
    private $fachgebiet;
    private $colors = '';
    private $ocolors = '';
    private $lett = 'f';
    private $fachliste_url = 'http://dbis.uni-regensburg.de/dbinfo/fachliste.php';
    private $dbliste_url = 'http://dbis.uni-regensburg.de/dbinfo/dbliste.php';
    private $db_detail_url = 'http://dbis.uni-regensburg.de/dbinfo/detail.php';
    private $db_detail_suche_url = 'http://dbis.uni-regensburg.de/dbinfo/suche.php';

    public $all;
    public $top_five_dbs;
    //variable of typoscript configuration
    private $bibID;
    private $licenceForbid = array();
    // sources
    private $HttpPageConnection;

    /**
     * constructor
     */
    public function __construct() {
        //$this->HttpPageConnection = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('tx_libconnect_resources_private_lib_httppageconnection');
        $this->setBibID();
    }

    /**
     * sets ID of the library
     */
    public function setBibID() {
        $this->bibID = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_libconnect.']['dbisbibid'];
    }

    /**
     * Set id Fachgebiet
     *
     * @param integer $fachgebiet
     */
    public function setGebiet($fachgebiet) {
        $this->fachgebiet = (int) $fachgebiet;
    }

    /**
     * Set the letter string
     *
     * @param string $lett
     */
    public function setLett($lett) {
        $this->lett = $lett;
    }

    /**
     * Set the int value colors
     *
     * @param integer $colors
     */
    public function setColors($colors) {
        $this->colors = $colors;
    }

    /**
     * Set the int value ocolors
     *
     * @param integer $ocolors
     */
    public function setOcolors($ocolors) {
        $this->ocolors = $ocolors;
    }

    /**
     * Set the array with unused licences
     */
    public function setLicenceForbid() {
        $this->licenceForbid = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_libconnect.']['settings.']['dbislicenceforbid.'];
    }

    /**
     * red all subjects
     *
     * @return array()
     */
    public function getFachliste() {

        $xml_categories = $this->getRequestFachliste('');
        $categories = array();

        if (isset($xml_categories->list_subjects_collections->list_subjects_collections_item)) {
            foreach ($xml_categories->list_subjects_collections->list_subjects_collections_item AS $key => $value) {
                $categories[(string) $value['notation']] = array(
                    'title' => (string) $value,
                    'id' => (string) $value['notation'],
                    'count' => (int) (string) $value['number'],
                    'lett' => (string) $value['lett']);
            }
        }

        return $categories;
    }

    /**
     * returns entries of a subject
     *
     * @param integer $fachgebiet
     * @param string $sort
     *
     * @return array
     */
    public function getDbliste($fachgebiet, $sort = 'type', $accessFilter = FALSE) {
        $sortlist = array();
        
        $params = array(
                    'colors' => $this->colors,
                    'ocolors' => $this->ocolors,
                    'sort' => $sort
                );

        $headline = '';

        //BOF workaround for alphabetical listing
        if ($fachgebiet == 'all') {

            $params['lett'] = 'a';
            $tmpParams = \TYPO3\CMS\Core\Utility\GeneralUtility::_GET('libconnect');

            if (!empty($tmpParams['lc'])) {
                if( !empty($tmpParams['lc'][0]) ){
                    $tmpParams['lc'] = $tmpParams['lc'][0];
                }
 
                 $params['lc'] = $tmpParams['lc'];
            }
            if (!empty($tmpParams['fc'])) {
                if (!empty($tmpParams['fc'][0])) {
                    $tmpParams['fc'] = $tmpParams['fc'][0];
                }
                
                $params['fc'] = $tmpParams['fc'];
            }
        } else {

            if (is_numeric($fachgebiet)) {
                //notation is an id => dbis collection
                $params['lett'] = 'f';
                $params['gebiete'] = $fachgebiet;
            } else {
                //notation is a character => own colelction
                $params['lett'] = 'c';
                $params['collid'] = $fachgebiet;
            }
        }
        //EOF workaround for alphabetical listing

        $xml_fachgebiet_db = $this->setRequest($this->dbliste_url, $params);

        $list = array(
            'top' => array(),
            'groups' => array(),
            'access_infos' => array()
        );

        //name of own collection
        if (isset($xml_fachgebiet_db->headline)){
            $headline = $xml_fachgebiet_db->headline;
        }

        //BOF workaround for alphabetical listing
        if (is_object($xml_fachgebiet_db->list_dbs->alphabetical_list)) {
            $alphabeticalNavList = array();
            if(isset($xml_fachgebiet_db->list_dbs->alphabetical_list->block_of_chars)){
                foreach ($xml_fachgebiet_db->list_dbs->alphabetical_list->block_of_chars as $charBlock) {
                    $tmpCharArray = array();
                    foreach ($charBlock->char as $char) {
                        $tmpCharArray[] = $char;
                    }
                    $alphabeticalNavList[] = array(
                        'chars' => $tmpCharArray,
                        'fc' => (string)$charBlock->attributes()->fc,
                        'lc' => (string)$charBlock->attributes()->lc,
                        //check current view for which char is shown
                        'current' => ($charBlock->attributes()->lc == $tmpParams['lc'] || $charBlock->attributes()->fc == $tmpParams['lc'] ? TRUE : FALSE)
                    );

                }
            }
            //check if a current view got set
            // if not, set the first charBlock as current view
            $currentChk = FALSE;
            foreach ($alphabeticalNavList as $value) {
                if ($value['current']) {
                    $currentChk = TRUE;
                    break;
                }
            }
            if (!$currentChk && count($alphabeticalNavList)) {
                $alphabeticalNavList[0]['current'] = TRUE;
            }
        }
        $list['alphNavList'] = (isset($alphabeticalNavList) && count($alphabeticalNavList) ? $alphabeticalNavList : FALSE);
        //EOF workaround for alphabetical listing
        
        //get access infos for the legen
        $list['access_infos'] = $this->getAccessInfos($xml_fachgebiet_db);

        if ($sort == 'access') {
            $list['groups'] = &$list['access_infos'];
            //BOF workaround for alphabetical listing
        } elseif ($fachgebiet == 'all') {
            if (isset($xml_fachgebiet_db->list_dbs->dbs)) {
                foreach ($xml_fachgebiet_db->list_dbs->dbs as $value) {
                    $id = (string) $value->attributes()->char;
                    $title = (string) $value->attributes()->char;
                    $list['groups'][$id] = array(
                        'id' => $id,
                        'title' => $title,
                        'dbs' => array()
                    );
                }
            }
            //EOF workaround for alphabetical listing
        } else {
            if (isset($xml_fachgebiet_db->list_dbs->db_type_infos->db_type_info)) {
                foreach ($xml_fachgebiet_db->list_dbs->db_type_infos->db_type_info as $value) {
                    $id = (string) $value->attributes()->db_type_id;
                    $title = (string) $value->db_type;
                    $list['groups'][$id] = array(
                        'id' => $id,
                        'title' => $title,
                        'dbs' => array()
                    );
                }
            }
        }

        if (isset($xml_fachgebiet_db->list_dbs->dbs)) {
            foreach ($xml_fachgebiet_db->list_dbs->dbs as $dbs) {

                foreach ($dbs->db as $value) {

                    if( $accessFilter === FALSE ){

                        $db = array(
                            'id' => (int) $value['title_id'],
                            'title' => (string) $value,
                            'access_ref' => (string) $value['access_ref'],
                            'access' => $list['access_infos'][(string) $value['access_ref']]['title'],
                            'db_type_refs' => (string) $value['db_type_refs'],
                            'top_db' => (int) $value['top_db'],
                            'link' => $this->db_detail_url . $this->bibID .'&lett='. $this->lett .'&titel_id='. $value['title_id'],
                        );

                        if ($db['top_db']) {
                            $list['top'][] = $db;
                            //BOF workaround for alphabetical listing
                        } elseif ($fachgebiet == 'all') {
                            $list['groups'][(string) $dbs->attributes()->char]['dbs'][] = $db;
                            $sortlist[$db['access']] = $db['access_ref'];
                            //EOF workaround for alphabetical listing
                        } else {
                            if ($sort == 'alph') {
                                $list['groups']['Treffer']['dbs'][] = $db;
                                $sortlist['Treffer'] = $db['Treffer'];
                            } elseif ($sort == 'access') {
                                $list['access_infos'][$db['access_ref']]['dbs'][] = $db;
                                $sortlist[$db['access']] = $db['access_ref'];
                            } else {
                                foreach (explode(' ', $db['db_type_refs']) as $ref) {
                                    $list['groups'][$ref]['dbs'][] = $db;
                                    $sortlist[$db['access']] = $db['access_ref'];
                                }
                            }
                        }
                    }else{//filter dbs

                        if( (string) $value['access_ref'] == 'access_'.$accessFilter){

                            $db = array(
                                'id' => (int) $value['title_id'],
                                'title' => (string) $value,
                                'access_ref' => (string) $value['access_ref'],
                                'access' => $list['access_infos'][(string) $value['access_ref']]['title'],
                                'db_type_refs' => (string) $value['db_type_refs'],
                                'top_db' => (int) $value['top_db'],
                                'link' => $this->db_detail_url . $this->bibID .'&lett='. $this->lett .'&titel_id='. $value['title_id'],
                            );

                            if ($db['top_db']) {
                                $list['top'][] = $db;
                                //BOF workaround for alphabetical listing
                            } elseif ($fachgebiet == 'all') {
                                $list['groups'][(string) $dbs->attributes()->char]['dbs'][] = $db;
                                $sortlist[$db['access']] = $db['access_ref'];
                                //EOF workaround for alphabetical listing
                            } else {
                                if ($sort == 'alph') {
                                    $list['groups']['Treffer']['dbs'][] = $db;
                                    $sortlist['Treffer'] = $db['Treffer'];
                                } elseif ($sort == 'access') {
                                    $list['access_infos'][$db['access_ref']]['dbs'][] = $db;
                                    $sortlist[$db['access']] = $db['access_ref'];
                                } else {
                                    foreach (explode(' ', $db['db_type_refs']) as $ref) {
                                        $list['groups'][$ref]['dbs'][] = $db;
                                        $sortlist[$db['access']] = $db['access_ref'];
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        if (!empty($sortlist) && ($sort == 'access')) {
            natsort($sortlist);
            foreach ($sortlist as $value => $key) {
                $list['alphasort'][$value] = $key;
            }
        }

        $list['alphasort'] = $sortlist;

        return array('groups' => $access_infos, 'list' => $list, 'headline' =>$headline);
    }

    /**
     * get detail information
     *
     * @param integer id of the entry
     *
     * @return array
     */
    public function getDbDetails($db_id) {

        $details = array();

        $params = array(
                    'lett' => $this->lett, 
                    'colors' => $this->colors,
                    'ocolors' => $this->ocolors,
                    'titel_id' => $db_id
                );

        $xml_db_details = $this->setRequest($this->db_detail_url, $params);

        //@todo error message
        if (!isset($xml_db_details->details)) {
            return FALSE;
        }

        //library
        $details['library']['bib_id'] = (string) $xml_db_details->library->attributes()->bib_id;
        $details['library']['name'] = (string) $xml_db_details->library;

        //details
        if (count($xml_db_details->details->children()) > 0){
            $details['titel_id'] = $db_id;

            foreach ($xml_db_details->details->children() as $key => $value) {

                if ($key == 'titles') {
                    $details['else_titles'] = array();
                    foreach ($value->children() as $key2 => $value2) {
                        if (((string) $value2->attributes()->main) == 'Y') {
                            $details['title'] = (string) $value2;
                        } else {
                            $details['else_titles'][] = (string) $value2;
                        }
                    }
                } else if ($key == 'db_access_info') {
                    $details['access_id'] = (string) $value->attributes()->access_id;
                    $details['access_icon'] = (string) $value->attributes()->access_icon;
                    $details['db_access'] = (string) $value->db_access;
                    $details['db_access_short_text'] = (string) $value->db_access_short_text;

                //check has library access and who else
                } else if ($key == 'biblist') {

                    //libraries with access to the title
                    foreach ($value->children() as $key2 => $value2) {
                        $details['biblist'] []= array(
                                'bibname' => (string) $value2,
                                'bib_id' => (string) $value2->attributes()->bib_id
                            );
                    }

                } else if ($key == 'accesses') {

                    //accesses
                    foreach ($value->access as $access) {

                        $main = (string) $access->attributes()->main;
                        $type = (string) $access->attributes()->type;
                        $href = (string) $access->attributes()->href;

                        if ($main == 'Y') {
                            //main link to start the research
                            $details['access'] = array(
                                'type' => $type,
                                'href' => $href
                            );
                        } else {
                            //other links to start resarch
                            $details['access_lic'][$type][] = array(
                                'name' => (string) $access,
                                'href' => $href
                            );
                        }
                    }
                } else if ($key == 'subjects') {
                    foreach ($value->children() as $key2 => $value2) {
                        $details['subjects'][] = (string) $value2;
                    }
                } else if ($key == 'keywords') {
                    foreach ($value->children() as $key2 => $value2) {
                        $details['keywords'][] = (string) $value2;
                    }
                    $details['keywords_join'] = join(', ', $details['keywords']);
                } else if ($key == 'db_type_infos') {
                    $i=0;
                    foreach ($value->children() as $value2) {
                        $details['db_type_infos'][$i]['type'] = (string) $value2->db_type;
                        $details['db_type_infos'][$i]['long_text'] = (string) $value2->db_type_long_text;
                        $i++;
                    }
                    //$details['db_type_infos_join'] = join(', ', $details['db_type_infos']);
                } else if ($key == 'hints') {
                    //warpto link must be completet, because ist is relative
                    $hint = preg_replace('/warpto/', 'http://rzblx10.uni-regensburg.de/dbinfo/warpto',  (string) $value);
                    $details['hints'] =  $hint;
                } else if ($key == 'instruction') {
                    $details['instruction'] = (string) $value;
                } else if ($key == 'isbn') {
                    $details['isbn'] = (string) $value;
                }

                //copy all left values into array
                else {
                    $details[(string)$key] = (string) $value;
                }
            }
        }

        //$url = 'http://rzblx10.uni-regensburg.de/dbinfo/detail.php?bib_id='.$this->bibID.'&colors=&ocolors=&lett=fs&tid=0&titel_id='. $db_id;

        //$details['more_internet_accesses'] = $this->HttpPageConnection->getDataFromHttpPage($url);
        //$details['moreDetails'] = $this->getMoreDetails($db_id);

        return $details;
    }

    /**
     * returns extended search form
     *
     * @return array
     */
    public function getExtendedForm() {
        $params = array(
                    'colors' => $this->colors,
                    'ocolors' => $this->ocolors
                );

        $xml_search_form = $this->setRequest($this->db_detail_suche_url, $params);
        
        //get access list
        if (isset($xml_search_form->dbis_search->option_list)) {
            foreach ($xml_search_form->dbis_search->option_list AS $key => $value) {
                foreach ($value->option AS $key2 => $value2) {
                    $form[(string) $value->attributes()->name][(string) $value2->attributes()->value] = (string) $value2;
                }
            }
        }

        //delete inadvertent accesses
        $this->setLicenceForbid();
        if((!empty($this->licenceForbid)) && ($this->licenceForbid!= FALSE)){
            foreach($this->licenceForbid as $key =>$licence){
                unset($form['zugaenge'][$key]);
            }
        }

        return $form;
    }

    /**
     * returns search url
     *
     * @param array $searchVars
     * @param string $lett
     *
     * @retun array
     */
    private function createSearchParams($searchVars) {
        $params = array(
                    'lett' => 'k',
                    'ocolors' => $this->ocolors,
                    'colors' => $this->colors
                  );

        foreach ($searchVars as $var => $values) {
            if (!is_array($values)) {
                //falls jemand kein utf-8 verwendet
                if((mb_strtolower($GLOBALS['TSFE']->metaCharset)) == 'utf-8'){
                    $values = utf8_decode($values);
                }
                $params[$var] = urlencode($values);
            } else {
                foreach ($values as $value) {
                    if((mb_strtolower($GLOBALS['TSFE']->metaCharset)) == 'utf-8'){
                        $value = utf8_decode($value);
                    }
                    //$searchUrl .= '&' . $var . '[]=' . urlencode($value);
                    $params[$var][] = urlencode($value);
                }
            }
        }

        return $params;
    }

    /**
     * execute search
     *
     * @param string $term
     * @param mixed $searchVars
     * @param string $lett
     *
     * @return array
     */
    public function search($searchVars = FALSE, $lett = 'fs') {

        $params = array(
                    'lett' => $lett, 
                    'colors' => $this->colors,
                    'ocolors' => $this->ocolors
                );
        
        if (!$searchVars || isset($searchVars['sword'])) {
            if((mb_strtolower($GLOBALS['TSFE']->metaCharset)) == 'utf-8'){
                $term = utf8_decode($searchVars['sword']);
            }else{
                $term = $searchVars['sword'];
            }

            // encode term
            $term = urlencode($term);
            
            $params['Suchwort'] = $term;
        } else {
            
            $params = $this->createSearchParams($searchVars);
        }

        $accessFilter = FALSE;
        if(isset($searchVars['zugaenge'])){
            $accessFilter = $searchVars['zugaenge'];
        }
        
        $response = $this->setRequest($this->dbliste_url, $params);

        if (!$response) {
            return FALSE;
        }

        $list = array(
            'top' => array(),
            //'groups' => array(),
            //'access_infos' => array(),
            'page_vars' => array(),
            'values' => array(),
            'db_count' => 0
        );
        $dbsid = array();

        foreach ($response->page_vars->children() AS $key => $value) {
            $page_vars[$key] = (string) $value;
        }

        //get access infos for the legend
        $list['access_infos'] = $this->getAccessInfos($response);

        if (isset($response->list_dbs->db_type_infos->db_type_info)) {
            foreach ($response->list_dbs->db_type_infos->db_type_info as $value) {
                $id = (string) $value->attributes()->db_type_id;
                $list['groups'][$id] = array(
                    'id' => $id,
                    'title' => (string) $value->db_type,
                    'dbs' => array()
                );
            }
        }

        if (isset($response->list_dbs->dbs)) {

            //get numer of results
            if(isset($response->list_dbs->dbs->attributes()->db_count)){
                $list['db_count'] = (int) $response->list_dbs->dbs->attributes()->db_count;
            }
            
            foreach ($response->list_dbs->dbs as $dbs) {

                foreach ($dbs->db as $value) {

                    if( ($accessFilter === FALSE) || ($accessFilter == "1000") ){

                        $db = array(
                            'id' => (int) $value['title_id'],
                            'title' => (string) $value,
                            'access_ref' => (string) $value['access_ref'],
                            'access' => $list['access_infos'][(string) $value['access_ref']]['title'],
                            'db_type_refs' => (string) $value['db_type_refs'],
                            'top_db' => (int) $value['top_db'],
                            'link' => $this->db_detail_url .'&xmloutput=1&'. $this->bibID .'&lett='. $this->lett .'&titel_id='. $value['title_id'],
                        );

                        if ($db['top_db']) {
                            $list['top'][] = $db;
                        }

                        $list['values'][$db['title'] . '_' . $db['id']] = $db;
                    }else{
                        if( (string) $value['access_ref'] == 'access_'.$accessFilter){
                            $db = array(
                                'id' => (int) $value['title_id'],
                                'title' => (string) $value,
                                'access_ref' => (string) $value['access_ref'],
                                'access' => $list['access_infos'][(string) $value['access_ref']]['title'],
                                'db_type_refs' => (string) $value['db_type_refs'],
                                'top_db' => (int) $value['top_db'],
                                'link' => $this->db_detail_url .'&xmloutput=1&'. $this->bibID .'&lett='. $this->lett .'&titel_id='. $value['title_id'],
                            );

                            if ($db['top_db']) {
                                $list['top'][] = $db;
                            }

                            $list['values'][$db['title'] . '_' . $db['id']] = $db;
                        }
                    }
                }
            }
        }

        //get search description
        $list['searchDescription'] = $this->getSearchDescription($response);

        if (isset($response->error)) {
            $list['error'] = (string) $response->error;
        }

        return array('page_vars' => $page_vars, 'list' => $list);
    }
    
    /**
     * returns search description
     * 
     * @param simpleXML $request
     * 
     * @return array
     */
    private function getSearchDescription($request){
        $list = array();
        
        if (isset($request->search_desc->search_desc_item)) {
            foreach ($request->search_desc->search_desc_item as $searchDesc) {
                $list[] = (string)$searchDesc;
            }
        }
        
        return $list;
    }
    
    /**
     *
     * helper function get fachliste
     *
     * @param string $request
     *
     * @return array
     */
    public function getRequestFachliste($request) {
        
        $xml_response = $this->setRequest($this->fachliste_url, $params = array());
        
        return $xml_response;
    }

    /**
     * helper function get db liste
     *
     * @param string $request
     *
     * @return array
     */
    public function getRequestDbliste($request) {
        $url = $this->dbliste_url . $this->bibID . '&' . $request;
        $xml_request = $this->XMLPageConnection->getDataFromXMLPage($url);

        return $xml_request;
    }

    /**
     * get some inforemation from the HTML page
     * @param integer
     *
     * @return array
     */
    private function getMoreDetails($db_id){
        $HttpPageConnection = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('tx_libconnect_resources_private_lib_httppageconnection');
        $url = 'https://rzblx10.uni-regensburg.de/dbinfo/detail.php?bib_id='.$this->bibID.'&colors=&ocolors=&lett=fs&tid=0&titel_id='. $db_id;
        $HttpRequestData = $HttpPageConnection->getDataFromHttpPage($url);

        $moreDetails = array();

        //detail_content_more_internet_accesses
        $start = mb_stripos($HttpRequestData, "detail_content_more_internet_accesses");
        if($start){
            $stop = mb_stripos($HttpRequestData, "</td>", $start);
            $detail_content_more_internet_accesses = trim(mb_substr($HttpRequestData, $start, $stop-$start-5));
            $detail_content_more_internet_accesses = str_replace("</td>", "", $detail_content_more_internet_accesses);
            $detail_content_more_internet_accesses = utf8_encode(str_replace("_more_internet_accesses\">", "", $detail_content_more_internet_accesses));
        }
        $moreDetails['more_internet_accesses'] = $detail_content_more_internet_accesses;

        return $moreDetails;
    }

    /**
     * get information for the access legen
     * @param SimpleXMLElement $request
     */
    private function getAccessInfos($request){
        $accessInfos = array();

        if (isset($request->list_dbs->db_access_infos->db_access_info)) {
            foreach ($request->list_dbs->db_access_infos->db_access_info as $value) {
                $id = (string) $value->attributes()->access_id;

                $accessInfos[$id] = array(
                    'id' => $id,
                    'description' => (string) $value->db_access,
                    'description_short' => (string) $value->db_access_short_text,
                );
            }
        }

        return $accessInfos;
    }

    /**
     *
     * @param type string
     * @param type array
     * @return SimpleXMLElement
     */
    private function setRequest($url, $params = array()){

        $request = NEW \Sub\Libconnect\Service\Request;
        
        $request->setUrl($url);
        $request->setQuery( array('bib_id' => $this->bibID ) );

        if(!empty($params)){
            $request->setQuery( $params );
        }

        $xml_response = $request->request();

        return $xml_response;
    }
}
?>
