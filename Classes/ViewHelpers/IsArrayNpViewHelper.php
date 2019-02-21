<?php
namespace Sub\Libconnect\ViewHelpers;

use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * This class is a view helper that finds whether a variable is an array.
 *
 * @package TYPO3
 * @subpackage Fluid
 * @version
 */
class IsArrayNpViewHelper extends AbstractViewHelper {

    use CompileWithRenderStatic;

    public function initializeArguments() {
        $this->registerArgument('value', 'mixed', 'The to check.', FALSE);
    }

    /**
     * Returns true if $value is array, false otherwise.
     * 
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     * @return bool
     */
    public static function renderStatic ( array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext ) {

        return is_array($arguments['value']);
    }
}
?>
