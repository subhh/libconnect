<?php
namespace Sub\Libconnect\ViewHelpers;

/**
 * This class is a view helper that returns a urldecoded string.
 *
 * @package TYPO3
 * @subpackage Fluid
 * @version
 */

class UrldecodeViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

    /**
     * Returns urldecoded string
     *
     * @param string $url
     * @return string
     */
    public function render($url) {
        return urldecode($url);
    }
}
?>
