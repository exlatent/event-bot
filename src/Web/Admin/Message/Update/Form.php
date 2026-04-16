<?php

declare(strict_types=1);


namespace App\Web\Admin\Message\Update;

use App\Domain\Telegram\Message;
use Symfony\Contracts\Service\Attribute\Required;
use Yiisoft\FormModel\FormModel;
use Yiisoft\Validator\Rule\Integer;
use Yiisoft\Validator\Rule\Type\FloatType;

final class Form extends FormModel
{
    #[Required]
    #[Integer]
    public int $event_candidate;

    #[Integer]
    #[Required]
    public int $spam;

    #[Integer]
    #[Required]
    public int $off_topic;

    #[Required]
    #[FloatType]
    public float $confidence;

    public function __construct(
        public Message $model,
    ) {
        $this->event_candidate = $this->model->event_candidate ?? 0;
        $this->spam = $this->model->spam ?? 0;
        $this->off_topic = $this->model->off_topic ?? 0;
        $this->confidence = $this->model->confidence ?? 0;
    }
}
