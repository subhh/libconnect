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

class EzbController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController  {

    /**
	 * creates instance of EzbRepository
	 *
	 * @var \Sub\Libconnect\Domain\Repository\EzbRepository
	 * @inject
	 */
	protected $ezbRepository;
    
     /**
     * shows a list of journals (for general, search, choosed subject)
     */
    public function displayListAction() {
        if(!empty(\TYPO3\CMS\Core\Utility\GeneralUtility::_GET('tx_libconnect_ezb'))){
            $params = \TYPO3\CMS\Core\Utility\GeneralUtility::_GET('tx_libconnect_ezb');
        } else{
            $params = \TYPO3\CMS\Core\Utility\GeneralUtility::_GET('libconnect');
        }

        //get PageID
        $Pid = intval($GLOBALS['TSFE']->id);
        
        //include CSS
        $this->decideIncludeCSS();

        if ((!empty($params['subject'])) || (!empty($params['notation']))) {//choosed subject after start point

            $config['detailPid'] = $this->settings['flexform']['detailPid'];

            $options['index'] = $params['index'];
            $options['sc'] = $params['sc'];
            $options['lc'] = $params['lc'];
            $options['notation'] = $params['notation'];
            $options['colors'] = $params['colors'];

            //it´s for there is not NULL in the request or there will be a problem
            if(!isset($params['subject'])){
                $params['subject'] = "";
            }
            $liste =  $this->ezbRepository->loadList(
                $params['subject'], 
                $options,
                $config
            );
            $listURL = $GLOBALS['TSFE']->cObj->getTypolink_URL( $Pid );

            $formParameter = array(
                'libconnect[subject]' => $params['subject'],
                'libconnect[index]' => $params['index'],
                'libconnect[sc]' => $params['sc'],
                'libconnect[lc]' => $params['lc'],
                'libconnect[notation]' => $params['notation']
            );

            // no readable URL?
            // its different to seach results, because of different templates
            /*if(!empty(\TYPO3\CMS\Core\Utility\GeneralUtility::_GET('id'))){
                $formParameter['id'] = $Pid;
            }*/
            //variables for template
            $this->view->assign('journals', $liste);
            $this->view->assign('listUrl', $listURL);
            $this->view->assign('colors', $params['colors']);
            $this->view->assign('formParameter', $formParameter);

        } else if (!empty($params['search'])) {//search results

            $config['detailPid'] = $this->settings['flexform']['detailPid'];

            $journals = array();
 
            if(empty($params['search']['selected_colors'])){
                $params['search']['selected_colors'] = array(
                    1 => 1,
                    2 => 2,
                    4 => 4,
                    6 => 6
                );
            }
            
            //no readable URL?
            /*if(!empty(\TYPO3\CMS\Core\Utility\GeneralUtility::_GET('id'))){
                $this->view->assign('formParameterId', $Pid);
            }*/
            
            $journals =  $this->ezbRepository->loadSearch($params['search'], $journals['colors'], $config);           

            if(!empty($params['search']['selected_colors'])){
                //damit selected_colors nicht in verstekten Formularfeldern auftauchen
                unset($params['search']['selected_colors']);
            }

            //change view
            $controllerContext = $this->buildControllerContext();
            $controllerContext->getRequest()->setControllerActionName('displaySearch');
            $this->view->setControllerContext($controllerContext);
            
            $listURL = $GLOBALS['TSFE']->cObj->getTypolink_URL( $Pid );
            
            //variables for template
            $this->view->assign('journals', $journals);
            $this->view->assign('listUrl', $listURL);
            $this->view->assign('colors', $params['colors']);
            $this->view->assign('formParameter', $params['search']);

        } else {//start point
            
            $liste =  $this->ezbRepository->loadOverview();

            //change view
            $controllerContext = $this->buildControllerContext();
            $controllerContext->getRequest()->setControllerActionName('displayOverview');
            $this->view->setControllerContext($controllerContext);

            //variables for template
            $this->view->assign('list', $liste);
        }
    }

