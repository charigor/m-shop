<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class AttributeProduct extends Model implements HasMedia
{
    use HasFactory,InteractsWithMedia;

    public $table = 'attribute_products';

    public $fillable = [
        'quantity',
        'price',
        'cost',
        'rebate',
        'width',
        'height',
        'depth',
        'weight',
        'default',
        'reference',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function attributes(): BelongsToMany
    {
        return $this->belongsToMany(Attribute::class);
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this
            ->addMediaConversion('preview')
            ->nonQueued();
    }

    public function getMainImageAttribute()
    {
        return $this->getMedia('image', ['main_image' => '1'])->first();
    }

    public function getSortedMediaAttribute()
    {
        return $this->getMedia('image', ['active' => '1'])
            ->sortBy(fn ($value) => $value->custom_properties['order']);
    }

    public function getLocalizedAttributes($locale = null)
    {
        $locale = $locale ?? app()->getLocale();

        $result = [];

        $attributes = $this->attributes()->with('translate', 'group.translate')->get();

        // Group attributes by their group
        $groupedAttributes = $attributes->groupBy(function ($attribute) {
            return $attribute->group->id;
        });

        foreach ($groupedAttributes as $groupId => $attributes) {
            // Get the group name
            $groupName = $attributes->first()->group->translate?->name
                ?? $attributes->first()->group->translation()
                    ->where('locale', config('app.fallback_locale'))
                    ->first()?->name
                ?? 'Unnamed Group';

            $attributeValues = [];

            foreach ($attributes as $attribute) {
                $attrName = $attribute->translate?->name
                    ?? $attribute->translation()
                        ->where('locale', config('app.fallback_locale'))
                        ->first()?->name
                    ?? 'Unnamed Attribute';

                $attributeValues[] = [
                    'id' => $attribute->id,
                    'name' => $attrName,
                    'color' => $attribute->color,
                ];
            }

            $result[] = [
                'group_id' => $groupId,
                'group_name' => $groupName,
                'attributes' => $attributeValues,
            ];
        }

        return $result;
    }
}
