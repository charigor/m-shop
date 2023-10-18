<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AttributeGroup extends Model
{
    use HasFactory;
    public $table = 'attribute_groups';
    public $timestamps = false;
    protected $fillable = [
        'is_color_group',
        'group_type',
        'position'
    ];
    const  GROUP_TYPE = [
        0 => 'radio',
        1 => 'color',
        2 => 'select'
    ];
    const  IS_COLOR_GROUP = [
        0 => 'no',
        1 => 'yes',
    ];

    /**
     * @return HasMany
     */
    public function translation(): HasMany
    {
        return $this->hasMany(AttributeGroupLang::class);
    }

    /**
     * @return HasMany
     */
    public function attributes(): HasMany
    {
        return $this->hasMany(Attribute::class);
    }
    public function translate()
    {
        return $this->hasOne(AttributeGroupLang::class)->whereLocale(app()->getLocale());
    }
}
