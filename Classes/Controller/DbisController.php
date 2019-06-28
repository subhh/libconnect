<?php
namespace Sub\Libconnect\Controller;
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

class DbisController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {
    
    /**
	 * creates instance of DbisRepository
	 *
	 * @var \Sub\Libconnect\Domain\Repository\DbisRepository
	 * @inject
	 */
	protected $dbisRepository;
    
    /**
     * shows top databases
     */
    public function displayTopAction() {
        $config['subject'] = $this->settings['flexform']['subject'];
        $config['detailPid'] = $this->settings['flexform']['detailPid'];

        $top =  $this->dbisRepository->loadTop($config);

        //variables for template
        $this->view->assign('top', $top);
    }

    /**
     * shows a list of databases (for general, search, choosed subject)
     */
    public function displayListAction() {
        $params = array();
        if(!empty(\TYPO3\CMS\Core\Utility\GeneralUtility::_GPmerged('tx_libconnect_dbis'))){
            $params_temp = \TYPO3\CMS\Core\Utility\GeneralUtility::_GPmerged('tx_libconnect_dbis');
            $params = $params_temp['libconnect'];
        } 
        if(!empty(\TYPO3\CMS\Core\Utility\GeneralUtility::_GPmerged('libconnect'))){
            $params_temp = \TYPO3\CMS\Core\Utility\GeneralUtility::_GPmerged('libconnect');
            if(!empty($params)){
                $params = array_merge($params_temp, $params);
            }else{
                $params = $params_temp;
            }
        }

        //show overview on empty search
        $isSearch = FALSE;
        if (!empty($params['search'])){
            if( ( count( $params['search'] ) > 1 ) || (!empty($params['search']['sword'] )) ){
                $isSearch = TRUE;
            }
        }

        if (!empty($params['subject'])) {//choosed subject after start point
            $config['sort'] = $this->settings['flexform']['sortParameter'];
            $config['detailPid'] = $this->settings['flexform']['detailPid'];

            //user sorted list
            if(isset($params['sort']) && !empty($params['sort'])) {
                $config['sort'] = $params['sort'];
            }
            //set default sorting
            if(empty($config['sort'])){
                $config['sort'] = 'type';
            }

            if(isset($params['search']['zugaenge']) && ( $params['search']['zugaenge'] != '1000' ) ){
                $config['search']['zugaenge']=$params['search']['zugaenge'];
                
                //it is not possible to filter "zugaenge" if sort is set to access
                if( $config['sort'] == 'access'){
                    $config['sort'] = 'type';
                }
            }

            $list =  $this->dbisRepository->loadList($params['subject'], $config);

            //check, if there are no results and inform user to change licence
            $empty = TRUE;
            if(!empty($list['list']['alphNavList'])){
                $empty = FALSE;
            }
            foreach($list['list']['groups'] as $group){

                if(!empty($group['dbs'])){
                    $empty = FALSE;
                    break;
                }
            }
            $this->view->assign('empty', $empty);

            //decide full or short text
            $list['list']['access_infos'] = $this->setAccessInformation($list['list']['access_infos']);

            //variables for template
            $this->view->assign('listhead', $list['subject']);
            $this->view->assign('subject', $params['subject']);
            $this->view->assign('zugaenge', $params['search']['zugaenge']);
            $this->view->assign('list', $list['list']);

        } else if ($isSearch !== FALSE) {//search results
            $config['detailPid'] = $this->settings['flexform']['detailPid'];

            $list =  $this->dbisRepository->loadSearch($params['search'], $config);

            //change view
            $controllerContext = $this->buildControllerContext();
            $controllerContext->getRequest()->setControllerActionName('displaySearch');
            $this->view->setControllerContext($controllerContext);

            //decide full or short text
            $list['access_infos'] = $this->setAccessInformation($list['access_infos']);
            
            //variables for template
            $this->view->assign('list', $list);

        } else {//start point
            $list =  $this->dbisRepository->loadOverview();

            //use other view
            $controllerContext = $this->buildControllerContext();
            $controllerContext->getRequest()->setControllerActionName('displayOverview');
            $this->view->setControllerContext($controllerContext);

            //variables for template
            $this->view->assign('list', $list);
        }
    }

