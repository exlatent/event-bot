<?php

declare(strict_types=1);


namespace App\Web\Admin\Source\Update;

use App\Domain\Telegram\Source;
use Symfony\Contracts\Service\Attribute\Required;
use Yiisoft\FormModel\FormModel;
use Yiisoft\Hydrator\Attribute\Parameter\Trim;

final class Form extends FormModel
{
    #[Trim]
    #[Required]
    public string $username;

    #[Trim]
    #[Required]
    public string $title;

    #[Trim]
    #[Required]
    public string $is_active;

    public function __construct(
        public Source $model,
    ) {
        $this->username = $this->model->username;
        $this->title = $this->model->title;
        $this->is_active = (string)$this->model->is_active;
    }
}
