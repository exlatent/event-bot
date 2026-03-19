<?php

declare(strict_types=1);

namespace App\Model;

abstract class AbstractEntity
{
    public ?int $id = null;

    public function __construct(?int $id = null)
    {
        $this->id = $id;
    }
}
