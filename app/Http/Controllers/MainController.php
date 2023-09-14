<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Admin\Traits\MediaUploadingTrait;
use App\Models\Brand;
use App\Models\Product;
use App\Models\User;
use App\Notifications\TestNotify;

use App\Services\Filter\BrandSearchRepository;
//use App\Services\Filter\SearchRepository;
use App\Services\Test\TestService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Notification;
use Inertia\Inertia;

class MainController extends Controller
{
    //
    public function test(TestService $test,int $delta)
    {
        var_dump($test->start() + $delta);
    }
    public function checkTicket($str,$k){
           $n =  $k / 2;
           $firstPart = substr($str,0,$n);
           $lastPart = substr($str,-$n);
           return $this->getSum($firstPart) === $this->getSum($lastPart);
    }
    public function getSum($str){
        $count = 0;
        $result = 0;
        while($count < strlen($str)){
            $result += (int) $str[$count];
            $count++;
        }
        return $result;
    }
    public function it($min,$max,$k){

        $min = number_format(($min / 10** $k),$k);

        $max = number_format(($max / 10** $k),$k);
        $res_m = $min;
        $c = 1;

        while($res_m <= $max){
            $res_m = number_format(($c / (10**$k)),$k);
            if($this->checkTicket(substr($res_m,2),$k)){
                yield 1;
            }
            $c++;

        }
    }
    public function luckyTickets($k) {
        if($k % 2 != 0){
            dd ("Число може бути тільки парним");
        }
        $min = null;
        $max = null;
        $result = 0;

        for($i  = $k;$i > 0; $i--){
            $min.= '0';
            $max.= '9';
        }

        foreach($this->it($min,$max,$k) as $val){
             $result += $val;
        } ;

        return $result;
    }
    public function index(Request $request,BrandSearchRepository $searchRepository){

        echo memory_get_usage() . "\n";
        echo '<pre>';
        $btime = microtime(true);
        echo $this->luckyTickets(6);
        echo '('.round(microtime(true) - $btime, 4).' сек.)';
        echo '</pre>';
        echo memory_get_usage() . "\n";
//        $arr = [1,2,3,4,5,6,7,8,9];
//
//        $odd = max($arr);
//
//        echo "Нечетные:\n";
//        print_r($odd);
//
//        $even = array_filter($arr, function($x) { return $x % 2 === 0;});
//
//        echo "Четные:\n";
//        print_r($even);

        $str = '❤hello world';
        $r = '';
        for ($i = mb_strlen($str); $i>=0; $i--) {
            $r .= mb_substr($str, $i, 1);
        }
        print_r( $r);


//        $start = microtime(true);
//        $array = [];
//        for($r = 0; $r < 10000; $r++){
//            $array[] = $r;
//        }
//        $arr = [102,10,20,44,5,90,11,4,35,40,44,11,10,12,56,78,90,87,66,66,77];
//        dd($this->quicksort($arr));
//        dd($this->search(340, $array));

//        $end = microtime(true);
//        $time = number_format(($end - $start), 2);
//        echo 'This page loaded in ', $time, ' seconds';
//        $enrollmentData = [
//            'body' => 'some body',
//            'text' => 'some text',
//            'name' => 'igor',
//            'url' => url('/'),
//            'thanks' => 'thanks',
//        ];
//        $user = User::find(1);
//        Notification::send(auth()->user(), new TestNotify($enrollmentData));
//        $user->notify(new TestNotify($enrollmentData));
    $products =  Product::with(['media','translation' => function($query){
        $query->where('locale',app()->getLocale());
    }])->get();
        return view('front.main', [
            'brands' => request()->has('q')
                ? $searchRepository->search(request('q'))
                : Brand::all(),
            'products' =>   $products
        ]);

//        return Inertia::render('MainView', [
//            'data' =>  $request->get('search') ? Product::search(
//                query: trim($request->get('search')) ?? '',
//            )->get() : ''
//        ]);
    }
//    public function fibo($num){
//        if($num === 0 ){
//            return 0;
//        }
//        if($num === 1 || $num === 2) {
//            return  1;
//        }else{
//           return $this->fibo($num - 1) + $this->fibo($num - 2);
//        }
//    }
 public function fibo2($n){
        $num = 0;
        $res = 1;
        $count = 0;
        while($count < $n){
            echo ' '. $res;
            $temp = $num + $res;
            $num = $res;
            $res = $temp;
            $count++;
        }
 }
 public function bubbleSort($data){
        $change = false;
            $iterations = count($data) - 1;
            for($i=0;$i < count($data);$i++){
                $change = true;
                for($j = 0; $j < $iterations;$j++){
                    if($data[$j] > $data[$j + 1]){
                        list($data[$j] ,$data[$j + 1]) = array($data[$j + 1],$data[$j]);
                    }
                }
                $iterations--;
            }
        if(!$change = false){
            return $data;
        }
 }
    public function search(int $element, array $data)
    {
        $begin = 0;
        $end = count($data) - 1;
        $prev_begin = $begin;
        $prev_end = $end;
        $count = 0;
        while (true) {
            $count++;
            echo( '<pre>'.$count.'</pre>');

            $position = round(($begin + $end) / 2);

            if (isset($data[$position])) {
                if ($data[$position] == $element) {
                    return $position;
                }
                if ($data[$position] > $element) {
                    $end = floor(($begin + $end) / 2);
                } elseif ($data[$position] < $element) {
                    $begin = ceil(($begin + $end) / 2);
                }
            }
            if ($prev_begin == $begin && $prev_end == $end) {
                return false;
            }
            $prev_begin = $begin;
            $prev_end = $end;

        }
    }
    public function quicksort($data){
        $left = [];
        $right = [];
        $pivot = $data[0];

        for($i = 1;$i < count($data);$i++){
           if($pivot > $data[$i]){
               array_push($left,$data[$i],);
           }
           if($pivot < $data[$i]){
               array_push($right,$data[$i]);
           }
            if($pivot === $data[$i]){
                array_push($left,$data[$i],);
            }
        }
        return array_merge(count($left) ? $this->quicksort($left) : [] , [$pivot] ,count($right) ? $this->quicksort($right) : [] );

    }
}
