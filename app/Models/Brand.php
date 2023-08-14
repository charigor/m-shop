<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Brand extends Model implements HasMedia
{
    use HasFactory,InteractsWithMedia,Sluggable;

    public $table = 'brands';

    protected $fillable = [
        'name',
        'slug',
        'active'
    ];
    const  ACTIVE = [
        0 => 'Inactive',
        1 => 'Active',
    ];

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }
    public function registerMediaConversions(Media $media = null): void
    {
        $this
            ->addMediaConversion('preview')
            ->nonQueued();
    }
    /**
     * @return HasMany
     */
    public function translation(): HasMany
    {
        return $this->hasMany(BrandLang::class);
    }
    /**
     * @return HasMany
     */
    public function products(): HasMany
    {
        return  $this->hasMany(Product::class);
    }
    /**
     * @return Model|null
     */
    public function getTranslateAttribute(): Model|null
    {
        return $this->translation()->where('locale',app()->getLocale())?->first();
    }

    /**
     * @param Builder $query
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('active', array_search('Active',self::ACTIVE));
    }
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime:d-m-Y h:m:s',
        'updated_at' => 'datetime:d-m-Y h:m:s',
    ];
}
