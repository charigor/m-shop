<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lang extends Model
{
    use HasFactory;

    public $table = 'langs';

    protected $fillable = [
        'name',
        'code',
        'active',
        'date_format',
        'date_format_full',
    ];

    const  DATE_FORMAT = [
        1 => 'Y-m-d',
        2 => 'd/m/Y',
        3 => 'm/d/Y',
    ];

    const  DATE_FORMAT_FULL = [
        1 => 'Y-m-d H:i:s',
        2 => 'd/m/Y H:i:s',
        3 => 'm/d/Y H:i:s',

    ];

    const  ACTIVE = [
        0 => 'Inactive',
        1 => 'Active',
    ];
}
