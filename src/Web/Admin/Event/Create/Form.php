<?php

declare(strict_types=1);


namespace App\Web\Admin\Event\Create;

use App\Domain\Event\Event;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\FormModel\FormModel;
use Yiisoft\Hydrator\Attribute\Parameter\ToDateTime;
use Yiisoft\Hydrator\Attribute\Parameter\Trim;

final class Form extends FormModel
{
    #[Trim]
    #[Required]
    public string $title = '';

    #[Required]
    #[ToDateTime(format: 'php:Y-m-d H:i:s')]
    public string $datetime = '';

    #[Trim]
    #[Required]
    public string $location = '';

    #[Trim]
    #[Required]
    public string $price = '';

    public int $state = Event::STATE_DRAFT;

    public function getFormName(): string
    {
        return 'CreateForm';
    }
}
