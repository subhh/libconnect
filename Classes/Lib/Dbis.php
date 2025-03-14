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
 * @author niklas guenther
 * @author Torsten Witt
 */
class Dbis
{
    private $fachgebiet;
    private $colors = '';
    private $ocolors = '';
    private $lett = 'f';
    //private $fachliste_url = 'https://dbis.ur.de/dbinfo/fachliste.php';
    private $fachliste_url = 'https://dbis-test.ur.de/fachliste.php';
    //private $fachliste_url = 'https://dbis-test.ur.de/api/v1/subjects';//json
    //private $dbliste_url = 'https://dbis.ur.de/dbinfo/dbliste.php';
    //private $dbliste_url = 'https://dbis-test.ur.de/dbliste2.php';
    private $dbliste_url = 'https://dbis-test.ur.de/dbliste.php';
    //private $db_detail_url = 'https://dbis.ur.de/dbinfo/detail.php';
    private $db_detail_url = 'https://dbis-test.ur.de/detail.php';
    //private $db_detail_suche_url = 'https://dbis.ur.de/dbinfo/suche.php';
    private $db_detail_suche_url = 'https://dbis-test.ur.de/suche.php';
    private $dbis_domain = 'dbis.ur.de';

    private $lang = 'de';
    public $all;
    public $top_five_dbs;
    //variable of typoscript configuration
    private $bibID;
    private $licenceForbid = [];

    /**
     * constructor
     */
    public function __construct()
    {
        $this->setBibID();
    }

    /**
     * sets ID of the library
     */
    public function setBibID()
    {
        $this->bibID = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_libconnect.']['dbisbibid'];
    }

    /**
     * Set id Fachgebiet
     *
     * @param int $fachgebiet
     */
    public function setGebiet($fachgebiet)
    {
        $this->fachgebiet = (int)$fachgebiet;
    }

    /**
     * Set the letter string
     *
     * @param string $lett
     */
    public function setLett($lett)
    {
        $this->lett = $lett;
    }

    /**
     * Set the int value colors
     *
     * @param int $colors
     */
    public function setColors($colors)
    {
        $this->colors = $colors;
    }

    /**
     * Set the int value ocolors
     *
     * @param int $ocolors
     */
    public function setOcolors($ocolors)
    {
        $this->ocolors = $ocolors;
    }

    /**
     * Set the array with unused licences
     */
    public function setLicenceForbid()
    {
        $this->licenceForbid = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_libconnect.']['settings.']['dbislicenceforbid.'];
    }

    /**
     * red all subjects
     *
     * @return array()
     */
    public function getFachliste()
    {
        //json
        /*$response = $this->setRequest($this->fachliste_url, array('lang' => $this->lang));

        $return = array();
        
        if (!empty($response)) {
            foreach ($response as $key => $value) {
                $return['list_subjects_collections'][] = array(
                    'id' => $value['id'],
                    'title' => $value['title'],
                    'subject_system' => $value['subject_system'],
                    'parent' => $value['parent'],
                    'is_collection' => $value['is_collection'],
                    'lett' => ($value['is_collection'] == TRUE) ? 'c' : 'f'
                );
            }
        }

        return $return;*/
        
        $response = $this->setRequest($this->fachliste_url, array('colors' => 511, 'ocolors' => 40, 'lett' => 1));

        $return = array();

        foreach($response->page_vars as $key => $value){
            $regurn['pagaVars'][$key] = $value;
        }

        $return['headline'] = (string)$response->headline;

        if (!empty($response->list_subjects_collections->list_subjects_collections_item)) {
            foreach ($response->list_subjects_collections->list_subjects_collections_item as $key => $value) {
                 $return['list_subjects_collections'][] = array(
                    'title' => (string)$value,
                    'notation' => (string)$value['notation'],
                    'number' => (int)(string)$value['number'],
                    'lett' => (string)$value['lett']
                 );
             }
        }
        
        return $return;
    }

