<?php

declare(strict_types=1);

namespace PHPSharkTank\Anonymizer\Tests\Handler;

use PHPSharkTank\Anonymizer\Annotation\EnableAnonymize;
use PHPSharkTank\Anonymizer\Annotation\Handler;
use PHPSharkTank\Anonymizer\Handler\CallbackHandler;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class CallbackHandlerTest extends TestCase
{
    use ProphecyTrait;

    private CallbackHandler $handler;

    protected function setUp(): void
    {
        $this->handler = new CallbackHandler();
    }

    public function testGetName(): void
    {
        self::assertSame('callback', $this->handler->getName());
    }

    public function testProcessWithMethod(): void
    {
        $user = $this->prophesize(User::class);
        $user->removeName()->shouldBeCalled();

        $this->handler->process($user->reveal(), [
            'method' => 'removeName',
        ]);
    }

    public function testProcessWitHCallback(): void
    {
        $value = new \stdClass();
        $callback = static function () {
            self::assertTrue(true);
        };

        $this->handler->process($value, [
            'callback' => $callback,
        ]);
    }
}

#[EnableAnonymize]
class User
{
    #[Handler('callback', options: ['method' => 'removeName'])]
    private ?string $name = 'Name';

    public function removeName(): void
    {
        $this->name = null;
    }
}
