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
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;
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
        if (!empty( $this->request->getQueryParams()['tx_libconnect_ezblist'])) {
        $params = [];
            $params = $this->request->getQueryParams()['tx_libconnect_ezblist'];
        }
        if (!empty( $this->request->getQueryParams()['libconnect'])) {
            $params = $this->request->getQueryParams()['libconnect'];
        }

        //show overview on empty search
        $isSearch = false;
        if (!empty($params['sword']) || (!empty($params['jq_term1'])) || (!empty($params['jq_term2'])) || (!empty($params['jq_term3'])) ) {
            $isSearch = true;
        }

        //set language
        $this->setLanguage();

        //get PageID
        $Uid = (int)($GLOBALS['TSFE']->page['uid']);
        $this->view->assign('listUid', $Uid);

        $templateRootPaths = $this->view->getTemplateRootPaths();

        if (!empty($params['notation'])) {//chosen subject after start point --> template DisplayList

            $options['sindex'] = $params['sindex'] ?? '';
            $options['sc'] = $params['sc'] ?? '';
            $options['lc'] = $params['lc'] ?? '';
            $options['notation'] = $params['notation'] ?? '';
            $options['colors'] = $params['colors'] ?? '';

            //it´s for there is not NULL in the request or there will be a problem
            if (!isset($params['notation'])) {
                $params['notation'] = '';
            }
            $journals =  $this->ezbRepository->loadList(
                $params['notation'],
                $options,
                array()
            );

            $formParameter = [
                'sindex' => $options['sindex'],
                'sc' => $options['sc'],
                'lc' => $options['lc'],
                'notation' => $options['notation']
            ];

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
            $this->view->assign('colors', $params['colors']);
            $this->view->assign('formParameter', $formParameter);
            $this->view->assign('detailPid', $this->settings['flexform']['detailPid']);

        } elseif ($isSearch !== false) {//search results

            $journals = [];

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


            //search
            $journals =  $this->ezbRepository->loadSearch($params, $params['colors']);

            //disable first link in navigation initial view
            if( !empty($journals['navlist']['pages']) && empty($params['sc']) ){
                $firstElement = array_key_first($journals['navlist']['pages']);

                $journals['navlist']['pages'][$firstElement] = $journals['navlist']['pages'][$firstElement]['title'];
            }

            //change view
            $this->view->setTemplatePathAndFilename($templateRootPaths[0].'/Ezb/DisplaySearch.html');

            //variables for template
            $this->view->assign('journals', $journals);
            $this->view->assign('colors', $params['colors']);
            $this->view->assign('formParameter', $params);
            $this->view->assign('detailPid', $this->settings['flexform']['detailPid']);

        } else {//start point

            $journals =  $this->ezbRepository->loadOverview();

            //change view
            $this->view->setTemplatePathAndFilename($templateRootPaths[0].'/Ezb/DisplayOverview.html');

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
        if (!empty( $this->request->getQueryParams()['libconnect'])) {
            $params = $this->request->getQueryParams()['libconnect'];
        }

        $this->setLanguage();

        //error - wrong jourid
        //@todo move in new function
        if(!is_numeric($params['jourid']) || empty($params['jourid'])){
            $templateRootPaths = $this->view->getTemplateRootPaths();

            //change view
            $this->view->setTemplatePathAndFilename($templateRootPaths[0].'/Ezb/DisplayError.html');

            return $this->htmlResponse();
        }

        //$this->ezbRepository->setLongAccessInfos($this->ezblongaccessinfos->de);

        $journal =  $this->ezbRepository->loadDetail($params['jourid'], array('listPid' => $this->settings['flexform']['listPid']));

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
        $this->view->assign('participantsPid', $this->settings['flexform']['participantsPid']);
        $this->view->assign('listPid', $this->settings['flexform']['listPid']);

        return $this->htmlResponse();
    }

    /**
     * shows sidebar
     */
    public function displayMiniFormAction(): ResponseInterface
    {
        $params = [];
        if (!empty( $this->request->getQueryParams()['libconnect'])) {
            $params = $this->request->getQueryParams()['libconnect'];
        }

        $this->setLanguage();

        //variables for template
        $newparams = [];
        if (!empty($params['sword'])) {
            $newparams['sword'] = $params['sword'];
        }
        $this->view->assign('vars', $newparams);

        $this->view->assign('searchPid', $this->settings['flexform']['searchPid']);//link to search
        $this->view->assign('listPid', $this->settings['flexform']['listPid']);//ID of list

        //links to the plugin "new in ezb"
        if (!empty($this->settings['flexform']['newPid'])) {

            //new entries for a selected subject
            if (!empty($params['notation'])) {
                $this->view->assign('showSubjectLink', true);

                $count = (int)$this->getNewCount($params['notation']);

                if ($count > 0) {
                    $this->view->assign('notation', $params['notation']);

                    $this->view->assign('newInSubjectCount', $count);
                }
            }

            //new entries for all subjects
            $count = (int)$this->getNewCount(false);

            //show "new in EZB" only if there is something new
            if ($count > 0) {
                $this->view->assign('newPid', (int)($this->settings['flexform']['newPid']));
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
        $params = array('search' => NULL);

        if (!empty( $this->request->getQueryParams()['libconnect'])) {
            $params = $this->request->getQueryParams()['libconnect'];
        }

        $this->setLanguage();

        $form = $this->ezbRepository->loadForm();

        //variables for template
        $this->view->assign('vars', $params);
        $this->view->assign('form', $form);
        $this->view->assign('listPid', $this->settings['flexform']['listPid']);//ID of list view

        return $this->htmlResponse();
    }

    /**
     * shows list of new entries
     */
    public function displayNewAction(): ResponseInterface
    {
        $params = [];

        if (!empty( $this->request->getQueryParams()['tx_libconnect_ezblist'])) {
            $params = $this->request->getQueryParams()['tx_libconnect_ezblist'];
        }
        if (!empty( $this->request->getQueryParams()['libconnect'])) {
            $params = $this->request->getQueryParams()['libconnect'];
            
        }
        ;

        $newParams = [];

        $newParams['jq_type1'] = 'ID';
        if (!empty($params['sc'])) {
            $newParams['sc'] = $params['sc'];//paging
        }

        $this->setLanguage();

        //subject
        if (!empty($params['notation'])) {
            $subject = $this->ezbRepository->getSubject($params['notation']);
            $newParams['Notations']=[$subject['notation']];

            $this->view->assign('subject', $subject);
        }

        if (!empty($params['sindex'])) {
            $newParams['sindex'] = $params['sindex'];
        }

        unset($params['notation']);

        //date how long entry is new
        $newParams['jq_term1'] = $this->getCalculatedDate();

        if (empty($this->settings['flexform']['detailPid'])) {
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
            if (empty($params['colors'][1]) &
                empty($params['colors'][2]) &
                empty($params['colors'][4])) {
                $params['colors'] = [
                    1 => 1,
                    2 => 2,
                    4 => 4
                ];
            }

            $journals =  $this->ezbRepository->loadSearch($newParams, $params['colors']);
        }

        //disable first link in navigation initial view
        if( !empty($journals['navlist']['pages']) && empty($params['sc']) ){
                $firstElement = array_key_first($journals['navlist']['pages']);

                $journals['navlist']['pages'][$firstElement] = $journals['navlist']['pages'][$firstElement]['title'];
        }


        //get PageID
        $Pid = (int)($GLOBALS['TSFE']->page['uid']);
        $this->view->assign('pageUid', $Pid);

        //variables for template
        $this->view->assign('journals', $journals);
        $this->view->assign('new_date', $newParams['jq_term1']);
        $this->view->assign('colors', $params['colors']);
        $this->view->assign('formParameter', $newParams);
        $this->view->assign('detailPid', $this->settings['flexform']['detailPid']);

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

        if (!empty( $this->request->getQueryParams()['tx_libconnect_ezblist']['libconnect'])) {
            $params = $this->request->getQueryParams()['tx_libconnect_ezblist']['libconnect'];
        }
	    if (!empty( $this->request->getQueryParams()['libconnect'])) {
            $params = $this->request->getQueryParams()['libconnect'];
        }

        $params['jq_type1'] = 'ID';

        $this->setLanguage();

        if ($subjectId != false) {
            $subject = $this->ezbRepository->getSubject($subjectId);
            $params['Notations']=[$subject['notation']];
        }
        unset($params['notation']);

        //date how long entry is new
        $params['jq_term1'] = $this->getCalculatedDate();

        $colors = [
            1 => 1,
            2 => 2,
            4 => 4
        ];

        //request
        $journals =  $this->ezbRepository->Search($params, $colors);

        return $journals['page_vars']['search_count'];
    }

    /**
     * generates form for the participants
     */
    public function displayParticipantsFormAction(): ResponseInterface
    {
        if (!empty( $this->request->getQueryParams()['libconnect'])) {
            $params = $this->request->getQueryParams()['libconnect'];
        }

        $this->setLanguage();

        //error - wrong jourid
        if(!empty(!is_numeric($params['jourid'])) || empty($params['jourid'])){
            $templateRootPaths = $this->view->getTemplateRootPaths();

            //change view
            $this->view->setTemplatePathAndFilename($templateRootPaths[0].'/Ezb/DisplayError.html');

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
        $this->setLanguage();

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

    private function setLanguage()
    {
        $language = $this->request->getAttribute('language');
        $locale = $language->getLocale();
        $langCode = $locale->getlanguageCode();

        $this->ezbRepository->setLanguageCode($langCode);

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
