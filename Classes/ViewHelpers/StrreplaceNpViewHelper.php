<?php

namespace Sub\Libconnect\ViewHelpers;

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

/**
 * This class is a view helper that replace a string.
 *
 * @version
 */
class StrreplaceNpViewHelper extends AbstractViewHelper
{
    use CompileWithRenderStatic;

    public function initializeArguments()
    {
        $this->registerArgument('search', 'string', 'The search string.', false);
        $this->registerArgument('replace', 'string', 'The replacement.', false);
    }

    /**
     * Replace a string with a string
     *
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     * @return type
     */
    public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext)
    {
        $subject = $renderChildrenClosure();

        $newString = str_replace($arguments['search'], $arguments['replace'], $subject);

        return $newString;
    }
}
