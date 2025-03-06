<?php

declare(strict_types=1);

namespace In2code\LuxletterImport\ViewHelpers\SelectOptions;

use Closure;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class FormatFegroupsViewHelper extends AbstractViewHelper
{
    public function initializeArguments(): void
    {
        $this->registerArgument('fegroups', 'array', 'Frontendusergroups to be parsed as options');
    }

    public static function renderStatic(
        array $arguments,
        Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ) {
        $options = [];

        foreach ($arguments['fegroups'] ?? [] as $group) {
            $options[$group['uid'] ?? 0] = $group['title'] ?? '';
        }

        return $options;
    }
}
