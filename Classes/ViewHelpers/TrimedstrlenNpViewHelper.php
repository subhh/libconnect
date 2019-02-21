<?php
namespace Sub\Libconnect\ViewHelpers;

/**
 * This class is a view helper that trim a string.
 *
 * @package TYPO3
 * @subpackage Fluid
 * @version
 */

class TrimedstrlenViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

    /**
     * Returns trimed string
     *
     * @param string $string
     * @return string
     */
    public function render($string) {
        return strlen(trim($string));
    }
}
?>