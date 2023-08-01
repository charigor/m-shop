<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeatureProduct extends Model
{
    use HasFactory;
    public $table = 'feature_product';
    public $timestamps = false;
    protected $fillable = [
        'product_id',
        'feature_id',
        'feature_value_id',
    ];
}
