<?php

namespace Subhh\Libconnect\ViewHelpers;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

/**
 * This class is a view helper that count a trimmed string.
 *
 * @version
 */
class TrimedstrlenNpViewHelper extends AbstractViewHelper
{
    use CompileWithRenderStatic;

    public function initializeArguments()
    {
        $this->registerArgument('string', 'string', 'The string.', false);
    }

    /**
     * Returns count of trimmed string
     *
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     * @return int
     */
    public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext)
    {
        return strlen(trim($arguments['string']));
    }
}
