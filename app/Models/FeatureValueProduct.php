<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class FeatureValueProduct extends Model
{
    use HasFactory;
    public $table = 'feature_value_product';
    public $timestamps = false;
    protected $fillable = [
        'product_id',
        'feature_id',
        'feature_value_id',
    ];
    public function products()
    {
        return $this->hasMany(Product::class,'product_id');
    }
}
