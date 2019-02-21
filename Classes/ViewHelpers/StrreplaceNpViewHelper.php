<?php
namespace Sub\Libconnect\ViewHelpers;

/***************************************************************
*  Copyright notice
*
*  (c) 2011 Paul Rohrbeck
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
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
 * This class is a view helper that replace a string.
 *
 * @package TYPO3
 * @subpackage Fluid
 * @version
 */

class StrreplaceViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

    /**
     * replace all matches of the search string with an other
     *
     * @param string $search
     * @param string $replace
     * @return string
     */
    public function render($search = '', $replace = '') {

        $subject = $this->renderChildren();
        $newString = str_replace($search, $replace, $subject);

        return $newString;
    }
}
?>
