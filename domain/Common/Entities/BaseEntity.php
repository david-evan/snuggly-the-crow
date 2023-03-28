<?php

namespace Domain\Common\Entities;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

abstract class BaseEntity extends Model
{
    protected function createdAt(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value === null ? null : (new Carbon($value))->format(DATE_RFC3339)
        );
    }

    protected function deletedAt(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value === null ? null : (new Carbon($value))->format(DATE_RFC3339)
        );
    }

    protected function updatedAt(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value === null ? null : (new Carbon($value))->format(DATE_RFC3339)
        );
    }
}
