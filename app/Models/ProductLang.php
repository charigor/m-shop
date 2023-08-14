<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductLang extends Model
{
    use HasFactory, Sluggable;
    public $table = 'product_lang';
    public $timestamps = false;
    protected $fillable = [
        'locale',
        'name',
        'description',
        'short_description',
        'link_rewrite',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'available_now',
        'available_later',
        'delivery_in_stock',
        'delivery_out_stock',
    ];

    public function sluggable(): array
    {
        return [
            'link_rewrite' => [
                'source' => 'name'
            ]
        ];
    }
    /**
     * @return BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
