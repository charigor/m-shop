<?php

namespace App\Http\Resources\Category;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResourceIndex extends JsonResource
{


    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $defaultData = [
            'id'      => $this['id'],
            'active'  => Category::ACTIVE[$this['active']],
            'title'  => $this['title'],
            'created_at'  => isset($this->created_at) ? $this->created_at->format('d-m-Y h:m:s')  : null,
        ];

        $additionalData = [
            'translation' => $this->translation->keyBy('locale')
        ];

        return array_merge($defaultData,$additionalData);

    }
}
