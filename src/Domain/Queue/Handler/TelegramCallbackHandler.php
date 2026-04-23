<?php

declare(strict_types=1);


namespace App\Domain\Queue\Handler;

use App\Web\Telegram\Handler;
use Yiisoft\Queue\Message\MessageInterface;

final readonly class TelegramCallbackHandler
{
    public function __construct(
        private Handler $handler
    ) {}

    public function __invoke(MessageInterface $message): void
    {
        $this->handler->handle($message->getData());
    }
}
