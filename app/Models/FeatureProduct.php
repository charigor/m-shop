<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class FeatureProduct extends Model
{
    use HasFactory;
    public $table = 'feature_product';
    public $timestamps = false;
    protected $fillable = [
        'product_id',
        'feature_id',
        'feature_value_id',
    ];
    public function products()
    {
        return $this->hasMany(Product::class,'product_id');
    }

//    public static function getData($where = null)
//    {
//        $query =  DB::table('feature_product');
//        if(!is_null($where )) {
//            foreach($where as $k => $v){
//                $query->where($k, $v);
//            }
//        }
//        return $query->get();
//    }
}
