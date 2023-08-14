<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeatureLang extends Model
{
    use HasFactory;

    public $table = 'feature_lang';

    public $timestamps = false;

    protected $fillable = [
        'locale',
        'name',
    ];

    /**
     * @return BelongsTo
     */
    public function feature(): BelongsTo
    {
        return $this->belongsTo(Feature::class);
    }
}
