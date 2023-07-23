<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Feature extends Model
{
    use HasFactory;
    public $table = 'features';

    public $timestamps = false;

    protected $fillable = [
        'position'
    ];
    /**
     * @return HasMany
     */
    public function translation(): HasMany
    {
        return $this->hasMany(FeatureLang::class);
    }
}
