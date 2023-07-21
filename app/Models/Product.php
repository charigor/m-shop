<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Product extends Model implements HasMedia
{
    use HasFactory,Searchable,InteractsWithMedia;
    public $table = 'products';
    protected $fillable = [
        'active',
        'name',
        'description',
        'category_id'
    ];
    protected $with = [
        'category.translation'
    ];

    public function registerMediaConversions(Media $media = null): void
    {
        $this
            ->addMediaConversion('preview')
            ->nonQueued();
    }
    public function category(){
       return  $this->belongsTo(Category::class,'category_id','id');
    }
    public function toSearchableArray(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'category' => [
                'trans' => $this->category->translation,
            ]
        ];
    }

}
