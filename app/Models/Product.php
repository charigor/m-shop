<?php

namespace App\Models;

use App\Services\Datatables\FeatureValues\FeatureValues;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Scout\Searchable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Product extends Model implements HasMedia
{
    use HasFactory,InteractsWithMedia;
    public $table = 'products';

    protected $fillable = [
        'brand_id',
        'tax_id',
        'quantity',
        'reference',
        'price',
        'unity',
        'unit_price_ratio',
        'width',
        'height',
        'depth',
        'weight',
        'active',
    ];
    const  ACTIVE = [
        0 => 'Inactive',
        1 => 'Active',
    ];
    const  TAXES = [
        ['id' => '1','name' => 'Без налогу','value' => 0],
        ['id' => '2','name' => 'Ндс 5%','value' => 5],
        ['id' => '3','name' => 'Ндс 7%','value' => 7],
        ['id' => '4','name' => 'Ндс 20%','value' => 20],
    ];
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime:d-m-Y h:m:s',
        'updated_at' => 'datetime:d-m-Y h:m:s',
    ];

//    protected $with = [
//        'category.translation'
//    ];

    public function registerMediaConversions(Media $media = null): void
    {
        $this
            ->addMediaConversion('preview')
            ->nonQueued();
    }

    /**
     * @return BelongsTo
     */
    public function brand(): BelongsTo
    {
        return  $this->belongsTo(Brand::class);
    }
    /**
     * @return HasMany
     */
    public function translation(): HasMany
    {
        return $this->hasMany(ProductLang::class);
    }
    /**
     * @return Model|null
     */
    public function getTranslateAttribute(): Model|null
    {
        return $this->translation()->where('locale',app()->getLocale())?->first();
    }
    /**
     * @return BelongsToMany
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }
    /**
     * @return BelongsToMany
     */
    public function features(): BelongsToMany
    {
        return $this->belongsToMany(Feature::class)->withPivot('feature_value_id');
    }
    public function featureValues(): BelongsToMany
    {
        return $this->belongsToMany(FeatureValue::class,'feature_product')->withPivot('feature_id');
    }

    /**
     * @param Builder $query
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('active', array_search('Active',self::ACTIVE));
    }
//    public function toSearchableArray(): array
//    {
//        return [
//            'name' => $this->name,
//            'description' => $this->description,
//            'category' => [
//                'trans' => $this->category->translation,
//            ]
//        ];
//    }

}
