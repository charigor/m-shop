<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

class CategoryLang extends Model
{
    use HasFactory, Sluggable;
    public $table = 'category_lang';
    public $timestamps = false;

    protected $fillable = [
        'title',
        'link_rewrite',
        'locale',
        'description',
        'meta_title',
        'meta_description',
        'meta_keywords'
    ];

    public function sluggable(): array
    {
        return [
            'link_rewrite' => [
                'source' => 'title'
            ]
        ];
    }

//    /**
//     * @return BelongsTo
//     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

}
