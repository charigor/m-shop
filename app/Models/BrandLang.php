<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BrandLang extends Model
{
    use HasFactory;

    public $table = 'brand_lang';

    public $timestamps = false;

    protected $fillable = [
        'locale',
        'short_description',
        'description',
        'meta_title',
        'brand_id',
        'meta_description',
        'meta_keywords',
    ];

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }
}
