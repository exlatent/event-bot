<?php

declare(strict_types=1);

namespace App\Infrastructure;

abstract class AbstractEntity
{
    public ?int $id = null;

    public function __construct(?int $id = null)
    {
        $this->id = $id;
    }
}