    public function getDbList($fachgebiet, $sort){
        $params = [
                    'colors' => $this->colors,
                    'ocolors' => $this->ocolors,
                    'sort' => $sort
                ];

        $headline = '';

        //BOF workaround for alphabetical listing
        if ($fachgebiet == 'all') {
            $params['lett'] = 'a';

            if (!empty($tmpParams['lc'])) {
                if (!empty($tmpParams['lc'][0])) {
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
        
        $xml = $this->setRequest($this->dbliste_url, $params);

        $list = [
            'top' => [],
            'groups' => [],
            'access_infos' => [],
        ];

        //headline - content of the list
        if (isset($xml->headline)) {
            $list['headline'] = $xml->headline;
        }
        
        //acces_infos
        if (isset($xml->list_dbs->db_access_infos)) {
            $list['access_infos'] = $this->getAccessInfos($xml->list_dbs->db_access_infos);
        }
        
        //dbTypeInfos
        if (isset($xml->list_dbs->db_type_infos)) {
            $list['db_type_infos'] = $this->getdbTypeInfos($xml->list_dbs->db_type_infos);
        }

        $list['dbs']['list'] = array();

        //databases
        foreach($xml->list_dbs->dbs as $db){
            //top_dbs
            if( isset($db->attributes()->top_db) ) {
                $list['top']['list'] = $this->getTopDatabases($db);
            }else {//found dbs
                //sort = alph
                if($db->attributes()->sort == "alph") {
                    $list['dbs']['list'] = $this->getDatabases($db);
                } else { //sort = type
                    $list['dbs']['list'][(string)$db->attributes()->db_type_ref] = $this->getDatabases($db);
                }
            }
        }
        
        
        return $list;
    }
    
    private function getResult($xml){
        $list = [
            'top' => [],
            'groups' => [],
            'access_infos' => [],
        ];

        //headline - content of the list
        if (isset($xml->headline)) {
            $list['headline'] = $xml->headline;
        }
        
        //acces_infos
        if (isset($xml->list_dbs->db_access_infos)) {
            $list['access_infos'] = $this->getAccessInfos($xml->list_dbs->db_access_infos);
        }
        
        //dbTypeInfos
        if (isset($xml->list_dbs->db_type_infos)) {
            $list['db_type_infos'] = $this->getdbTypeInfos($xml->list_dbs->db_type_infos);
        }

        //databases
        foreach($xml->list_dbs->dbs as $db){
            //top_dbs
            if( isset($db->attributes()->top_db) ) {
                $list['top']['list'] = $this->getTopDatabases($db);
                $list['top']['db_count'] = (integer)$db->attributes()->db_count;
            }else {//found dbs
                $list['dbs']['list'] = $this->getDatabases($db);
                $list['dbs']['db_count'] = (integer)$db->attributes()->db_count;
            }
        }
        
        return $list;
    }

    private function getdbTypeInfos($dbTypeInfos){
        $return = array();

        foreach ($dbTypeInfos->children() as $db) {      
            $return[] = array(
                'db_type_id' => (string)$db->attributes()->db_type_id,
                'db_type'  => (string)$db->db_type,
                'db_type_long_text'  => (string)$db->db_type_long_text
            );
            
        }

        return $return;
    }
    
    private function getDBTypeRefsArray($db_type_refs){
        return ;
    }
    
    /*
        returns a list with dbs in their type
    */
    private function getDbtypeDbs($dbs){
        $list = array();
        
        foreach($dbs as $db) {
            $list['db_type_refs '][$db['titleid ']] = $db;
        }
        
        return $list;
    }
    
    private function getTopDatabases($topDatabases){
        $return = array();

        foreach($topDatabases->children() as $db){
            $return[] = array(
                'titleid' => (string)$db->attributes()->title_id,
                'title' => (string)$db,
                'access_ref' => (string)$db->attributes()->access_ref,
                'db_type_refs' => explode(" ", (string)$db->attributes()->db_type_refs),
                'href' => (string)$db->attributes()->href,
                'top_db' => (string)$db->attributes()->top_db,
            );
        }

        return $return;
    }

    private function getDatabases($databases){
        $return = array();
        
        foreach($databases as $key => $value){
            $return[ (string)$value ] = array(
                'titleid' => (string)$value->attributes()->title_id,
                'title' => (string)$value,
                'access_ref' => (string)$value->attributes()->access_ref == ('no mapping to old access_id found' or 'not_found') ? (string)$value->attributes()->traffic_light : (string)$value->attributes()->access_ref,
                'db_type_refs' => explode(" ", (string)$value->attributes()->db_type_refs),
                'href' => (string)$value->attributes()->href,
                'traffic_light' => (string)$value->attributes()->traffic_light,
            );
        }            
        
        ksort($return, SORT_STRING);
        
        return $return;
    }
    
    private function getAccessInfos($accessInfos){
        $return = array();
        
        foreach($accessInfos->db_access_info  as $key => $value){
                $access_id = (string)$value->attributes()->access_id;

                $return[$access_id] = [
                    'access_id' => (string)$value->attributes()->access_id,
                    'db_access' => (string)$value->db_access,
                    'db_access_short_text' => (string)$value->db_access_short_text,
                ];
        }

        return $return;
    }

    private function getAlphabeticalListing($list){
        $result = array();
        
        foreach ($list as $key => $value){
            $result[ $value['title'] ] = $value;
        }
    }

    /**
     * returns entries of a subject
     *
     * @param int $fachgebiet
     * @param string $sort
     *
     * @return array
     */
    public function getDbliste($fachgebiet, $sort = 'type', $accessFilter = false, $tmpParams = array())
    {
        $sortlist = [];

        $params = [
                    'colors' => $this->colors,
                    'ocolors' => $this->ocolors,
                    'sort' => $sort
                ];

        $headline = '';

        //BOF workaround for alphabetical listing
        if ($fachgebiet == 'all') {
            $params['lett'] = 'a';

            if (!empty($tmpParams['lc'])) {
                if (!empty($tmpParams['lc'][0])) {
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

        $list = [
            'top' => [],
            'groups' => [],
            'access_infos' => []
        ];

        //name of own collection
        if (isset($xml_fachgebiet_db->headline)) {
            $headline = $xml_fachgebiet_db->headline;
        }

        //BOF workaround for alphabetical listing
        if (is_object($xml_fachgebiet_db->list_dbs->alphabetical_list)) {
            $alphabeticalNavList = [];
            if (isset($xml_fachgebiet_db->list_dbs->alphabetical_list->block_of_chars)) {
                foreach ($xml_fachgebiet_db->list_dbs->alphabetical_list->block_of_chars as $charBlock) {
                    $tmpCharArray = [];
                    foreach ($charBlock->char as $char) {
                        $tmpCharArray[] = $char;
                    }
                    $alphabeticalNavList[] = [
                        'chars' => $tmpCharArray,
                        'fc' => (string)$charBlock->attributes()->fc,
                        'lc' => (string)$charBlock->attributes()->lc,
                        //check current view for which char is shown
                        'current' => ($charBlock->attributes()->lc == $tmpParams['lc'] || $charBlock->attributes()->fc == $tmpParams['lc'] ? true : false)
                    ];
                }
            }
            //check if a current view got set
            // if not, set the first charBlock as current view
            $currentChk = false;
            foreach ($alphabeticalNavList as $value) {
                if ($value['current']) {
                    $currentChk = true;
                    break;
                }
            }
            if (!$currentChk && count($alphabeticalNavList)) {
                $alphabeticalNavList[0]['current'] = true;
            }
        }
        $list['alphNavList'] = (isset($alphabeticalNavList) && count($alphabeticalNavList) ? $alphabeticalNavList : false);
        //EOF workaround for alphabetical listing

        //get access infos for the legen
        $list['access_infos'] = $this->getAccessInfos($xml_fachgebiet_db);

        if ($sort == 'access') {
            $list['groups'] = &$list['access_infos'];
        //BOF workaround for alphabetical listing
        } elseif ($fachgebiet == 'all') {
            if (isset($xml_fachgebiet_db->list_dbs->dbs)) {
                foreach ($xml_fachgebiet_db->list_dbs->dbs as $value) {
                    $id = (string)$value->attributes()->char;
                    $title = (string)$value->attributes()->char;
                    $list['groups'][$id] = [
                        'id' => $id,
                        'title' => $title,
                        'dbs' => []
                    ];
                }
            }
            //EOF workaround for alphabetical listing
        } else {
            if (isset($xml_fachgebiet_db->list_dbs->db_type_infos->db_type_info)) {
                foreach ($xml_fachgebiet_db->list_dbs->db_type_infos->db_type_info as $value) {
                    $id = (string)$value->attributes()->db_type_id;
                    $title = (string)$value->db_type;
                    $list['groups'][$id] = [
                        'id' => $id,
                        'title' => $title,
                        'dbs' => []
                    ];
                }
            }
        }

        if (isset($xml_fachgebiet_db->list_dbs->dbs)) {
            foreach ($xml_fachgebiet_db->list_dbs->dbs as $dbs) {
                foreach ($dbs->db as $value) {
                    if ($accessFilter === false) {
                        $db = [
                            'id' => (int)$value['title_id'],
                            'title' => (string)$value,
                            'access_ref' => (string)$value['access_ref'],
                            //'access' => $list['access_infos'][(string)$value['access_ref']]['title'],
                            'db_type_refs' => (string)$value['db_type_refs'],
                            'top_db' => (int)$value['top_db'],
                            'link' => $this->db_detail_url . $this->bibID . '&lett=' . $this->lett . '&titel_id=' . $value['title_id'],
                        ];

                        if ($db['top_db']) {
                            $list['top'][] = $db;
                        //BOF workaround for alphabetical listing
                        } elseif ($fachgebiet == 'all') {
                            $list['groups'][(string)$dbs->attributes()->char]['dbs'][] = $db;
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
                                    //$sortlist[$db['access']] = $db['access_ref'];
                                }
                            }
                        }
                    } else {//filter dbs

                        if ((string)$value['access_ref'] == 'access_' . $accessFilter) {
                            $db = [
                                'id' => (int)$value['title_id'],
                                'title' => (string)$value,
                                'access_ref' => (string)$value['access_ref'],
                                'access' => $list['access_infos'][(string)$value['access_ref']]['title'],
                                'db_type_refs' => (string)$value['db_type_refs'],
                                'top_db' => (int)$value['top_db'],
                                'link' => $this->db_detail_url . $this->bibID . '&lett=' . $this->lett . '&titel_id=' . $value['title_id'],
                            ];

                            if ($db['top_db']) {
                                $list['top'][] = $db;
                            //BOF workaround for alphabetical listing
                            } elseif ($fachgebiet == 'all') {
                                $list['groups'][(string)$dbs->attributes()->char]['dbs'][] = $db;
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

        return ['groups' => $access_infos, 'list' => $list, 'headline' =>$headline];
    }

    /**
     * get detail information
     *
     * @param int id of the entry
     *
     * @return array
     */
    public function getDbDetails($db_id)
    {
        $details = [];

        $params = [
                    'lett' => $this->lett,
                    'colors' => $this->colors,
                    'ocolors' => $this->ocolors,
                    'titel_id' => $db_id
                ];

        $xml_db_details = $this->setRequest($this->db_detail_url, $params);

        //@todo error message
        if (!isset($xml_db_details->details)) {
            return false;
        }

        //library
        $details['library']['bib_id'] = (string)$xml_db_details->library->attributes()->bib_id;
        $details['library']['name'] = (string)$xml_db_details->library;

        //details
        if (count($xml_db_details->details->children()) > 0) {
            $details['titel_id'] = $db_id;

            foreach ($xml_db_details->details->children() as $key => $value) {
                if ($key == 'titles') {
                    $details['else_titles'] = [];
                    foreach ($value->children() as $key2 => $value2) {
                        if (((string)$value2->attributes()->main) == 'Y') {
                            $details['title'] = (string)$value2;
                        } else {
                            $details['else_titles'][] = (string)$value2;
                        }
                    }
                } elseif ($key == 'db_access_info') {
                    $details['access_id'] = (string)$value->attributes()->access_id;
                    $details['access_icon'] = (string)$value->attributes()->access_icon;
                    $details['db_access'] = (string)$value->db_access;
                    $details['db_access_short_text'] = (string)$value->db_access_short_text;

                //check has library access and who else
                } elseif ($key == 'biblist') {

                    //libraries with access to the title
                    foreach ($value->children() as $key2 => $value2) {
                        $details['biblist'] []= [
                                'bibname' => (string)$value2,
                                'bib_id' => (string)$value2->attributes()->bib_id
                            ];
                    }
                } elseif ($key == 'accesses') {

                    //accesses
                    foreach ($value->access as $access) {
                        $main = (string)$access->attributes()->main;
                        $type = (string)$access->attributes()->type;
                        $href = (string)$access->attributes()->href;

                        if ($main == 'Y') {
                            //main link to start the research
                            $details['access'] = [
                                'type' => $type,
                                'href' => $href
                            ];
                        } else {
                            //other links to start resarch
                            $details['access_lic'][$type][] = [
                                'name' => (string)$access,
                                'href' => $href
                            ];
                        }
                    }
                } elseif ($key == 'subjects') {
                    foreach ($value->children() as $key2 => $value2) {
                        $details['subjects'][] = (string)$value2;
                    }
                } elseif ($key == 'keywords') {
                    foreach ($value->children() as $key2 => $value2) {
                        $details['keywords'][] = (string)$value2;
                    }

                    if(!empty($details['keywords'])){
                        $details['keywords_join'] = implode(', ', $details['keywords']);
                    }
                } elseif ($key == 'db_type_infos') {
                    $i=0;
                    foreach ($value->children() as $value2) {
                        $details['db_type_infos'][$i]['type'] = (string)$value2->db_type;
                        $details['db_type_infos'][$i]['long_text'] = (string)$value2->db_type_long_text;
                        $i++;
                    }
                    //$details['db_type_infos_join'] = join(', ', $details['db_type_infos']);
                } else if ($key == 'hints') {
                    //warpto link must be completed, because it is relative
                    $hint = preg_replace('/warpto/', 'https://'.$this->dbis_domain.'/warpto', (string) $value);

                    $details['hints'] =  $hint;
                } elseif ($key == 'instruction') {
                    $details['instruction'] = (string)$value;
                } elseif ($key == 'isbn') {
                    $details['isbn'] = (string)$value;
                }

                //copy all left values into array
                else {
                    $details[(string)$key] = (string)$value;
                }
            }
        }

        //$details['moreDetails'] = $this->getMoreDetails($db_id);

        return $details;
    }

    /**
     * returns extended search form
     *
     * @return array
     */
    public function getExtendedForm()
    {
        $params = [
                    'colors' => $this->colors,
                    'ocolors' => $this->ocolors
                ];

        $xml_search_form = $this->setRequest($this->db_detail_suche_url, $params);

        //get access list
        if (isset($xml_search_form->dbis_search->option_list)) {
            foreach ($xml_search_form->dbis_search->option_list as $key => $value) {
                foreach ($value->option as $key2 => $value2) {
                    $form[(string)$value->attributes()->name][(string)$value2->attributes()->value] = (string)$value2;
                }
            }
        }

        //delete inadvertent accesses
        //$this->setLicenceForbid();
        if ((!empty($this->licenceForbid)) && ($this->licenceForbid!= false)) {
            foreach ($this->licenceForbid as $key =>$licence) {
                unset($form['zugaenge'][$key]);
            }
        }

        return $form;
    }

    /**
     * returns search url
     *
     * @param array $searchVars
     *
     * @return array
     */
    private function createSearchParams($searchVars)
    {
        $params = [
                    'lett' => 'k',
                    'ocolors' => $this->ocolors,
                    'colors' => $this->colors
                  ];

        foreach ($searchVars as $var => $values) {
            if (!is_array($values)) {
                //DBIS needs ISO-8859-1
                $values = mb_convert_encoding($values, "ISO-8859-1");

                $params[$var] = urlencode($values);
            } else {
                foreach ($values as $value) {
                    //DBIS needs ISO-8859-1
                    $value = mb_convert_encoding($value, "ISO-8859-1");

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
    public function search($searchVars = false, $lett = 'fs')
    {
        $params = [
                    'lett' => $lett,
                    'colors' => $this->colors,
                    'ocolors' => $this->ocolors
                ];

        if (!$searchVars || isset($searchVars['sword'])) {
            //DBIS needs ISO-8859-1
            $term = mb_convert_encoding($searchVars['sword'], "ISO-8859-1");

            // encode term
            $term = urlencode($term);

            $params['Suchwort'] = $term;
        } else {
            $params = $this->createSearchParams($searchVars);
        }

        $accessFilter = false;
        if (isset($searchVars['zugaenge'])) {
            $accessFilter = $searchVars['zugaenge'];
        }

        $response = $this->setRequest($this->dbliste_url, $params);
        
        $list = $this->getResult($response);
        /*
        
        if (!$response) {
            return false;
        }

        $list = [
            'top' => [],
            //'groups' => array(),
            //'access_infos' => array(),
            'page_vars' => [],
            'values' => [],
            'db_count' => 0
        ];
        $dbsid = [];

        foreach ($response->page_vars->children() as $key => $value) {
            $page_vars[$key] = (string)$value;
        }

        //get access infos for the legend
        $list['access_infos'] = $this->getAccessInfos($response);

        if (isset($response->list_dbs->db_access_infos->db_type_info)) {
            foreach ($response->list_dbs->db_type_infos->db_type_info as $value) {
                $id = (string)$value->attributes()->db_type_id;
                $list['groups'][$id] = [
                    'id' => $id,
                    'title' => (string)$value->db_type,
                    'dbs' => []
                ];
            }
        }

        if (isset($response->list_dbs->dbs)) {

            //get numer of results
            if (isset($response->list_dbs->dbs->attributes()->db_count)) {
                $list['db_count'] = (int)$response->list_dbs->dbs->attributes()->db_count;
            }

            foreach ($response->list_dbs->dbs as $dbs) {
                foreach ($dbs->db as $value) {
                    if (($accessFilter === false) || ($accessFilter == '1000')) {
                        $db = [
                            'id' => (int)$value['title_id'],
                            'title' => (string)$value,
                            'access_ref' => (string)$value['access_ref'],
                            'access' => $list['access_infos'][(string)$value['access_ref']]['title'],
                            'db_type_refs' => (string)$value['db_type_refs'],
                            'top_db' => (int)$value['top_db'],
                            'link' => $this->db_detail_url . '&xmloutput=1&' . $this->bibID . '&lett=' . $this->lett . '&titel_id=' . $value['title_id'],
                        ];

                        if ($db['top_db']) {
                            $list['top'][] = $db;
                        }

                        $list['values'][$db['title'] . '_' . $db['id']] = $db;
                    } else {
                        if ((string)$value['access_ref'] == 'access_' . $accessFilter) {
                            $db = [
                                'id' => (int)$value['title_id'],
                                'title' => (string)$value,
                                'access_ref' => (string)$value['access_ref'],
                                'access' => $list['access_infos'][(string)$value['access_ref']]['title'],
                                'db_type_refs' => (string)$value['db_type_refs'],
                                'top_db' => (int)$value['top_db'],
                                'link' => $this->db_detail_url . '&xmloutput=1&' . $this->bibID . '&lett=' . $this->lett . '&titel_id=' . $value['title_id'],
                            ];

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
            $list['error'] = (string)$response->error;
        }

        return ['page_vars' => $page_vars, 'list' => $list];*/
        
        return $list;
    }

    /**
     * returns search description
     *
     * @param simpleXML $request
     *
     * @return array
     */
    private function getSearchDescription($request)
    {
        $list = [];

        if (isset($request->search_desc->search_desc_item)) {
            foreach ($request->search_desc->search_desc_item as $searchDesc) {
                $list[] = (string)$searchDesc;
            }
        }

        return $list;
    }

    /**
     * helper function get db liste
     *
     * @param string $request
     *
     * @return array
     */
    public function getRequestDbliste($request)
    {
        $url = $this->dbliste_url . $this->bibID . '&' . $request;
        $xml_request = $this->XMLPageConnection->getDataFromXMLPage($url);

        return $xml_request;
    }

    /**
     * get some inforemation from the HTML page
     * @param int
     *
     * @return array
     */
    private function getMoreDetails($db_id)
    {
        $params = ['bib_id' => $this->bibID, 'colors' =>'', 'ocolors' => '', 'lett' => 'fs', 'tid' => 0, 'titel_id' => $db_id, 'xmloutput' => 0];

        $htmlResponse = $this->setRequest('https://'.$this->dbis_domain.'/detail.php', $params);

        $moreDetails = [];

        //detail_content_more_internet_accesses
        $start = mb_stripos($htmlResponse, 'detail_content_more_internet_accesses');
        if ($start) {
            $stop = mb_stripos($htmlResponse, '</td>', $start);
            $detail_content_more_internet_accesses = trim(mb_substr($htmlResponse, $start, $stop-$start-5));
            $detail_content_more_internet_accesses = str_replace('</td>', '', $detail_content_more_internet_accesses);

            $detail_content_more_internet_accesses =  mb_convert_encoding(str_replace('_more_internet_accesses">', '', $detail_content_more_internet_accesses), "ISO-8859-1");
        }
        $moreDetails['more_internet_accesses'] = $detail_content_more_internet_accesses;

        return $moreDetails;
    }

    /**
     * get information for the access
     * @param SimpleXMLElement $request
     */
    private function getAccessInfos_old($request)
    {
        $accessInfos = [];

        if (isset($request->list_dbs->db_access_infos->db_access_info)) {
            foreach ($request->list_dbs->db_access_infos->db_access_info as $value) {
                $id = (string)$value->attributes()->access_id;

                $accessInfos[$id] = [
                    'id' => $id,
                    'description' => (string)$value->db_access,
                    'description_short' => (string)$value->db_access_short_text,
                ];
            }
        }

        return $accessInfos;
    }

    /**
     * sets language
     * @param string lang
     */
    public function setLanguage($lang)
    {
        //only de and en is allowed
        if (($lang != 'de') && ($lang != 'en')) {
            $lang = 'de';
        }

        $this->lang = $lang;
    }

    /**
     * returns language
     *
     * @return string
     */
    public function getLanguage()
    {
        return $this->lang;
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
        $request->setQuery(['bib_id' => $this->bibID, 'xmloutput' => 1 ]);

        if (!empty($params)) {
            $request->setQuery($params);
        }

        $xml_response = $request->request();

        return $xml_response;
    }
}
