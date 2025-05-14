<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Получаем текущую локаль или используем локаль по умолчанию
        $locale = $request->header('Content-Language') ?? app()->getLocale();

        $featureGroups = [];

        // Группируем характеристики по имени
        foreach ($this->features as $featureValueObj) {
            $feature = $featureValueObj->feature;

            // Получаем перевод для текущей локали
            $featureName = $feature->translation->where('locale', $locale)->first()?->name;
            $featureValueStr = $featureValueObj->translation->where('locale', $locale)->first()?->value;

            // Если нет перевода для текущей локали, пропускаем
            if (! $featureName || ! $featureValueStr) {
                continue;
            }

            // Если такой группы еще нет, создаем ее
            if (! isset($featureGroups[$featureName])) {
                $featureGroups[$featureName] = [
                    'name' => $featureName,
                    'values' => [],
                ];
            }

            $featureGroups[$featureName]['values'][] = [
                'id' => $featureValueObj->id,
                'value' => $featureValueStr,
                'guard_name' => $feature->guard_name,
            ];
        }

        // Преобразуем хеш-таблицу в индексированный массив для вывода в JSON
        $features = array_values($featureGroups);

        return [
            'id' => $this->id,
            'name' => optional($this->translateWithFallback)->name,
            'price' => $this->price,
            'locale' => optional($this->translateWithFallback)->locale,
            'description' => optional($this->translateWithFallback)->description,
            'meta_description' => optional($this->translateWithFallback)->meta_description,
            'meta_keywords' => optional($this->translateWithFallback)->meta_keywords,
            'meta_title' => optional($this->translateWithFallback)->meta_title,
            'link_rewrite' => optional($this->translateWithFallback)->link_rewrite,
            'preview_url' => $this->media->first()?->preview_url,
            'attributes' => AttributeProductResource::collection($this->attributes),
            'images' => $this->media->map(function ($media) {
                return [
                    'id' => $media->id,
                    'url' => $media->url,
                    'preview_url' => $media->preview_url,
                    'thumbnail' => $media->thumbnail ?? $media->preview_url,
                    'position' => $media->position ?? 0,
                    'alt' => $media->alt ?? $this->name,
                ];
            }),
            'features' => $features,
        ];
    }
}
