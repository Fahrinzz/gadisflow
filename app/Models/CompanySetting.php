<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanySetting extends Model
{
    protected $guarded = [];

    /**
     * Return the single settings row, creating a default one if needed.
     */
    public static function current(): self
    {
        return static::firstOrCreate(['id' => 1]);
    }
}