    /**
     * shows details
     */
    public function displayDetailAction() {
        if(!empty(\TYPO3\CMS\Core\Utility\GeneralUtility::_GET('tx_libconnect_ezb'))){
            $params = \TYPO3\CMS\Core\Utility\GeneralUtility::_GET('tx_libconnect_ezb');
        } else{
            $params = \TYPO3\CMS\Core\Utility\GeneralUtility::_GET('libconnect');
        }
        $config['participantsPid'] = $this->settings['flexform']['participantsPid'];
        $config['listPid'] = $this->settings['flexform']['listPid'];

        //include CSS
        $this->decideIncludeCSS();

        //$this->set('bibid', $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_libconnect.']['ezbbibid']);
        if (!($params['jourid'])){
            $this->view->assign('error', 'Error');
            //return "<strong>Fehler: Es wurde keine Zeitschrift mit der angegeben URL gefunden.</strong>";
            return;
        }
        
        //$this->ezbRepository->setLongAccessInfos($this->ezblongaccessinfos->de);
        
        $journal =  $this->ezbRepository->loadDetail($params['jourid'], $config);

    //BOF ZDB LocationData
        //check if locationData is enabled
        if($this->settings['enableLocationData'] == 1) {
            $locationData = $this->ezbRepository->loadLocationData($journal);
            if($locationData) {
                $journal['locationData'] = $locationData;
            }

        }
    //EOF ZDB LocationData

        //variables for template
        $this->view->assign('journal', $journal);
        $this->view->assign('bibid', $this->ezbRepository->getBibid());
    }

    /**
     * shows sidebar
     */
    public function displayMiniFormAction() {
        if(!empty(\TYPO3\CMS\Core\Utility\GeneralUtility::_GET('tx_libconnect_ezb'))){
            $params = \TYPO3\CMS\Core\Utility\GeneralUtility::_GET('tx_libconnect_ezb');
        } else{
            $params = \TYPO3\CMS\Core\Utility\GeneralUtility::_GET('libconnect');
        }

        //include CSS
        $this->decideIncludeCSS();

        //variables for template
        $this->view->assign('vars', $params['search']);

        $this->view->assign('siteUrl', $GLOBALS['TSFE']->cObj->getTypolink_URL($GLOBALS['TSFE']->id));//current URL
        $this->view->assign('searchUrl', $GLOBALS['TSFE']->cObj->getTypolink_URL($this->settings['flexform']['searchPid']));//link to search
        $this->view->assign('listUrl', $GLOBALS['TSFE']->cObj->getTypolink_URL($this->settings['flexform']['listPid']));//link to search results
        $this->view->assign('listPid', $this->settings['flexform']['listPid']);//ID of list

        //if subject is choosed link  to subject list is displayed
        if ((!empty($params['subject'])) || (!empty($params['notation']))) {
            $this->view->assign('showSubjectLink', true);

            //if new activated should here the new for subject be active
            if(!empty($this->settings['flexform']['newPid'])){

                if(!empty($params['subject'])){
                    $count = (int) $this->getNewCount($params['subject']);

                    if($count >0){
                        $this->view->assign('newInSubjectCount',  $count);

                        $this->view->assign('newUrlSub', $GLOBALS['TSFE']->cObj->getTypolink_URL( intval($this->settings['flexform']['newPid']), 
                            array('libconnect' => array('subject' => $params['subject'] )) ) );//URL of new list
                    }
                }
            }

        }elseif(!empty($this->settings['flexform']['newPid'])){
            $count = (int) $this->getNewCount(FALSE);

            //show "new in EZB" only if there is something new
            if($count >0){
                $this->view->assign('newUrl', $GLOBALS['TSFE']->cObj->getTypolink_URL( intval($this->settings['flexform']['newPid'])) );
                $this->view->assign('newInSubjectCount',  $count);
            }
        }
    }

    /**
     * show the seach form
     */
    public function displayFormAction() {
        if(!empty(\TYPO3\CMS\Core\Utility\GeneralUtility::_GET('tx_libconnect_ezb'))){
            $params = \TYPO3\CMS\Core\Utility\GeneralUtility::_GET('tx_libconnect_ezb');
        } else{
            $params = \TYPO3\CMS\Core\Utility\GeneralUtility::_GET('libconnect');
        }

        //include CSS
        $this->decideIncludeCSS();

        $form = $this->ezbRepository->loadForm();
        
        //variables for template
        $this->view->assign('vars', $params['search']);
        $this->view->assign('form', $form);
        $this->view->assign('siteUrl', $GLOBALS['TSFE']->cObj->getTypolink_URL($GLOBALS['TSFE']->id));//current URL
        $this->view->assign('listUrl', $GLOBALS['TSFE']->cObj->getTypolink_URL($this->settings['flexform']['listPid']));//url to search
        $this->view->assign('listPid', $this->settings['flexform']['listPid']);//ID of list view
    }

