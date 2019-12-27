<?php

declare(strict_types=1);

namespace PHPSharkTank\Anonymizer\Tests\Handler;

use PHPSharkTank\Anonymizer\Annotation as Anonymize;
use PHPSharkTank\Anonymizer\Handler\CallbackHandler;
use PHPUnit\Framework\TestCase;

class CallbackHandlerTest extends TestCase
{
    /**
     * @var CallbackHandler
     */
    private $handler;

    protected function setUp(): void
    {
        $this->handler = new CallbackHandler();
    }

    public function testGetName()
    {
        self::assertSame('callback', $this->handler->getName());
    }

    public function testProcessWithMethod()
    {
        $user = $this->prophesize(User::class);
        $user->removeName()->shouldBeCalled();

        $this->handler->process($user->reveal(), [
            'method' => 'removeName',
        ]);
    }

    public function testProcessWitHCallback()
    {
        $value = new \stdClass();
        $callback = function () {
            self::assertTrue(true);
        };

        $this->handler->process($value, [
            'callback' => $callback,
        ]);
    }
}

/**
 * @Anonymize\EnableAnonymize
 */
class User
{
    /**
     * @var string|null
     * @Anonymize\AnonymizeValue(type="callback", options={"method":"removeName"})
     */
    private $name = 'Name';

    public function removeName(): void
    {
        $this->name = null;
    }
}
