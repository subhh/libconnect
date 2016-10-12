<?php
namespace Sub\Libconnect\ViewHelpers;

/**
 * This class is a view helper that returns a substring.
 *
 * @package TYPO3
 * @subpackage Fluid
 * @version
 */

class TruncateViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

    /**
     * Returns truncated string
     *
     * @param string $string
     * @param length $int
     * @return string
     */
    public function render($string, $length) {
        return mb_substr($string, 0, $length);
    }
}
?>