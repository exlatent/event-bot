<?php

declare(strict_types=1);


namespace App\Web\Telegram\Widget;

use App\Web\Telegram\Period;
use Yiisoft\Json\Json;
use Telegram\Bot\Keyboard\Keyboard;

class KeyboardWidget
{
    public static function render($period = null, int $page = 1, bool $hasNext = false)
    {
        $keyboard = Keyboard::make()->inline();
        $nav_row = [];
        if($page > 1) {
            $nav_row[] = Keyboard::inlineButton([
                'text'          => '⬅️  Назад',
                'callback_data' => Json::encode([
                    'action' => 'digest',
                    'period' => $period,
                    'page'   => $page - 1
                ])
            ]);
        }
        if ($hasNext) {
            $nav_row[] = Keyboard::inlineButton([
                'text'          => '➡️ Далее',
                'callback_data' => Json::encode([
                    'action' => 'digest',
                    'period' => $period,
                    'page'   => $page + 1
                ])
            ]);
        }

        if(!empty($nav_row)) {
            $keyboard->row($nav_row);
        }

        $keyboard
            ->row([
                Keyboard::inlineButton([
                    'text'          => '🔥 Сегодня',
                    'callback_data' => Json::encode([
                        'action' => 'digest',
                        'period' => Period::TODAY,
                    ])
                ]),
                Keyboard::inlineButton([
                    'text'          => '🗓 Завтра',
                    'callback_data' => Json::encode([
                        'action' => 'digest',
                        'period' => Period::TOMORROW
                    ])
                ])
            ])
            ->row([
                Keyboard::inlineButton([
                    'text'          => '📆 На этой неделе',
                    'callback_data' => Json::encode([
                        'action' => 'digest',
                        'period' => Period::CURRENT_WEEK
                    ])
                ]),
                Keyboard::inlineButton([
                    'text'          => '🎉 На выходных',
                    'callback_data' => Json::encode([
                        'action' => 'digest',
                        'period' => Period::HOLIDAYS
                    ])
                ]),
            ]);

        return $keyboard;
    }

}
