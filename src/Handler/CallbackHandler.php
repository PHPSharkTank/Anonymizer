<?php

declare(strict_types=1);

namespace PHPSharkTank\Anonymizer\Handler;

use PHPSharkTank\Anonymizer\Exception\LogicException;

class CallbackHandler implements HandlerInterface
{
    public function getName(): string
    {
        return 'callback';
    }

    /**
     * @param array<string, mixed> $options
     */
    public function process(mixed $value, array $options): mixed
    {
        if (isset($options['method'])) {
            $callable = [$value, $options['method']];
            if (\is_callable($callable)) {
                return $callable();
            }
        }

        $callback = $options['callback'] ?? null;

        if (!\is_callable($callback)) {
            throw new LogicException('The callback handler requires either a public "method" of the object or a callable "callback" option.');
        }

        return $callback();
    }
}
