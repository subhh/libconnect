<?php
namespace Sub\Libconnect\ViewHelpers;

use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * This class is a view helper that trim a string.
 *
 * @package TYPO3
 * @subpackage Fluid
 * @version
 */
class TrimedstrlenNpViewHelper extends AbstractViewHelper {

    use CompileWithRenderStatic;

    public function initializeArguments() {
        $this->registerArgument('string', 'string', 'The string.', FALSE);
    }

    /**
     * Returns trimed string
     * 
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     * @return string
     */
    public static function renderStatic ( array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext ) {

        return strlen(trim($arguments['string']));
    }
}
?>
