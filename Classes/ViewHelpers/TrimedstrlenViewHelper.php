<?php
namespace Sub\Libconnect\ViewHelpers;

/**
 * This class is a view helper that count a trimed string.
 *
 * @package TYPO3
 * @subpackage Fluid
 * @version
 */


class TrimedstrlenViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

    /**
     * Returns count of trimmed string
     *
     * @param string $string
     * @return integer
     */
    public function render($string) {
        return strlen(trim($string));
    }
}
?>
