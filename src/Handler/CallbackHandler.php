<?php

declare(strict_types=1);

namespace PHPSharkTank\Anonymizer\Handler;

class CallbackHandler implements HandlerInterface
{
    public function getName(): string
    {
        return 'callback';
    }

    public function process(mixed $value, array $options): mixed
    {
        if (isset($options['method'])) {
            $callable = [$value, $options['method']];
            if (is_callable($callable)) {
                return $callable();
            }
        }

        return call_user_func($options['callback']);
    }
}
