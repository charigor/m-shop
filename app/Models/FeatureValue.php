<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FeatureValue extends Model
{
    public $table = 'feature_values';
    public $timestamps = false;
    protected $fillable = [
        'feature_id',
        'custom'
    ];
    protected $with = [
        'translate'
    ];

    /**
     * @return HasMany
     */
    public function translation(): HasMany
    {
        return $this->hasMany(FeatureValueLang::class);
    }
    /**
     * @return mixed
     */
    public function translate(): mixed
    {
        return $this->hasOne(FeatureValueLang::class)->whereLocale(app()->getLocale());
    }
    /**
     * @return BelongsTo
     */
    public function feature(): BelongsTo
    {
        return $this->belongsTo(Feature::class,'feature_id');
    }

    /**
     * @return Model|null
     */
    public function getTranslateAttribute(): Model|null
    {
        return $this->translation()->where('locale',app()->getLocale())?->first();
    }
    public function featureValuesProduct(): BelongsToMany
    {
        return $this->belongsToMany(Product::class,'feature_product')->withPivot('feature_id');
    }
}
