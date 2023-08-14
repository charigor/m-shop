<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttributeLang extends Model
{
    use HasFactory;
    public $table = 'attribute_lang';

    public $timestamps = false;

    protected $fillable = [
        'locale',
        'name',
    ];
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function attribute(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Attribute::class);
    }
}
