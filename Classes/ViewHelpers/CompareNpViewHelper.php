<?php
namespace Sub\Libconnect\ViewHelpers;

use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * This class is a view helper that compare variables.
 *
 * @package TYPO3
 * @subpackage Fluid
 * @version
 */
class CompareNpViewHelper extends AbstractViewHelper {

    use CompileWithRenderStatic;

    public function initializeArguments() {
        $this->registerArgument('a', 'mixed', 'The variable to compare with the constant.', true);
        $this->registerArgument('b', 'mixed', 'The defined constant to compare with the variable.', true);
    }

    /**
     * 
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     * @return boolean
     */
    public static function renderStatic ( array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext ) {

        return $arguments['a'] === $arguments['b'];
   }
}
?>
