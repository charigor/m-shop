<?php


namespace App\Services\Crud\Product;


use App\Enums\ProductTypeEnum;
use App\Models\AttributeProduct;
use App\Models\Product;
use App\Services\BaseCrudService;
use App\Services\Datatables\Products\Products;
use App\Services\TranslationService;
use Cviebrock\EloquentSluggable\Services\SlugService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ProductService extends BaseCrudService
{
    public function __construct()
    {
        parent::__construct(new Product());
    }

    /**
     * @param $request
     * @param null $params
     * @return mixed
     */
    public function getItems($request, $params = null): mixed
    {
        return (new Products)->table($request);
    }

    /**
     * @param $request
     * @return Builder|Model|void
     */
    public function createItem($request)
    {
        $data = $request->validated();
        $model = $this->model::create($data);
        $prepareData = (new TranslationService)->prepareFields($data['lang'], ['name', 'link_rewrite']);
        $model->translation()->createMany($prepareData);
        $this->addMedia($model, $data, ['image']);
        if ($data['categories']) $model->categories()->attach($data['categories']);
        if ($data['features']) $this->createUpdateProductFeature($model, $data);
        if ($data['attributes']) {
            $attributeProduct = $this->createAttribute($model,true);
            $attributeProduct->attributes()->attach($data['attributes']);
        }
        $model->refresh();
        return $model;
    }

    /**
     * @param $model
     * @param $request
     * @return mixed
     */
    public function updateItem($model, $request): mixed
    {
        $data = $request->validated();
        $model->update($data);

        $prepareData = (new TranslationService)->prepareFields($data['lang'], ['name', 'link_rewrite']);
        foreach ($prepareData as $item) {
            $model->translation()->updateOrCreate(['locale' => $item['locale']], $item);
        };
        $model->categories()->sync($data['categories']);

        $this->createUpdateProductFeature($model, $data);
        $this->addMedia($model, $data, ['image']);
        $this->removeMedia($model, $data, ['image']);
        if ($data['attributes']) {
            $attributeProduct = $this->createAttribute($model);
            $attributeProduct->attributes()->syncWithoutDetaching($data['attributes']);
        }

        if(isset($data['default_attr'])){
            $model->attributes()->where('id','=',$data['default_attr'])->update(['default' => 1]);
            $model->attributes()->where('id','<>',$data['default_attr'])->update(['default' => null]);
        }
        $model->refresh();

        return $model;


    }

    /**
     * @param $model
     * @param $slug_field
     * @param $slug_from
     */
    public function setSlug($model, $slug_field, $slug_from): string
    {
        return $slug_from ? SlugService::createSlug($model, $slug_field, $slug_from) : "";
    }

    /**
     * @param $model
     * @param $data
     * @param array $collections
     */
    public function removeMedia($model, $data, array $collections = []): void
    {
        foreach ($collections as $name) {
            foreach ($model->getMedia($name) as $media) {
                if (!in_array($media->file_name, $data[$name])) {
                    if ($media->custom_properties['main_image']) {
                        if ($m = $model->getMedia($name)->filter(fn($value) => $value->custom_properties['order'] === $media->custom_properties['order'] + 1)->first()) {
                            $m->setCustomProperty('main_image', 1);
                            $m->setCustomProperty('order', 0);
                            $m->save();
                            if ($model->attributes->count()) {
                                foreach($model->attributes as $attr)
                                {
                                    $m_attr = $attr->getMedia($name)->where('name', $m->name)->first();
                                    $m_attr->setCustomProperty('main_image', 1);
                                    $m_attr->setCustomProperty('order', 0);
                                    $m_attr->save();
                                }

                            }

                        }
                    }
                    $name_media = $media->name;
                    $media->delete();
                    $model->attributes()->each(fn($item) => $item->getMedia('image')->each(fn($media) => ($media->name === $name_media) ? $media->delete() : ''));
                }
            }
        }

    }

    /**
     * @param $model
     * @param $data
     * @param array $collections
     */
    public function addMedia($model, $data, array $collections = []): void
    {
        foreach ($collections as $name) {
            foreach ($data[$name] as $key => $file) {
                if (file_exists(storage_path('app/public/tmp/uploads/' . $file))) {
                    $media = $model->addMedia(storage_path('app/public/tmp/uploads/' . $file))->withCustomProperties(['order' => ($data['main_image'] === $file) ? 0 : $key + 1, 'main_image' => ($data['main_image'] === $file) ? 1 : 0])->toMediaCollection($name);
                    if ($model->attributes->count()) {
                        $this->copyMediaToAttributes($model, $media, $name, $data['main_image']);
                    }
                } else {
                    foreach ($model->getMedia($name) as $media) {
                        if ($media->file_name === $file) {
                            $media->setCustomProperty('main_image', ($data['main_image'] === $file) ? 1 : 0);
                            $media->setCustomProperty('order', ($data['main_image'] === $file) ? 0 : $key + 1);
                            $media->save();
                            $media->refresh();
                            if ($model->attributes->count()) {
                                $this->copyMediaToAttributes($model, $media, $name, $data['main_image']);
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * @param $model
     * @param $media
     * @param $collection_name
     * @param $main
     * @return void
     */
    public function copyMediaToAttributes($model, $media, $collection_name, $main = null): void
    {
        foreach ($model->attributes as $attributeProduct) {
            if (!$attributeProduct->getMedia($collection_name)->where('name', $media->name)->first()) {
                $m = $media->copy($attributeProduct, $collection_name);
                $m->setCustomProperty('relative_id', $media->id);
                $m->setCustomProperty('active', 1);
                $m->save();
            }
            if ($main) {
                foreach ($attributeProduct->getMedia($collection_name) as $key => $el_media) {
                    $el_media->setCustomProperty('main_image', ($main === $el_media->file_name) ? 1 : 0);
                    $el_media->setCustomProperty('order', ($main === $el_media->file_name) ? 0 : $key + 1);
                    $el_media->save();
                }
            }
        }
    }
    /**
     * @param $model
     * @param $data
     * @return void
     */
    public function createUpdateProductFeature($model, $data): void
    {
        $arr = [];
        foreach ($data['features'] as $item) {
            $arr[$item['feature_value_id']] = ['feature_id' => $item['feature_id']];
        }
        $model->features()->sync($arr);
    }

    /**
     * @param $model
     * @return AttributeProduct $attributeProduct
     */
    public function createAttribute($model,$default = null): AttributeProduct
    {
        if(!$model->attributes()->count()) $default = 1;
        $attributeProduct = $model->attributes()->create(['default' => $default]);
        foreach ($model->getMedia('image') as $media) {
            $m = $media->copy($attributeProduct, 'image');
            $m->setCustomProperty('active', 1);
            $m->save();
        }
        return $attributeProduct;
    }
    public function removeAttributes($model)
    {
        $model->type = ProductTypeEnum::Regular;
        $model->save();
        return  $model->attributes()->each(fn($value) => $value->delete());
    }
}
