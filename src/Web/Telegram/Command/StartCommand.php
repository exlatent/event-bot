<?php

declare(strict_types=1);


namespace App\Web\Telegram\Command;

use Telegram\Bot\Api;
use Telegram\Bot\Keyboard\Keyboard;
use Yiisoft\Json\Json;

final class StartCommand
{
    public const int TODAY = 1;
    public const int TOMORROW = 2;
    public const int CURRENT_WEEK = 3;
    public const int NEXT_WEEK = 4;

    public function __construct(
        private Api $bot
    ) {
    }

    public function handle(array $message): void
    {
        $chatId = $message['chat']['id'];

        $this->bot->sendMessage([
            'chat_id'      => $chatId,
            'text'         => 'Привет! 👋 Нажми на кнопку и получи список событий',
            'reply_markup' =>
                Keyboard::make()
                    ->inline()
                    ->row([
                        Keyboard::inlineButton([
                            'text'          => 'Сегодня',
                            'callback_data' => Json::encode([
                                'action' => 'digest',
                                'period' => self::TODAY
                            ])
                        ]),
                        Keyboard::inlineButton([
                            'text'          => 'Завтра',
                            'callback_data' => Json::encode([
                                'action' => 'digest',
                                'period' => self::TOMORROW
                            ])
                        ])
                    ])
                    ->row([
                        Keyboard::inlineButton([
                            'text'          => 'На этой неделе',
                            'callback_data' => Json::encode([
                                'action' => 'digest',
                                'period' => self::CURRENT_WEEK
                            ])
                        ]),
                        Keyboard::inlineButton([
                            'text'          => 'На следующей неделе',
                            'callback_data' => Json::encode([
                                'action' => 'digest',
                                'period' => self::NEXT_WEEK
                            ])
                        ]),
                    ])
        ]);
    }
}
