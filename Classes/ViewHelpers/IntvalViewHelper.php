<?php
namespace Sub\Libconnect\ViewHelpers;
/**
 * This class is a view helper that convert a variable in Integer.
 *
 * @package TYPO3
 * @subpackage Fluid
 * @version
 */

class IntvalViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

    /**
     * Returns the parameter as integer
     *
     * @param mixed $value
     * @return int
     */
    public function render($wert) {
        $return = intval($wert);

        return $return;
    }
}
?>