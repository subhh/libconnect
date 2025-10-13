<?php
declare(strict_types = 1);

/*
 * This file is part of the package bk2k/bootstrap-package.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Subhh\Libconnect\Utility;

use Psr\Http\Message\ServerRequestInterface;

/**
 * TypoScriptUtility
 */
class TypoScriptUtility
{
    public static function getSetup(ServerRequestInterface $request): array
    {
        return $request->getAttribute('frontend.typoscript')->getSetupArray();
    }

    public static function getConstants(ServerRequestInterface $request): array
    {
        return $request->getAttribute('frontend.typoscript')->getFlatSettings();
    }
}
