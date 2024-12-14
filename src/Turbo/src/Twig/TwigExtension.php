<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\UX\Turbo\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @author Kévin Dunglas <kevin@dunglas.fr>
 */
final class TwigExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('turbo_stream_listen', [TurboRuntime::class, 'renderTurboStreamListen'], ['needs_environment' => true, 'is_safe' => ['html']]),
        ];
    }
}
