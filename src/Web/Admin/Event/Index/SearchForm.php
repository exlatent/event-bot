<?php

declare(strict_types=1);


namespace App\Web\Admin\Event\Index;

use App\Shared\ApplicationDateTime;
use Yiisoft\FormModel\FormModel;

class SearchForm extends FormModel
{
    public ?string $query = null;
    public ?string $state = null;
    public ?string $dateFrom = null;
    public ?string $dateTo = null;
    public ?bool $hasDuplicates = null;

    public function load(array $data): void
    {
        $this->query = $data['query'] ?? null;
        $this->state = isset($data['state']) && is_numeric($data['state']) ? $data['state'] : null;
        $this->dateFrom = !empty($data['dateFrom']) ? ApplicationDateTime::toDb(ApplicationDateTime::fromInput($data['dateFrom'])) : null;
        $this->dateTo =  !empty($data['dateTo']) ? ApplicationDateTime::toDb(ApplicationDateTime::fromInput($data['dateTo'])) : null;
        $this->hasDuplicates = isset($data['hasDuplicates'])
            ? (bool)$data['hasDuplicates']
            : null;
    }

}
