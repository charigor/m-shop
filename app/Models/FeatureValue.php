<?php

namespace App\Models;

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
        'custom',
    ];

    protected $with = [
        'translate',
    ];

    public function translation(): HasMany
    {
        return $this->hasMany(FeatureValueLang::class);
    }

    public function translate(): mixed
    {
        return $this->hasOne(FeatureValueLang::class)->whereLocale(app()->getLocale());
    }

    public function feature(): BelongsTo
    {
        return $this->belongsTo(Feature::class, 'feature_id');
    }

    public function getTranslateAttribute(): ?Model
    {
        return $this->translation()->where('locale', app()->getLocale())?->first();
    }

    public function featureValuesProduct(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'feature_product')->withPivot('feature_id');
    }
}
