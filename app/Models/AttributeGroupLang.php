<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttributeGroupLang extends Model
{
    use HasFactory;

    public $table = 'attribute_group_lang';

    public $timestamps = false;

    protected $fillable = [
        'locale',
        'name',
        'public_name',
    ];

    public function attributeGroup(): BelongsTo
    {
        return $this->belongsTo(AttributeGroup::class);
    }
}
