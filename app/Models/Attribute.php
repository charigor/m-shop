<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Attribute extends Model
{
    use HasFactory;

    public $table = 'attributes';
    public $timestamps = false;
    protected $fillable = [
        'attribute_group_id',
        'color',
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
        return $this->hasMany(AttributeLang::class);
    }
    /**
     * @return BelongsTo
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(AttributeGroup::class,'attribute_group_id');
    }
    public function translate()
    {
        return $this->hasOne(AttributeLang::class)->whereLocale(session('locale') ?? app()->getLocale());
    }
}
