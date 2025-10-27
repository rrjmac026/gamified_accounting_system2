<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    

    protected $fillable = [
        'key',
        'value',
        'type',
        'description',
        'is_editable'
    ];

    protected $casts = [
        'is_editable' => 'boolean'
    ];
}
