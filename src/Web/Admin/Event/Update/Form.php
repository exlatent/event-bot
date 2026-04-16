<?php

declare(strict_types=1);


namespace App\Web\Admin\Event\Update;

use App\Domain\Event\Event;
use App\Shared\ApplicationDateTime;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\FormModel\FormModel;
use Yiisoft\Hydrator\Attribute\Parameter\ToDateTime;
use Yiisoft\Hydrator\Attribute\Parameter\Trim;


final class Form extends FormModel
{
    #[Trim]
    #[Required]
    public string $title;

    #[Required]
    #[ToDateTime(format: 'php:Y-m-d H:i:s')]
    public string $datetime;

    #[Trim]
    #[Required]
    public string $location;

    #[Trim]
    #[Required]
    public string $price;

    #[Required]
    public int $state;

    public function __construct(
        public Event $model,
    ) {
        $this->title = $this->model->title;
        $this->datetime = $this->model->datetime;
        $this->location = $this->model->location;
        $this->price = $this->model->price;
        $this->state = (int)$this->model->state;
    }

    public function getFormName(): string
    {
        return 'UpdateForm';
    }
}