    /**
     * shows deatail view
     */
    public function displayDetailAction() {
        if(!empty(\TYPO3\CMS\Core\Utility\GeneralUtility::_GPmerged('tx_libconnect_dbis'))){
            $params = \TYPO3\CMS\Core\Utility\GeneralUtility::_GPmerged('tx_libconnect_dbis');
        } else{
            $params = \TYPO3\CMS\Core\Utility\GeneralUtility::_GPmerged('libconnect');
        };

        if (!($params['titleid'])){
            //variables for template
            $this->view->assign('error', 'Error');

            return;
        }
        $list =  $this->dbisRepository->loadDetail($params['titleid']);

        //repair broken HTML
        $UserFunc = new \Sub\Libconnect\UserFunctions\RepairHTMLUserFunction();
        $list = $UserFunc->RepairHTMLUserFunction($list);

        if(!$list){
            //variables for template
            $this->view->assign('error', 'Error');

        }else{
            //BG> Hide start research link for internal access only items
            if($list['access_id']!='access_4'){
                $list['access_workaround']=$list['access_id'];
            }
            //variables for template
            $this->view->assign('db', $list);
        }        
    }

    /**
     * shows sidebar
     */
    public function displayMiniFormAction() {
        $params = array();
        if(!empty(\TYPO3\CMS\Core\Utility\GeneralUtility::_GPmerged('tx_libconnect_dbis'))){
            $params_temp = \TYPO3\CMS\Core\Utility\GeneralUtility::_GPmerged('tx_libconnect_dbis');
            $params = $params_temp['libconnect'];
        }
        if(!empty(\TYPO3\CMS\Core\Utility\GeneralUtility::_GPmerged('libconnect'))){
            $params_temp = \TYPO3\CMS\Core\Utility\GeneralUtility::_GPmerged('libconnect');
            if(!empty($params)){
                $params = array_merge($params_temp, $params);
            }else{
                $params = $params_temp;
            }
        }

        $form = $this->dbisRepository->loadMiniForm();

        //variables for template
        $this->view->assign('form', $form);
        $this->view->assign('siteUrl', $GLOBALS['TSFE']->cObj->getTypolink_URL($GLOBALS['TSFE']->id));//active URL
        $this->view->assign('searchUrl', $GLOBALS['TSFE']->cObj->getTypolink_URL($this->settings['flexform']['searchPid']));//link to search page
        $this->view->assign('listUrl', $GLOBALS['TSFE']->cObj->getTypolink_URL($this->settings['flexform']['listPid']));//link to list page
        $this->view->assign('listPid', $this->settings['flexform']['listPid']);//id of page with list
        $this->view->assign('vars', $params['search']);
        //hide selectbox for licence/access if search and sort alph
        if ( isset($params['sort']) && isset($params['subject'])) {
            if( ($params['sort'] == 'alph') && ($params['subject'] == 'all')){
                $this->view->assign('hideAccess', TRUE);
            }
        }
        if ( isset($params['subject'])) {
            if( ($params['subject'] == 'all')){
                $this->view->assign('hideAccess', TRUE);
            }
        }
        //if no databases are listed, no filter is displayed
        if(empty($params)){
            $this->view->assign('hideAccess', TRUE);
        }
        //Set stubject
        if(!empty($params['subject'])) {
            $this->view->assign('subject', $params['subject']);
        }
        //sort, if default was changed
        if(!empty($params['sort'])) {
            $this->view->assign('sort', $params['sort']);
        }
        //dbis-listings-wrapper
        if(!empty($params['subject'])) {
            if($params['subject'] != 'all'){
                $this->view->assign('listingsWrapper', true);
            }
        }
        
        //links to the plugin "new in dbis"
        if(!empty($this->settings['flexform']['newPid'])){
            //new entries for a selected subject
            if( !empty($params['subject']) && ($params['subject'] != 'all') ) {
                $subject = $this->dbisRepository->getSubject($params['subject']);
                $count = (int) $this->getNewCount($subject['dbisid']);

                if($count >0){
                    $this->view->assign('newUrlSub', $GLOBALS['TSFE']->cObj->getTypolink_URL( intval($this->settings['flexform']['newPid']), 
                        array('libconnect' => array('subject' => $params['subject'] )) ) );//URL der New-Darstellung

                    $this->view->assign('newInSubjectCount',  $count);
                }
            }

            //new entries for all subjects
            $count = (int) $this->getNewCount(FALSE);

            if($count >0){
                $this->view->assign('newUrl', $GLOBALS['TSFE']->cObj->getTypolink_URL( intval($this->settings['flexform']['newPid'])) );
                $this->view->assign('newCount',  $count);
            }   
        }
    }

