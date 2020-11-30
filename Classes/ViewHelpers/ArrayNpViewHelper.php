<?php

namespace Sub\Libconnect\ViewHelpers;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

/**
 * This class is a view helper that returns a value of an sub array.
 *
 * @version
 */
class ArrayNpViewHelper extends AbstractViewHelper
{
    use CompileWithRenderStatic;

    public function initializeArguments()
    {
        $this->registerArgument('inputArray', 'array', 'The array with the values.', true);
        $this->registerArgument('key', 'string', 'The string to compare with the values of the array.', true);
    }

    /**
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     * @return string
     */
    public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext)
    {
        if (is_array($arguments['inputArray'])) {
            foreach ($arguments['inputArray'] as $value) {
                if ($value == $arguments['key']) {
                    return $value;
                }
            }
        }
    }
}
