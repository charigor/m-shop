<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Scout\Searchable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Product extends Model implements HasMedia
{
    use HasFactory,InteractsWithMedia,Searchable;
    public $table = 'products';

    public const SORTABLE = [
      'id','price','created_at'
    ];

    public const FILTERABLE = [
        'category','price','feature','brand'
    ];
    protected $fillable = [
        'brand_id',
        'tax_id',
        'quantity',
        'reference',
        'description',
        'price',
        'unity',
        'unit_price_ratio',
        'width',
        'height',
        'depth',
        'weight',
        'active',
        'type'
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

    protected $with = [
        'translate'
    ];

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
    public function attributes(): HasMany
    {
        return $this->hasMany(AttributeProduct::class);
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
        return $this->belongsToMany(FeatureValue::class)->withPivot('feature_id');
    }
    public function featureValues(): BelongsToMany
    {
        return $this->belongsToMany(Feature::class,'feature_value_product')->withPivot('feature_value_id');
    }


    /**
     * @param Builder $query
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('active', array_search('Active',self::ACTIVE));
    }

    /**
     * @return array
     */

    public function toSearchableArray(): array
    {
        return [
            'price' => $this->price * 1000000,
            'category' => $this->categories()->pluck('id')->toArray(),
            'brand' => $this->brand()->pluck('name')->first(),
            'feature' =>  $this->featureValues()->get()->groupBy('guard_name')
                ->map(function($item){
                    return $item->map(function($i) use($item){
                        return  $i->pivot->feature_value_id;
                    })->flatten()->toArray();
                }),
            'created_at' => $this->created_at
        ];
    }

    /**
     * @return mixed
     */
    public function translate(): mixed
    {
        return $this->hasOne(ProductLang::class)->whereLocale(app()->getLocale());
    }

    /**
     * @return string
     */
    public function searchableAs(): string
    {
        return 'products';
    }
    protected function makeAllSearchableUsing($query): Builder
    {
        return $query->with(['categories','features']);
    }


    public function getMainImageAttribute()
    {
        return  $this->getMedia('image', ['main_image' => 1])->first();
    }
    public function getSortedMediaAttribute()
    {
        return  $this->getMedia('image')->sortBy(fn($value) => $value->custom_properties['order']);
    }
}
