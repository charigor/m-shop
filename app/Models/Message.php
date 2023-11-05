<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    public $table = 'messages';

    protected $fillable = ['user_id', 'body'];

    protected $casts = [
        'created_at' => 'datetime:d-m-Y',
    ];
}
