<?php
namespace Sub\Libconnect\Domain\Model;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012 Torsten Witt <torsten.witt@sub.uni-hamburg.de>, Stabi Hamburg
 *  
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 *
 *
 * @package libconnect
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 *
 */

class Subject extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity {

    /**
     * name of subject
     *
     * @var string
     * @validate NotEmpty
     */
    protected $title;

    /**
     * notation in DBIS system
     *
     * @var string
     */
    protected $dbisid;

    /**
     * notation in EZB system
     *
     * @var string
     */
    protected $ezbnotation;
    
    /**
     * uid
     *
     * @var string
     */
    protected $uid;

    /**
     * __construct
     */
    public function __construct() {

    }

    /**
     * Returns the title
     *
     * @return string $title
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     * Sets the title
     *
     * @param string $title
     */
    public function setTitle($title) {
        $this->title = $title;
    }

    /**
     * Returns the dbisid
     *
     * @return string $dbisid
     */
    public function getDbisid() {
        return $this->dbisid;
    }

    /**
     * Sets the dbisid
     *
     * @param string $dbisid
     */
    public function setDbisId($dbisid) {
        $this->dbisId = $dbisid;
    }

    /**
     * Returns the ezbnotation
     *
     * @return string $ezbnotation
     */
    public function getEzbnotation() {
        return $this->ezbnotation;
    }

    /**
     * Sets the ezbnotation
     *
     * @param string $ezbnotation
     */
    public function setEzbnotation($ezbnotation) {
        $this->ezbnotation = $ezbnotation;
    }
}
?>