    /**
     * shows list of new entries
     */
    public function displayNewAction() {
        if(!empty(\TYPO3\CMS\Core\Utility\GeneralUtility::_GET('tx_libconnect_ezb'))){
            $params = \TYPO3\CMS\Core\Utility\GeneralUtility::_GET('tx_libconnect_ezb');
        } else{
            $params = \TYPO3\CMS\Core\Utility\GeneralUtility::_GET('libconnect');
        }
        $params['jq_type1'] = 'ID';
        $params['sc'] = $params['search']['sc'];
        if(!empty($params['subject'])){
            $subject = $this->ezbRepository->getSubject($params['subject']);
            $params['Notations']=array($subject['ezbnotation']);
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
            $journals =  $this->ezbRepository->loadSearch($params, false, $config);
        }
        
        //variables for template
        $this->view->assign('journals', $journals);
        $this->view->assign('new_date', $params['jq_term1']);
        $this->view->assign('subject', $subject['title']);
    }

    /**
     * count the new entries
     */
    public function getNewCount($subjectId = FALSE) {
        if(!empty(\TYPO3\CMS\Core\Utility\GeneralUtility::_GET('tx_libconnect_ezb'))){
            $params = \TYPO3\CMS\Core\Utility\GeneralUtility::_GET('tx_libconnect_ezb');
        } else{
            $params = \TYPO3\CMS\Core\Utility\GeneralUtility::_GET('libconnect');
        }
        $params['jq_type1'] = 'ID';
        $params['sc'] = $params['search']['sc'];

        if($subjectId != FALSE){
            $subject = $this->ezbRepository->getSubject($subjectId);
            $params['Notations']=array($subject['ezbnotation']);
        }
        unset($params['subject']);
        unset($params['search']);

        //date how long entry is new
        $params['jq_term1'] = $this->getCalculatedDate();

        $config['detailPid'] = $this->settings['flexform']['detailPid'];

        //request
        $journals =  $this->ezbRepository->loadSearch($params, false, $config);

        return $journals['page_vars']['search_count'];
    }

    /**
     * genrates Form for the participants
     */
    public function displayParticipantsFormAction() {
        if(!empty(\TYPO3\CMS\Core\Utility\GeneralUtility::_GET('tx_libconnect_ezb'))){
            $params = \TYPO3\CMS\Core\Utility\GeneralUtility::_GET('tx_libconnect_ezb');
        } else{
            $params = \TYPO3\CMS\Core\Utility\GeneralUtility::_GET('libconnect');
        }
        //include CSS
        $this->decideIncludeCSS();
        //include js
        $this->response->addAdditionalHeaderData('<script type="text/javascript" src="' . \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::siteRelPath('libconnect') . 'Resources/Public/Js/ezb.js" ></script>');    

        $ParticipantsList =  $this->ezbRepository->getParticipantsList($params['jourid']);

        $config['partnerPid'] = 0;
        $journal =  $this->ezbRepository->loadDetail($params['jourid'], $config);
        $titel = $journal['title'];
        unset($journal);

        //variables for template
        $this->view->assign('ParticipantsList', $ParticipantsList);
        $this->view->assign('jourid', $params['jourid']);
        $this->view->assign('titel', $titel);
    }

    /**
     * get contact information
     */
    public function displayContactAction() {
        $contact =  $this->ezbRepository->getContact();
        $this->view->assign('contact', $contact);
    }

    /**
     * check if css file is need and includes it
     */
    private function decideIncludeCSS(){
        //if user don´t want to use our css
        $noCSS = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_libconnect.']['settings.']['ezbNoCSS'];

        if($noCSS == 1){
            return;
        }

        //get UID of PlugIn
        $this->contentObj = $this->configurationManager->getContentObject();
        $uid = $this->contentObj->data['uid'];
        unset($this->contentObj);

        //only the first PlugIn needs to include the css
        if(\Sub\Libconnect\UserFunctions\IsfirstPlugInUserFunction::IsfirstPlugInUserFunction('ezb', $uid)){         
            $this->response->addAdditionalHeaderData('<link rel="stylesheet" href="' . \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::siteRelPath('libconnect') . 'Resources/Public/Styles/ezb.css" />');    
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