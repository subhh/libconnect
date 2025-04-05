<?php

namespace Subhh\Libconnect\Controller;

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

use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;
use Subhh\Libconnect\Domain\Repository\DbisRepository;

/**
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 */
class DbisController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{
    /**
     * @var DbisRepository
     */
    protected $dbisRepository;

    /**
     * shows top databases
     */
    public function displayTopAction(): ResponseInterface
    {
        //get top list
        $config['subject'] = $this->settings['flexform']['subject'];

        $list =  $this->dbisRepository->loadTop($config);

        //variables for template
        $this->view->assign('result', $list);
        $this->view->assign('detailPid', $this->settings['flexform']['detailPid']);

        return $this->htmlResponse();
    }

    /**
     * shows a list of databases (for general, search, chosen subject)
     */
    public function displayListAction(): ResponseInterface
    {
        $params = [];

        if (!empty( $this->request->getQueryParams()['tx_libconnect_dbissidebar']['libconnect'])) {
            $params = $this->request->getQueryParams()['tx_libconnect_dbissidebar']['libconnect'];
        }
        if (!empty( $this->request->getQueryParams()['libconnect'])) {
            $params = array_merge($this->request->getQueryParams()['libconnect'], $params);
        }

        //get PageID
        $Uid = (int)($GLOBALS['TSFE']->page['uid']);
        $this->view->assign('listUid', $Uid);

        //show overview on empty search
        $isSearch = false;
        if (key_exists('jq_term1', $params) || (!empty($params['sword']))) {
            $isSearch = true;
        }

        $templateRootPaths = $this->view->getTemplateRootPaths();

        if ( !empty($params['lett']) ) {//chosen subject after start point
            $config['sort'] = $this->settings['flexform']['sortParameter'];

            //user sorted list
            if (isset($params['sort']) && !empty($params['sort'])) {
                $config['sort'] = $params['sort'];
            }
            //set default sorting
            if (empty($config['sort'])) {
                $config['sort'] = 'type';
            }

            if (isset($params['zugaenge']) && ($params['zugaenge'] != '1000')) {
                $config['zugaenge']=$params['zugaenge'];

                //it is not possible to filter "zugaenge" if sort is set to access
                if ($config['sort'] == 'access') {
                    $config['sort'] = 'type';
                }
            }

            $list =  $this->dbisRepository->loadList($params['gebiete'], $config, $params);

            //check, if there are no results and inform user to change licence
            $empty = false;
            if (empty($list['dbs']['list'])) {
                $empty = true;
            }
            
            $this->view->assign('empty', $empty);
            
            //decide full or short text
            $list['access_infos'] = $this->setAccessInformation($list['access_infos']);

            //variables for template
            $this->view->assign('listhead', $list['headline']);
            $this->view->assign('gebiete', $params['gebiete']);
            $this->view->assign('params', $params);
            $this->view->assign('zugaenge', $params['zugaenge']);
            $this->view->assign('result', $list);
            $this->view->assign('detailPid', $this->settings['flexform']['detailPid']);

        } elseif ($isSearch !== false) {//search results

            $list =  $this->dbisRepository->loadSearch($params, array());

            //change view
            $this->view->setTemplatePathAndFilename($templateRootPaths[0].'/Dbis/DisplaySearch.html');

            //decide full or short text
            $list['access_infos'] = $this->setAccessInformation($list['access_infos']);

            //variables for template
            $this->view->assign('list', $list);
            $this->view->assign('result', $list);
            $this->view->assign('detailPid', $this->settings['flexform']['detailPid']);

        } else {//start point
            //$this->setLanguage();
            
            $list =  $this->dbisRepository->loadOverview();

            
            //use other view
            $this->view->setTemplatePathAndFilename($templateRootPaths[0].'/Dbis/DisplayOverview.html');

            //variables for template
            $this->view->assign('list', $list['list_subjects_collections']);
        }

        return $this->htmlResponse();
    }

