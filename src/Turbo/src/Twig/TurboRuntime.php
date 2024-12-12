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

use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Mercure\Authorization;
use Symfony\UX\Turbo\Bridge\Mercure\TopicSet;
use Twig\Environment;
use Twig\Extension\RuntimeExtensionInterface;

/**
 * @author Pierre Ambroise <pierre27.ambroise@gmail.com>
 *
 * @internal
 */
class TurboRuntime implements RuntimeExtensionInterface
{
    public function __construct(
        private ContainerInterface $turboStreamListenRenderers,
        private string $default,
        private ?Authorization $authorization = null,
        private ?RequestStack $requestStack = null,
    ) {
    }

    /**
     * @param object|string|array<object|string> $topic
     * @param array<string, mixed>               $options
     */
    public function renderTurboStreamListen(Environment $env, $topic, ?string $transport = null, array $options = []): string
    {
        $transport ??= $this->default;

        if (!$this->turboStreamListenRenderers->has($transport)) {
            throw new \InvalidArgumentException(\sprintf('The Turbo stream transport "%s" does not exist.', $transport));
        }

        if (\is_array($topic)) {
            $topic = new TopicSet($topic);
        }

        if (
            null !== $this->authorization
            && null !== $this->requestStack
            && (isset($options['subscribe']) || isset($options['publish']) || isset($options['additionalClaims']))
            && null !== $request = $this->requestStack->getMainRequest()
        ) {
            $this->authorization->setCookie(
                $request,
                $options['subscribe'] ?? [],
                $options['publish'] ?? [],
                $options['additionalClaims'] ?? [],
                $transport,
            );

            unset($options['subscribe'], $options['publish'], $options['additionalClaims']);
        }

        return $this->turboStreamListenRenderers->get($transport)->renderTurboStreamListen($env, $topic, $options);
    }
}
