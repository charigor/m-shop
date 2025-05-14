<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Kalnoy\Nestedset\NodeTrait;
use Laravel\Scout\Searchable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Category extends Model implements HasMedia
{
    use HasFactory,InteractsWithMedia,NodeTrait;
    //        Searchable {
    //    \Laravel\Scout\Searchable::usesSoftDelete insteadof \Kalnoy\Nestedset\NodeTrait;
    // }

    public $table = 'categories';

    protected $appends = ['menu_thumbnail_url'];

    protected $fillable = [
        'active',
        'parent_id',
    ];

    const ACTIVE = [
        0 => 'Inactive',
        1 => 'Active',
    ];

    const MODEL_NAME = 'category';

    public function registerMediaConversions(?Media $media = null): void
    {
        $this
            ->addMediaConversion('preview')
            ->nonQueued();

        $this
            ->addMediaConversion('thumbnail')
            ->width(100)
            ->height(100)
            ->nonQueued();
    }

    // Then update your accessor to use this conversion
    public function getMenuThumbnailUrlAttribute()
    {
        $media = $this->getFirstMedia('menu_thumbnail');

        if ($media) {
            return $media->getUrl('preview'); // Use the thumbnail conversion
        }

        return null;
    }

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime:d-m-Y',
    ];

    public function translation(): HasMany
    {
        return $this->hasMany(CategoryLang::class);
    }

    /**
     * @return Model|null
     */
    //    public function getTranslateAttribute(): Model|null
    //    {
    //        return $this->translation()->where('locale',app()->getLocale())?->first();
    //    }

    public function scopeActive(Builder $query): void
    {
        $query->where('categories.active', array_search('Active', self::ACTIVE));
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class);
    }

    public function getProductsIdsAttribute()
    {
        return $this->products->pluck('product_id');
    }

    public function translate($lang = null)
    {
        return $this->hasOne(\App\Models\CategoryLang::class)->whereLocale($lang ?? app()->getLocale());
    }

    public function translateWithFallback($locale = null)
    {
        $currentLocale = $locale ?? app()->getLocale();
        $fallbackLocale = config('app.fallback_locale');

        return $this->hasOne(CategoryLang::class)
            ->where(function ($query) use ($currentLocale, $fallbackLocale) {
                $query->where('locale', $currentLocale)
                    ->orWhere('locale', $fallbackLocale);
            })
            ->orderByRaw('CASE WHEN locale = ? THEN 0 ELSE 1 END', [$currentLocale]);
    }

    public function getTranslateWithFallbackAttribute()
    {
        $currentLocale = app()->getLocale();
        $fallbackLocale = config('app.fallback_locale');

        $translation = $this->translation()->where('locale', $currentLocale)->first();

        if (! $translation) {
            $translation = $this->translation()->where('locale', $fallbackLocale)->first();
        }

        return $translation;
    }

    public function ancestors()
    {
        return $this->newQuery()
            ->whereRaw('_lft < ? and _rgt > ?', [$this->_lft, $this->_rgt])
            ->orderBy('_lft');
    }
}