    /**
     * shows deatail view
     */
    public function displayDetailAction(): ResponseInterface
    {
        $params = [];

        if (!empty( $this->request->getQueryParams()['libconnect'])) {
            $params = $this->request->getQueryParams()['libconnect'];
        }

        if (!($params['titleid'])) {
            //variables for template
            $this->view->assign('error', 'Error');

            return $this->htmlResponse();
        }
        $list =  $this->dbisRepository->loadDetail($params['titleid']);

        //repair broken HTML
        $UserFunc = new \Subhh\Libconnect\UserFunctions\RepairHTMLUserFunction();
        $list = $UserFunc->RepairHTMLUserFunction($list);

        if (!$list) {
            //variables for template
            $this->view->assign('error', 'Error');
        } else {
            //BG> Hide start research link for internal access only items
            if ($list['access_id']!='access_4') {
                $list['access_workaround']=$list['access_id'];
            }
            //variables for template
            $this->view->assign('db', $list);
        }
        
        $lang = $this->getLanguage();
        $this->view->assign('lang', $lang);

        return $this->htmlResponse();
    }

    /**
     * shows sidebar
     */
    public function displayMiniFormAction(): ResponseInterface
    {
        $params = [];
        if (!empty( $this->request->getQueryParams()['tx_libconnect_dbissidebar']['libconnect'])) {
            $params = $this->request->getQueryParams()['tx_libconnect_dbissidebar']['libconnect'];
        }
        if (!empty( $this->request->getQueryParams()['libconnect'])) {
            $params = $this->request->getQueryParams()['libconnect'];
        }

        $form = $this->dbisRepository->loadMiniForm();

        //variables for template
        $this->view->assign('form', $form);
        $this->view->assign('listPid', (int)$this->settings['flexform']['listPid']);//id of page with list
        $this->view->assign('searchPid', (int)$this->settings['flexform']['searchPid']);//id of page with advanced search
        $this->view->assign('vars', $params);
        //hide selectbox for licence/access if search and sort alph
        if (isset($params['sort']) && isset($params['gebiete'])) {
            if (($params['sort'] == 'alph') && ($params['gebiete'] == 'all')) {
                $this->view->assign('hideAccess', true);
            }
        }
        if (isset($params['gebiete'])) {
            if (($params['gebiete'] == 'all')) {
                $this->view->assign('hideAccess', true);
            }
        }
        //if no databases are listed, no filter is displayed
        if (empty($params)) {
            $this->view->assign('hideAccess', true);
        }
        //Set stubject
        if (!empty($params['gebiete'])) {
            $this->view->assign('gebiete', $params['gebiete']);
        }
        if (!empty($params['lett'])) {
            $this->view->assign('lett', $params['lett']);
        }
        //sort, if default was changed
        if (!empty($params['sort'])) {
            $this->view->assign('sort', $params['sort']);
        }
        //dbis-listings-wrapper
        if (!empty($params['gebiete'])) {
            if ($params['gebiete'] != 'all') {
                $this->view->assign('listingsWrapper', true);
            }
        }

        //links to the plugin "new in dbis"
        if (!empty($this->settings['flexform']['newPid'])) {

            //new entries for a selected subject
            if (!empty($params['gebiete']) && ($params['gebiete'] != 'all') && (!is_array($params['gebiete']) ) ) {

                $count = (int)$this->getNewCount($params['gebiete'], $this->settings['flexform']['countDays']);

                if ($count > 0) {
                    $this->view->assign('newInSubjectCount', $count);
                }
            }

            //new entries for all subjects
            $count = (int)$this->getNewCount(false, $this->settings['flexform']['countDays']);

            //show "new in EZB" only if there is something new
            if ($count > 0) {
                $this->view->assign('newPid', (int)($this->settings['flexform']['newPid']));
                $this->view->assign('newCount', $count);
            }
        }

        return $this->htmlResponse();
    }

    /**
     * shows the search
     */
    public function displayFormAction(): ResponseInterface
    {
        $params = [];
        if (!empty( $this->request->getQueryParams()['libconnect'])) {
            $params = $this->request->getQueryParams()['libconnect'];
        }

        $form = $this->dbisRepository->loadForm($params);

        //variables for template
        $this->view->assign('vars', $params);
        $this->view->assign('form', $form);
        $this->view->assign('listPid', $this->settings['flexform']['listPid']);//Link zur Listendarstellung

        return $this->htmlResponse();
    }

