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
        //include CSS
        $this->decideIncludeCSS();

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
            $params = array_merge($params_temp, $params);
        }

        //include CSS
        $this->decideIncludeCSS();

        if (!empty($params['subject'])) {//choosed subject after start point
            $config['sort'] = $this->settings['flexform']['sortParameter'];
            $config['detailPid'] = $this->settings['flexform']['detailPid'];

            //user sorted list
            if(isset($params['sort']) && !empty($params['sort'])) {
                $config['sort'] = $params['sort'];
            }

            $liste =  $this->dbisRepository->loadList($params['subject'], $config);

            //variables for template
            $this->view->assign('listhead', $liste['subject']);
            $this->view->assign('subject', $params['subject']);
            $this->view->assign('list', $liste['list']);

        } else if (!empty($params['search'])) {//search results
            $config['detailPid'] = $this->settings['flexform']['detailPid'];

            $liste =  $this->dbisRepository->loadSearch($params['search'], $config);

            //change view
            $controllerContext = $this->buildControllerContext();
            $controllerContext->getRequest()->setControllerActionName('displaySearch');
            $this->view->setControllerContext($controllerContext);

            //variables for template
            $this->view->assign('list', $liste);

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

        //include CSS
        $this->decideIncludeCSS();

        if (!($params['titleid'])){
            //Variable Template übergeben
            $this->view->assign('error', 'Error');

            return;
        }
        $liste =  $this->dbisRepository->loadDetail($params['titleid']);

        //repair broken HTML
        $UserFunc = new \Sub\Libconnect\UserFunctions\RepairHTMLUserFunction();
        $liste = $UserFunc->RepairHTMLUserFunction($liste);

        if(!$liste){
            //variables for template
            $this->view->assign('error', 'Error');

        }else{
            //BG> Hide start research link for internal access only items
            if($liste['access_id']!='access_4'){
                $liste['access_workaround']=$liste['access_id'];
            }
            //variables for template
            $this->view->assign('db', $liste);
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
            $params = array_merge($params_temp, $params);
        }

        //include CSS
        $this->decideIncludeCSS();

        $form = $this->dbisRepository->loadMiniForm();

        //variables for template
        $this->view->assign('vars', $params['search']);
        $this->view->assign('form', $form);
        $this->view->assign('siteUrl', $GLOBALS['TSFE']->cObj->getTypolink_URL($GLOBALS['TSFE']->id));//active URL
        $this->view->assign('searchUrl', $GLOBALS['TSFE']->cObj->getTypolink_URL($this->settings['flexform']['searchPid']));//link to search page
        $this->view->assign('listUrl', $GLOBALS['TSFE']->cObj->getTypolink_URL($this->settings['flexform']['listPid']));//link to list page
        $this->view->assign('listPid', $this->settings['flexform']['listPid']);//id of page with list

        //possibility for sorting the entries of the subject for the choosed subject
        if(!empty($params['subject'])) {
            if($params['subject'] != 'all'){
                $this->view->assign('listingsWrapper', true);

            //new in DBIS in alphabetical list
            }  else {
                $this->view->assign('newUrl', $GLOBALS['TSFE']->cObj->getTypolink_URL( intval($this->settings['flexform']['newPid'])) );
            }

            //if new activated should here the new for subject be active
            if(!empty($this->settings['flexform']['newPid'])){
                $subject = $this->dbisRepository->getSubject($params['subject']);
                $count = (int) $this->getNewCount($subject['dbisid']);

                if($count >0){
                    $this->view->assign('newUrlSub', $GLOBALS['TSFE']->cObj->getTypolink_URL( intval($this->settings['flexform']['newPid']), 
                        array('libconnect' => array('subject' => $params['subject'] )) ) );//URL der New-Darstellung

                    $this->view->assign('newInSubjectCount',  $count);
                }
            }
        //new in all subjects
        }elseif(!empty($this->settings['flexform']['newPid'])){
            $count = (int) $this->getNewCount(FALSE);

            if($count >0){
                $this->view->assign('newUrl', $GLOBALS['TSFE']->cObj->getTypolink_URL( intval($this->settings['flexform']['newPid'])) );
                $this->view->assign('newInSubjectCount',  $count);
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

        //include CSS
        $this->decideIncludeCSS();

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

        //include CSS
        $this->decideIncludeCSS();

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
            $liste = FALSE;
        }else{
            //request
            $liste =  $this->dbisRepository->loadSearch($params, $config);
        }

        //variables for template
        $this->view->assign('list', $liste);
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

        //request
        $list = $this->dbisRepository->loadSearch($params, $config);

        return $list['db_count'];
    }

    /**
     * check if css file is need and includes it
     */
    private function decideIncludeCSS(){
        //if user don´t want to use our css
        $noCSS = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_libconnect.']['settings.']['dbisNoCSS'];
        if($noCSS == 1){
            return;
        }

        //get UID of PlugIn
        $this->contentObj = $this->configurationManager->getContentObject();
        $uid = $this->contentObj->data['uid'];
        unset($this->contentObj);

        //only the first PlugIn needs to include the css
        if(\Sub\Libconnect\UserFunctions\IsfirstPlugInUserFunction::IsfirstPlugInUserFunction('dbis', $uid)){
            $this->response->addAdditionalHeaderData('<link rel="stylesheet" href="' . \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::siteRelPath('libconnect') . 'Resources/Public/Styles/dbis.css" />');    
        }
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
}
?>

