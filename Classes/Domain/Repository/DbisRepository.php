<?php

namespace Subhh\Libconnect\Domain\Repository;

use Subhh\Libconnect\Lib\Dbis;

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
 */
class DbisRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{
    private $dbis_to_t3_subjects = [];
    private $t3_to_dbis_subjects = [];

    /**
     * @var SubjectRepository
     */
    protected $subjectRepository;

    /**
     * @var Dbis
     */
    protected $dbis;

    /**
     * shows top databases
     *
     * @param array $config
     *
     * @return array $result
     */
    public function loadTop($config)
    {
        $this->loadSubjects();

        $subject = $this->t3_to_dbis_subjects[$config['subject']];
        $dbis_id = $subject['dbisid'];

        $result = $this->dbis->getDbliste($dbis_id);

        //get top dbs
        $result = $this->getListTop($result['list']['top'], $config['detailPid']);

        return $result;
    }

    /**
     * shows a list of databases(for start, search and list of chose subject)
     *
     * @param int $subject_id
     * @param array array('subject'=>array(), 'list'=>array())
     *
     * @return array
     */
    public function loadList($subject_id, $config)
    {
        $this->loadSubjects();
        $accessFilter = false;

        if (isset($config['search']['zugaenge']) && (strlen($config['search']['zugaenge']) > 0)) {
            $accessFilter = $config['search']['zugaenge'];
        }

        //list a subject
        if (is_numeric($subject_id)) {
            $subject = $this->t3_to_dbis_subjects[$subject_id];

            $dbis_id = $subject['dbisid'];

            $result = $this->dbis->getDbliste($dbis_id, $config['sort'], $accessFilter);
        } else {//for own collection or all subject

            //access sort for all entries in all subjects is not possible
            if ($config['sort'] == 'access') {
                $config['sort'] = 'alph';
            }

            $result = $this->dbis->getDbliste($subject_id, $config['sort'], $accessFilter);

            $subject['title'] = $result['headline'];
        }

        //get top dbs
        $result['list']['top'] = $this->getListTop($result['list']['top'], $config['detailPid']);

        foreach (array_keys($result['list']['groups']) as $group) {
            foreach (array_keys($result['list']['groups'][$group]['dbs']) as $db) {
                $result['list']['groups'][$group]['dbs'][$db]['detail_link'] = $GLOBALS['TSFE']->cObj->getTypolink_URL(
                    (int)($config['detailPid']),
                    [
                        'libconnect[titleid]' => $result['list']['groups'][$group]['dbs'][$db]['id'],
                    ]
                );
            }
        }

        // sort groups by name
        $alph_sort_groups = [];
        foreach ($result['list']['groups'] as $group) {
            $alph_sort_groups[$group['title']] = $group;
        }
        ksort($alph_sort_groups, SORT_STRING); //added sort-flag SORT_STRING for correct sorting of alphabetical listings
        $result['list']['groups'] = $alph_sort_groups;

        return ['subject' => $subject['title'], 'list' => $result['list']];
    }

    /**
     * show start
     *
     * @return array $list
     */
    public function loadOverview()
    {
        $this->loadSubjects();
        $list = $this->dbis->getFachliste();

        foreach ($list as $el) {
            if ($el['lett'] != 'c') {
                //get id of subject from database
                $subject = $this->dbis_to_t3_subjects[$el['id']];

                $el['subject'] = $subject['uid'];
            } else {
                $el['subject'] = $el['id']];
            }

            $list[$el['id']] = $el;
        }

        return $list;
    }

    /**
     * load subjects from database
     */
    private function loadSubjects()
    {
        $res = $this->subjectRepository->findAll();

        foreach ($res as $row) {
            $this->dbis_to_t3_subjects[$row->getDbisId()]['dbisid'] = $row->getDbisId();
            $this->dbis_to_t3_subjects[$row->getDbisId()]['title'] = $row->getTitle();
            $this->dbis_to_t3_subjects[$row->getDbisId()]['uid'] = $row->getUid();

            $this->t3_to_dbis_subjects[$row->getUid()]['uid'] = $row->getUid();
            $this->t3_to_dbis_subjects[$row->getUid()]['dbisid'] = $row->getDbisId();
            $this->t3_to_dbis_subjects[$row->getUid()]['title'] = $row->getTitle();
        }
    }

    /**
     * show details
     *
     * @param int $title_id
     * @return mixed
     */
    public function loadDetail($title_id)
    {
        $db = $this->dbis->getDbDetails($title_id);

        if (!$db) {
            return false;
        }

        return $db;
    }

    /**
     * execute search
     *
     * @param array $searchVars
     * @param array $config
     *
     * @return array $result
     */
    public function loadSearch($searchVars, $config)
    {
        $this->loadSubjects();

        //execute search
        $result = $this->dbis->search($searchVars);

        //stop if function called from "New" - controller
        if (isset($config['onlyNew'])) {
            return $result['list'];
        }

        //get top dbs
        $result['list']['top'] = $this->getListTop($result['list']['top'], $config['detailPid']);

        foreach (array_keys($result['list']['values']) as $value) {
            $result['list']['values'][$value]['detail_link'] = $GLOBALS['TSFE']->cObj->getTypolink_URL(
                (int)($config['detailPid']),
                [
                    'libconnect[titleid]' => $result['list']['values'][$value]['id'],
                ]
            );
        }

        return $result['list'];
    }

    /**
    * return miniform
    *
    * @return array $form
    */
    public function loadMiniForm()
    {
        $form = $this->dbis->getExtendedForm();

        return $form;
    }

    /**
     * return form for detail search
     *
     * @return array $form
     */
    public function loadForm()
    {
        $form = $this->dbis->getExtendedForm();

        return $form;
    }

    /**
     * returns a subject
     *
     * @param int $subjectId Id of subject
     */
    public function getSubject($subjectId)
    {
        $this->loadSubjects();

        return $this->t3_to_dbis_subjects[$subjectId];
    }

    /**
     * @param array $list
     * @param int $detailPid id of the detail page
     * @return array Description
     */
    private function getListTop($list, $detailPid)
    {
        foreach (array_keys($list) as $db) {
            $list[$db]['titleid'] = $list[$db]['id'];
        }

        return $list;
    }

    /**
     * @param \Subhh\Libconnect\Domain\Repository\SubjectRepository $subjectRepository
     */
    public function injectSubjectRepository(SubjectRepository $subjectRepository)
    {
        $this->subjectRepository = $subjectRepository;
    }

    /**
     * @param \Subhh\Libconnect\Lib\Dbis $dbis
     */
    public function injectDbis(Dbis $dbis) {
        $this->dbis = $dbis;
    }
}