    /**
     * shows the new entries
     */
    public function displayNewAction(): ResponseInterface
    {
        $params = [];
        if (!empty( $this->request->getQueryParams()['libconnect'])) {
            $params = $this->request->getQueryParams()['libconnect'];
        }

        $params['jq_type1'] = 'LD';
        //$params['sc'] = $params['sc'];

        //date how long entry is new
        $params['jq_term1'] = $this->getCalculatedDate($this->settings['flexform']['countDays']);
        $params['jq_bool1'] = 'AND';

        if (empty($this->settings['flexform']['detailPid'])) {
            $this->addFlashMessage(
                'Bitte konfigurieren Sie ein Ziel für die Detailseite.',
                $messageTitle = 'Fehler',
                $severity = \TYPO3\CMS\Core\Type\ContextualFeedbackSeverity::ERROR,

                $storeInSession = true
            );
            $list = false;
        } else {
            //request
            $list =  $this->dbisRepository->loadSearch($params, array());
        }

        if (!empty($params['gebiete'])) {
            $this->view->assign('subject', $list['headline']);
        }

        //decide full or short text
        $list['access_infos'] = $this->setAccessInformation($list['access_infos']);

        //variables for template
        $this->view->assign('list', $list);
        $this->view->assign('new_date', $params['jq_term1']);
        
        $this->view->assign('detailPid', $this->settings['flexform']['detailPid']);
        
        return $this->htmlResponse();
    }

    /**
     * count the new entries
     *
     * @return array
     */
    private function getNewCount($subjectId = false, $countDays)
    {
        $params['jq_type1'] = 'LD';
        //$params['sc'] = $params['sc'];

        if ($subjectId != false) {
            $params['gebiete']=$subjectId;
        }

        //date how long entry is new
        $params['jq_term1'] = $this->getCalculatedDate($countDays);

        $config['onlyNew'] = true;
        //request
        $list = $this->dbisRepository->loadSearch($params, $config);

        return $list['dbs']['db_count'];
    }

    /**
     * calculates the date for a search of new entries
     *
     * @return string $date
     */
    private function getCalculatedDate($countDays)
    {
    
        if(!date_default_timezone_get()){
            date_default_timezone_set('Europe/Berlin');//@todo get the information from system
        }

        $oneDay = 86400;//seconds
        $numDays = 7; //default are 7 days
        $today = strtotime('now');

        if ( !empty($countDays) && ($countDays > 0) ) {
            $numDays = $countDays;
        }

        //calcaulate date
        $date = date('d.m.Y', $today-($numDays * $oneDay));

        return $date;
    }

    /**
     * decide full or short text
     *
     * @return array
     */
    private function setAccessInformation($accessInforomation)
    {
        $showAccess = $this->settings['flexform']['showAccess'];

        $newAccessInformation = [];

        foreach ($accessInforomation as $information) {
        
            $newAccessInformation[$information['access_id']] = [
                'id' => $information['access_id'],
                'description' => ($showAccess == 'full' ? $information['db_access'] : $information['db_access_short_text'])
            ];
        }

        return $newAccessInformation;
    }
    
    private function getLanguage()
    {
        $language = $this->request->getAttribute('language');
        $locale = $language->getLocale();
        $langCode = $locale->getlanguageCode();

        return $langCode;
    }
    
    private function setLanguage()
    {
        $language = $this->request->getAttribute('language');
        $locale = $language->getLocale();
        $langCode = $locale->getlanguageCode();

        $this->dbisRepository->setLanguageCode($langCode);
    }

    /**
     * @param \Subhh\Libconnect\Domain\Repository\DbisRepository $dbisRepository
     */
    public function injectDbisRepository(DbisRepository $dbisRepository) {
        $this->dbisRepository = $dbisRepository;
    }
}
