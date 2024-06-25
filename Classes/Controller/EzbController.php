<?php

namespace Subhh\Libconnect\Controller;

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

use Psr\Http\Message\ResponseInterface;
use Subhh\Libconnect\Domain\Repository\EzbRepository;
use Subhh\Libconnect\Domain\Repository\SubjectRepository;

/**
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 */
class EzbController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

    /**
     * @var EzbRepository
     */
    protected $ezbRepository;

    /**
     * creates instance of SubjectRepository
     *
     * @var SubjectRepository
     */
    protected $subjectRepository;

    /**
    * shows a list of journals (for general, search, chosen subject)
    */
    public function displayListAction(): ResponseInterface
    {
        $params = [];
        if (!empty(\TYPO3\CMS\Core\Utility\GeneralUtility::_GPmerged('tx_libconnect_ezb'))) {
            $params_temp = \TYPO3\CMS\Core\Utility\GeneralUtility::_GPmerged('tx_libconnect_ezb');
            $params = $params_temp['libconnect'];
        }
        if (!empty(\TYPO3\CMS\Core\Utility\GeneralUtility::_GPmerged('libconnect'))) {
            $params_temp = \TYPO3\CMS\Core\Utility\GeneralUtility::_GPmerged('libconnect');
            $params = array_merge($params_temp, $params);
        }

        //show overview on empty search
        $isSearch = false;
        if (!empty($params['search'])) {
            if ((count($params['search']) > 1) || (!empty($params['search']['sword']))) {
                $isSearch = true;
            }
        }

        //get PageID
        $Pid = (int)($GLOBALS['TSFE']->page['uid']);
        $this->view->assign('pageUid', $Pid);

        if ((!empty($params['subject'])) || (!empty($params['notation']))) {//chosen subject after start point

            $config['detailPid'] = $this->settings['flexform']['detailPid'];

            $options['index'] = $params['index'];
            $options['sc'] = $params['sc'];
            $options['lc'] = $params['lc'];
            $options['notation'] = $params['notation'];
            $options['colors'] = $params['colors'];

            //it´s for there is not NULL in the request or there will be a problem
            if (!isset($params['subject'])) {
                $params['subject'] = '';
            }
            $journals =  $this->ezbRepository->loadList(
                $params['subject'],
                $options,
                $config
            );
            $listURL = $GLOBALS['TSFE']->cObj->getTypolink_URL($Pid);

            $formParameter = [
                'libconnect[subject]' => $params['subject'],
                'libconnect[index]' => $params['index'],
                'libconnect[sc]' => $params['sc'],
                'libconnect[lc]' => $params['lc'],
                'libconnect[notation]' => $params['notation']
            ];

            // no readable URL?
            // its different to search results, because of different templates
            /*if(!empty(\TYPO3\CMS\Core\Utility\GeneralUtility::_GET('id'))){
                $formParameter['id'] = $Pid;
            }*/

            if (empty($params['colors'][1]) &
                empty($params['colors'][2]) &
                empty($params['colors'][4])) {
                $params['colors'] = [
                        1 => 1,
                        2 => 2,
                        4 => 4,
                        6 => 6
                    ];
            }

            //variables for template
            $this->view->assign('journals', $journals);
            $this->view->assign('listUrl', $listURL);
            $this->view->assign('colors', $params['colors']);
            $this->view->assign('formParameter', $formParameter);
        } elseif ($isSearch !== false) {//search results

            $config['detailPid'] = $this->settings['flexform']['detailPid'];

            $journals = [];

            //params form link
            if (!empty($params['search']['colors'])) {
                $params['colors'] = $params['search']['colors'];
            }

            unset($params['search']['colors']);

            //params from color legend
            if (empty($params['colors'][1]) &
                empty($params['colors'][2]) &
                empty($params['colors'][4])) {
                $params['colors'] = [
                        1 => 1,
                        2 => 2,
                        4 => 4
                    ];
            }

            $colors = $params['colors'];
            unset($params['colors']);

            //no readable URL?
            /*if(!empty(\TYPO3\CMS\Core\Utility\GeneralUtility::_GET('id'))){
                $this->view->assign('formParameterId', $Pid);
            }*/

            //search
            $journals =  $this->ezbRepository->loadSearch($params, $colors, $config);

            //change view
            $this->view->setTemplatePathAndFilename(
                'typo3conf/ext/' .
                $this->request->getControllerExtensionKey() .
                '/Resources/Private/Templates/Ezb/DisplaySearch.html'
            );


            //sets the link to the page with list plugin
            $listURL = $GLOBALS['TSFE']->cObj->getTypolink_URL($Pid);

            //variables for template
            $this->view->assign('journals', $journals);
            $this->view->assign('listUrl', $listURL);
            $this->view->assign('colors', $colors);
            $this->view->assign('formParameter', $params['search']);
        } else {//start point

            $journals =  $this->ezbRepository->loadOverview();

            //change view
            $this->view->setTemplatePathAndFilename(
                'typo3conf/ext/' .
                $this->request->getControllerExtensionKey() .
                '/Resources/Private/Templates/Ezb/DisplayOverview.html'
            );

            //variables for template
            $this->view->assign('list', $journals);
        }

	return $this->htmlResponse();
    }

    /**
     * shows details
     */
    public function displayDetailAction(): ResponseInterface
    {
        if (!empty(\TYPO3\CMS\Core\Utility\GeneralUtility::_GPmerged('tx_libconnect_ezb'))) {
            $params = \TYPO3\CMS\Core\Utility\GeneralUtility::_GPmerged('tx_libconnect_ezb');
        } else {
            $params = \TYPO3\CMS\Core\Utility\GeneralUtility::_GPmerged('libconnect');
        }
        $config['participantsPid'] = $this->settings['flexform']['participantsPid'];
        $config['listPid'] = $this->settings['flexform']['listPid'];

        //error - wrong jourid
        if(!is_numeric($params['jourid']) || empty($params['jourid'])){
            //change view
            $this->view->setTemplatePathAndFilename(
                'typo3conf/ext/' .
                $this->request->getControllerExtensionKey() .
                '/Resources/Private/Templates/Ezb/DisplayError.html'
            );

            return $this->htmlResponse();
        }

        //$this->ezbRepository->setLongAccessInfos($this->ezblongaccessinfos->de);

        $journal =  $this->ezbRepository->loadDetail($params['jourid'], $config);

        //BOF ZDB LocationData
        //check if locationData is enabled
        if ($this->settings['enableLocationData'] == 1) {
            $locationData = $this->ezbRepository->loadLocationData($journal);
            if ($locationData) {
                $journal['locationData'] = $locationData;
            }
        }
        //EOF ZDB LocationData

        //variables for template
        $this->view->assign('journal', $journal);
        $this->view->assign('bibid', $this->ezbRepository->getBibid());

	return $this->htmlResponse();
    }

    /**
     * shows sidebar
     */
    public function displayMiniFormAction(): ResponseInterface
    {
        $params = [];
        if (!empty(\TYPO3\CMS\Core\Utility\GeneralUtility::_GPmerged('tx_libconnect_ezb'))) {
            $params_temp = \TYPO3\CMS\Core\Utility\GeneralUtility::_GPmerged('tx_libconnect_ezb');
            $params = $params_temp['libconnect'];
        }
        if (!empty(\TYPO3\CMS\Core\Utility\GeneralUtility::_GPmerged('libconnect'))) {
            $params_temp = \TYPO3\CMS\Core\Utility\GeneralUtility::_GPmerged('libconnect');
            $params = array_merge($params_temp, $params);
        }

        //variables for template
        $newparams = [];
        if (!empty($params['search']['sword'])) {
            $newparams['search']['sword'] = $params['search']['sword'];
        }
        $this->view->assign('vars', $newparams['search']);

        $this->view->assign('siteUrl', $GLOBALS['TSFE']->cObj->getTypolink_URL($GLOBALS['TSFE']->page['uid']));//current URL
        $this->view->assign('searchUrl', $GLOBALS['TSFE']->cObj->getTypolink_URL($this->settings['flexform']['searchPid']));//link to search
        $this->view->assign('listUrl', $GLOBALS['TSFE']->cObj->getTypolink_URL($this->settings['flexform']['listPid']));//link to search results
        $this->view->assign('listPid', $this->settings['flexform']['listPid']);//ID of list

        //if new activated should here the new for subject be active
        if (!empty($this->settings['flexform']['newPid'])) {
            //if subject is chosen link to subject list is displayed
            if (!empty($params['subject'])) {
                $this->view->assign('showSubjectLink', true);

                $count = (int)$this->getNewCount($params['subject']);

                if ($count >0) {
                    $this->view->assign('newInSubjectCount', $count);

                    $this->view->assign('newUrlSub', $GLOBALS['TSFE']->cObj->getTypolink_URL(
                        (int)($this->settings['flexform']['newPid']),
                        ['libconnect' => ['subject' => $params['subject'] ]]
                    ));//URL of new list
                }
            }

            /*all new*/
            $count = (int)$this->getNewCount(false);

            //show "new in EZB" only if there is something new
            if ($count >0) {
                $this->view->assign('newUrl', $GLOBALS['TSFE']->cObj->getTypolink_URL((int)($this->settings['flexform']['newPid'])));
                $this->view->assign('newCount', $count);
            }
        }

	return $this->htmlResponse();
    }

    /**
     * show the search form
     */
    public function displayFormAction(): ResponseInterface
    {
        if (!empty(\TYPO3\CMS\Core\Utility\GeneralUtility::_GPmerged('tx_libconnect_ezb'))) {
            $params = \TYPO3\CMS\Core\Utility\GeneralUtility::_GPmerged('tx_libconnect_ezb');
        } else {
            $params = \TYPO3\CMS\Core\Utility\GeneralUtility::_GPmerged('libconnect');
        }

        $form = $this->ezbRepository->loadForm();

        //variables for template
        $this->view->assign('vars', $params['search']);
        $this->view->assign('form', $form);
        $this->view->assign('siteUrl', $GLOBALS['TSFE']->cObj->getTypolink_URL($GLOBALS['TSFE']->page['uid']));//current URL
        $this->view->assign('listUrl', $GLOBALS['TSFE']->cObj->getTypolink_URL($this->settings['flexform']['listPid']));//url to search
        $this->view->assign('listPid', $this->settings['flexform']['listPid']);//ID of list view

	return $this->htmlResponse();
    }

    /**
     * shows list of new entries
     */
    public function displayNewAction(): ResponseInterface
    {
        $params = [];
        if (!empty(\TYPO3\CMS\Core\Utility\GeneralUtility::_GPmerged('tx_libconnect_ezb'))) {
            $params_temp = \TYPO3\CMS\Core\Utility\GeneralUtility::_GPmerged('tx_libconnect_ezb');
            $params = $params_temp['libconnect'];
        }
        if (!empty(\TYPO3\CMS\Core\Utility\GeneralUtility::_GPmerged('libconnect'))) {
            $params_temp = \TYPO3\CMS\Core\Utility\GeneralUtility::_GPmerged('libconnect');
            $params = array_merge($params_temp, $params);
        }

        $newParams = [];

        $newParams['search']['jq_type1'] = 'ID';
        $newParams['search']['sc'] = $params['search']['sc'];//paging

        //subject
        if (!empty($params['subject'])) {
            $subject = $this->ezbRepository->getSubject($params['subject']);
            $newParams['search']['Notations']=[$subject['notation']];
            $newParams['subject'] = $params['subject'];
        }

        if (!empty($params['search']['sindex'])) {
            $newParams['search']['sindex'] = $params['search']['sindex'];
        }

        unset($params['search']['subject']);
        unset($params['search']['search']);

        //date how long entry is new
        $newParams['search']['jq_term1'] = $this->getCalculatedDate();

        $config['detailPid'] = $this->settings['flexform']['detailPid'];

        if (empty($config['detailPid'])) {
            $this->addFlashMessage(
                'Bitte konfigurieren Sie ein Ziel für die Detailseite.',
                $messageTitle = 'Fehler',
                $severity = \TYPO3\CMS\Core\Type\ContextualFeedbackSeverity::ERROR,
                $storeInSession = true
            );
            $journals = false;
        } else {
            //request
            //params from color legend
            if (empty($params['search']['colors'][1]) &
                empty($params['search']['colors'][2]) &
                empty($params['search']['colors'][4])) {
                $params['search']['colors'] = [
                        1 => 1,
                        2 => 2,
                        4 => 4
                    ];
            }

            $journals =  $this->ezbRepository->loadSearch($newParams, $params['search']['colors'], $config);
        }

        //get PageID
        $Pid = (int)($GLOBALS['TSFE']->page['uid']);
        $this->view->assign('pageUid', $Pid);

        //variables for template
        $this->view->assign('journals', $journals);
        $this->view->assign('new_date', $newParams['search']['jq_term1']);
        $this->view->assign('colors', $params['search']['colors']);
        $this->view->assign('subject', $subject['title']);
        $this->view->assign('formParameter', $newParams);

	return $this->htmlResponse();
    }

    /**
     * count the new entries
     *
     * @return array $journals
     */
    public function getNewCount($subjectId = false)
    {
        $params = [];
        if (!empty(\TYPO3\CMS\Core\Utility\GeneralUtility::_GPmerged('tx_libconnect_ezb'))) {
            $params_temp = \TYPO3\CMS\Core\Utility\GeneralUtility::_GPmerged('tx_libconnect_ezb');
            $params = $params_temp['libconnect'];
        }
        if (!empty(\TYPO3\CMS\Core\Utility\GeneralUtility::_GPmerged('libconnect'))) {
            $params_temp = \TYPO3\CMS\Core\Utility\GeneralUtility::_GPmerged('libconnect');
            $params = array_merge($params_temp, $params);
        }

        $params['search']['jq_type1'] = 'ID';

        if ($subjectId != false) {
            $subject = $this->ezbRepository->getSubject($subjectId);
            $params['search']['Notations']=[$subject['notation']];
        }
        unset($params['subject']);
        //unset($params['search']);

        //date how long entry is new
        $params['search']['jq_term1'] = $this->getCalculatedDate();

        $config['detailPid'] = $this->settings['flexform']['detailPid'];

        $colors = [
                        1 => 1,
                        2 => 2,
                        4 => 4
                ];

        //request
        $journals =  $this->ezbRepository->loadSearch($params, $colors, false);

        return $journals['page_vars']['search_count'];
    }

    /**
     * generates form for the participants
     */
    public function displayParticipantsFormAction(): ResponseInterface
    {
        if (!empty(\TYPO3\CMS\Core\Utility\GeneralUtility::_GPmerged('tx_libconnect_ezb'))) {
            $params = \TYPO3\CMS\Core\Utility\GeneralUtility::_GPmerged('tx_libconnect_ezb');
        } else {
            $params = \TYPO3\CMS\Core\Utility\GeneralUtility::_GPmerged('libconnect');
        }

        //error - wrong jourid
        if(!is_numeric($params['jourid']) || empty($params['jourid'])){
            //change view
            $this->view->setTemplatePathAndFilename(
                'typo3conf/ext/' .
                $this->request->getControllerExtensionKey() .
                '/Resources/Private/Templates/Ezb/DisplayError.html'
            );

            return $this->htmlResponse();
        }

        $ParticipantsList =  $this->ezbRepository->getParticipantsList($params['jourid']);

        $config['partnerPid'] = 0;
        $journal =  $this->ezbRepository->loadDetail($params['jourid'], $config);
        $title = $journal['title'];
        unset($journal);

        //variables for template
        $this->view->assign('ParticipantsList', $ParticipantsList);
        $this->view->assign('jourid', $params['jourid']);
        $this->view->assign('titel', $title);

	return $this->htmlResponse();
    }

    /**
     * get contact information
     */
    public function displayContactAction(): ResponseInterface
    {
        $contact =  $this->ezbRepository->getContact();
        $this->view->assign('contact', $contact);

	return $this->htmlResponse();
    }

    /**
     * calculates the date for a search of new entries
     *
     * @return string $date
     */
    private function getCalculatedDate()
    {
        date_default_timezone_set('GMT+1');//@todo get the information from system

        $oneDay = 86400;//seconds
        $numDays = 7; //default are 7 days
        $today = strtotime('now');

        if (!empty($this->settings['flexform']['countDays'])) {
            $numDays = $this->settings['flexform']['countDays'];
        }

        //calcaulate date
        $date = date('d.m.Y', $today-($numDays * $oneDay));

        return $date;
    }

    /**
     * @param \Subhh\Libconnect\Domain\Repository\EzbRepository $ezbRepository
     */
    public function injectEzbRepository(EzbRepository $ezbRepository) {
        $this->ezbRepository = $ezbRepository;
    }

    /**
     * @param \Subhh\Libconnect\Domain\Repository\SubjectRepository $subjectRepository
     */
    public function injectSubjectRepository(SubjectRepository $subjectRepository) {
        $this->subjectRepository = $subjectRepository;
    }
}