    /**
     * shows the search
     */
    public function displayFormAction() {
        if(!empty(\TYPO3\CMS\Core\Utility\GeneralUtility::_GPmerged('tx_libconnect_dbis'))){
            $params = \TYPO3\CMS\Core\Utility\GeneralUtility::_GPmerged('tx_libconnect_dbis');
        } else{
            $params = \TYPO3\CMS\Core\Utility\GeneralUtility::_GPmerged('libconnect');
        }

        $form = $this->dbisRepository->loadForm($params['search']);

        //variables for template
        $this->view->assign('vars', $params['search']);
        $this->view->assign('form', $form);
        $this->view->assign('siteUrl', $GLOBALS['TSFE']->cObj->getTypolink_URL($GLOBALS['TSFE']->id));//aktuelle URL
        $this->view->assign('listUrl', $GLOBALS['TSFE']->cObj->getTypolink_URL($this->settings['flexform']['listPid']));//Link zur Suchseite
        $this->view->assign('listPid', $this->settings['flexform']['listPid']);//Link zur Listendarstellung
    }

    /**
     * shows the new entries
     */
    public function displayNewAction() {
        if(!empty(\TYPO3\CMS\Core\Utility\GeneralUtility::_GPmerged('tx_libconnect_dbis'))){
            $params = \TYPO3\CMS\Core\Utility\GeneralUtility::_GPmerged('tx_libconnect_dbis');
        } else{
            $params = \TYPO3\CMS\Core\Utility\GeneralUtility::_GPmerged('libconnect');
        }
        
        $params['jq_type1'] = 'LD';
        $params['sc'] = $params['search']['sc'];

        if(!empty($params['subject'])){
            $subject = $this->dbisRepository->getSubject($params['subject']);
            $params['gebiete'][]=$subject['dbisid'];
        }
        unset($params['subject']);
        unset($params['search']);

        //date how long entry is new
        $params['jq_term1'] = $this->getCalculatedDate();

        $config['detailPid'] = $this->settings['flexform']['detailPid'];
        if(empty($config['detailPid'])){
            $this->addFlashMessage(
                "Bitte konfigurieren Sie ein Ziel für die Detailseite.",
                $messageTitle = 'Fehler',
                $severity = \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR,
                $storeInSession = TRUE
            );
            $list = FALSE;
        }else{
            //request
            $list =  $this->dbisRepository->loadSearch($params, $config);
        }

        //decide full or short text
        $list['access_infos'] = $this->setAccessInformation($list['access_infos']);
        
        //variables for template
        $this->view->assign('list', $list);
        $this->view->assign('new_date', $params['jq_term1']);
        $this->view->assign('subject', $subject['title']);
    }

    /**
     * count the new entries
     *
     * @return array
     */
    private function getNewCount($subjectId = FALSE) {
        $params['jq_type1'] = 'LD';
        $params['sc'] = $params['search']['sc'];

        if($subjectId != FALSE){
            $params['gebiete'][]=$subjectId;
        }

        unset($params['subject']);
        unset($params['search']);

        //date how long entry is new
        $params['jq_term1'] = $this->getCalculatedDate();

        $config['detailPid'] = $this->settings['flexform']['detailPid'];
        $config['onlyNew'] = TRUE;
        //request
        $list = $this->dbisRepository->loadSearch($params, $config);

        return $list['db_count'];
    }

    /**
     * calculates the date for a search of new entries
     * 
     * @return string $date
     */
    private function getCalculatedDate(){
        date_default_timezone_set('GMT+1');//@todo get the information from system

        $oneDay = 86400;//seconds
        $numDays = 7; //default are 7 days
        $today = strtotime('now');

        if(!empty($this->settings['flexform']['countDays'])){
            $numDays = $this->settings['flexform']['countDays'];
        }

        //calcaulate date
        $date = date("d.m.Y",$today-($numDays * $oneDay));

        return $date;
    }
    
    /**
     * decide full or short text
     * 
     * @return array
     */
    private function setAccessInformation($accessInforomation){
        $showAccess = $this->settings['flexform']['showAccess'];

        //full is default and variables are set right
        if($showAccess == "full"){
            return $accessInforomation;
        }

        $accessInformationShort = array();

        foreach($accessInforomation as $information){
            $accessInformationShort[$information['id']] = array(
                'id' => $information['id'],
                'description' => $information['description_short']
            );
        }

        return $accessInformationShort;
    }
}
?>
