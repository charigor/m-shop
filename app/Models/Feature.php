<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Feature extends Model
{
    use HasFactory;
    public $table = 'features';

    public $timestamps = false;

    protected $fillable = [
        'guard_name',
        'position'
    ];
    protected $with = [
        'translate'
    ];
    /**
     * @return HasMany
     */
    public function translation(): HasMany
    {
        return $this->hasMany(FeatureLang::class);
    }
    /**
     * @return mixed
     */
    public function translate(): mixed
    {
        return $this->hasOne(FeatureLang::class)->whereLocale(app()->getLocale());
    }
    /**
     * @return HasMany
     */
    public function featureValue(): HasMany
    {
        return $this->hasMany(FeatureValue::class);
    }
    /**
     * @return BelongsToMany
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class)->withPivot('feature_value_id');
    }
    /**
     * @return Model|null
     */
    public function getTranslateAttribute(): Model|null
    {
        return $this->translation()->where('locale',app()->getLocale())?->first();
    }

}
