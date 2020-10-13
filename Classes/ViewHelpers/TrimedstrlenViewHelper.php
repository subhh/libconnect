<?php

namespace Sub\Libconnect\ViewHelpers;

/**
 * This class is a view helper that count a trimed string.
 *
 * @version
 */
class TrimedstrlenViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper
{

    /**
     * Returns count of trimmed string
     *
     * @param string $string
     * @return int
     */
    public function render($string)
    {
        return strlen(trim($string));
    }
}
