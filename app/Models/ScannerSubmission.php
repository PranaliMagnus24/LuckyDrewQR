<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ScannerSubmission extends Model
{
     protected $fillable = [
        'unique_id',
        'name',
        'email',
        'phone',
    ];

    // Optionally auto-generate uuid on creating (if you prefer)
    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->unique_id)) {
                $model->unique_id = (string) Str::uuid();
            }
        });
    }
}
