<?php

namespace App\DTOs;

use JsonSerializable;

abstract class BaseDTO implements JsonSerializable
{
    abstract public function toArray(): array;

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public static function fromArray(array $data): static
    {
        return new static(...$data);
    }
}
