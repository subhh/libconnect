<?php
namespace Sub\Libconnect\ViewHelpers;

use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * This class is a view helper that replace a string.
 *
 * @package TYPO3
 * @subpackage Fluid
 * @version
 */
class StrreplaceNpViewHelper extends AbstractViewHelper {

    use CompileWithRenderStatic;
  
    public function initializeArguments() {
        $this->registerArgument('search', 'string', 'The search string.', FALSE);
        $this->registerArgument('replace', 'string', 'The replacement.', FALSE);
        $this->registerArgument('subject', 'string', 'The string.', FALSE);
    }

    /**
     * Replace a string with a string
     * 
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     * @return type
     */
    public static function renderStatic ( array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext ) {

        $newString = str_replace($arguments['search'], $arguments['replace'], $arguments['subject']);

        return $newString;
    }
}
?>
