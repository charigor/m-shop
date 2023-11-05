<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeatureValueLang extends Model
{
    use HasFactory;

    public $table = 'feature_value_lang';

    public $timestamps = false;

    protected $fillable = [
        'locale',
        'value',
    ];

    public function featureValue(): BelongsTo
    {
        return $this->belongsTo(FeatureValue::class);
    }
}
