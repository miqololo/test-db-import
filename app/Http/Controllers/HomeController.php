<?php

namespace App\Http\Controllers;

use App\Category;
use App\Cities;
use App\Mark;
use App\ModelItem;
use App\ProductDetail;
use App\ProductHasCity;
use App\Products;
use Illuminate\Http\Request;

class HomeController extends Controller
{

    /**
     * Show the profile for the given user.
     *
     * @param  int  $id
     * @return View
     */

    public function index(Request $request)
    {
        $products = Products::select([
            'products.name as name',
            'products.id as id',
            'products.weight as weight',
            'products.code as code',
        ])->when($request->code, function($query1) use ($request){
            $query1->where('products.code',$request->code);
        })->join('product_detail', 'product_detail.product_id', '=', 'products.id')
        ->groupBy('name','weight','code','id');

        if($request->model || $request->mark || $request->category) {
            $products = $products
                ->when($request->model, function ($query) use ($request) {
                    $query->where('product_detail.model_id', '=', $request->model);
                })->when($request->mark, function ($query) use ($request) {
                    $query->where('product_detail.mark_id', '=', $request->mark);
                })->when($request->category, function ($query) use ($request) {
                    $query->where('product_detail.category_id', '=', $request->category);
                });
        }
        $products = $products->paginate(15);
        return view('welcome',compact('products','request'));
    }


    public function import(){

        $storagePath = storage_path('app/public');
        $files = scandir($storagePath) ;
        ini_set('memory_limit',-1);
        set_time_limit(1000000000);

        $productsCitiesModels = ProductHasCity::all();
        $productsCitiesByCityAndProduct = [];
        foreach($productsCitiesModels as $key=>$value){
            if(empty($productsCitiesByCityAndProduct[$value->city_id]))
                $productsCitiesByCityAndProduct[$value->city_id] = [];
            if($productsCitiesByCityAndProduct[$value->city_id][$value->product_id]){
                $value->count = $productsCitiesByCityAndProduct[$value->city_id][$value->product_id]->count + $value->count;
            }
            $productsCitiesByCityAndProduct[$value->city_id][$value->product_id] = $value;
        }



       foreach($files as $key=>$file){
           $citiesArray = Cities::pluck('id','name')->toArray();
           $modelsArray = ModelItem::pluck('id','name')->toArray();
           $markArray = Mark::pluck('id','name')->toArray();
           $categoryArray = Category::pluck('id','name')->toArray();
           $productsArray = Products::pluck('id','code')->toArray();
           $filePath = $storagePath.'/'.$file;
           if(strpos($file, 'import') !== false){
               $pathInfo = pathinfo($filePath);
               if($pathInfo['extension'] === 'xml'){
                   $xml = simplexml_load_string(file_get_contents($filePath));
                   $json = json_encode($xml);
                   $assocArray = json_decode($json,TRUE);
                   $city = str_replace("Классификатор (", "", $assocArray['Классификатор']['Наименование']);
                   $city = str_replace(")", "", $city);
                   if(empty($citiesArray[$city])){
                       $cityModel = new Cities();
                       $cityModel->code = $city;
                       $cityModel->name = $city;
                       $cityModel->save();
                       $citiesArray[$cityModel->name] = $cityModel->id;
                   }
                   if(!empty($assocArray['Каталог']) && !empty($assocArray['Каталог']['Товары'])){
                       $products = $assocArray['Каталог']['Товары']['Товар'];
                       foreach ($products as $key2=>$value2){
                           if(empty($productsArray[$value2['Код']])){
                               $productModel = new Products();
                               $productModel->code = $value2['Код'];
                               $productModel->weight = $value2['Вес'];
                               $productModel->name = $value2['Наименование'];

                               $productsArray[$productModel->code] = $productModel->id;
                               $productModel->save();
                               if(!empty($value2['Взаимозаменяемости'])){
                                   $relations = $value2['Взаимозаменяемости']['Взаимозаменяемость'];
                                   foreach ($relations as $key3=>$relation){
                                       if((!empty($relation['Марка']) && strlen($relation['Марка'])>0) || (!empty($relation['Модель']) && strlen($relation['Модель'])>0) ||
                                           (!empty($relation['КатегорияТС']) && strlen($relation['КатегорияТС'])>0)){
                                           $productDetails = new ProductDetail();
                                           $productDetails->product_id = $productModel->id;
                                           if(!empty($relation['Марка']) && empty($markArray[$relation['Марка']])){
                                               $markModel = new Mark();
                                               $markModel->name = $relation['Марка'];
                                               $markModel->save();
                                               $markArray[$relation['Марка']] = $markModel->id;
                                           }
                                           if(!empty($relation['Марка'])){
                                               $productDetails->mark_id = $markArray[$relation['Марка']];
                                           }
                                           if(!empty($relation['Модель']) && empty($modelsArray[$relation['Модель']])){
                                               $modelItem = new ModelItem();
                                               $modelItem->name = $relation['Модель'];
                                               $modelItem->save();
                                               $modelsArray[$relation['Модель']] = $modelItem->id;
                                           }
                                           if(!empty($relation['Модель'])){
                                               $productDetails->model_id = $modelsArray[$relation['Модель']];
                                           }
                                           if(!empty($relation['КатегорияТС']) && empty($categoryArray[$relation['КатегорияТС']])){
                                               $categoryModel = new Category();
                                               $categoryModel->name = $relation['КатегорияТС'];
                                               $categoryModel->save();
                                               $categoryArray[$relation['КатегорияТС']] = $categoryModel->id;
                                           }
                                           if(!empty($relation['КатегорияТС'])){
                                               $productDetails->category_id = $categoryArray[$relation['КатегорияТС']];
                                           }
                                           $productDetails->save();
                                       }
                                   }
                               }
                           }
                       }
                   }
               }
               unlink($filePath);
           } else if (strpos($file, 'offers') !== false){
               $pathInfo = pathinfo($filePath);
               if($pathInfo['extension'] === 'xml'){
                   $xml = simplexml_load_string(file_get_contents($filePath));
                   $json = json_encode($xml);
                   $assocArray = json_decode($json,TRUE);
                   $city = str_replace("Классификатор (", "", $assocArray['Классификатор']['Наименование']);
                   $city = str_replace(")", "", $city);
                   if(empty($citiesArray[$city])){
                       $cityModel = new Cities();
                       $cityModel->code = $city;
                       $cityModel->name = $city;
                       $cityModel->save();
                       $citiesArray[$cityModel->name] = $cityModel->id;
                   }
                   if(!empty($assocArray['ПакетПредложений']) && !empty($assocArray['ПакетПредложений']['Предложения'])){
                       $offers = $assocArray['ПакетПредложений']['Предложения']['Предложение'];
                       $city = $citiesArray[$city];
                       if(empty($productsCitiesByCityAndProduct[$city])){
                           $productsCitiesByCityAndProduct[$city] = [];
                       }
                       foreach ($offers as $key2=>$offer){

                           if(!empty($productsArray[$offer['Код']])){
                               $productsCity = $productsCitiesByCityAndProduct[$city];

                               if(empty($productsCity[$productsArray[$offer['Код']]])){
                                   $cityProductModel = new ProductHasCity();
                                   $cityProductModel->city_id = $city;
                                   $cityProductModel->product_id = $productsArray[$offer['Код']];
                                   $cityProductModel->count = 0;
                                   $cityProductModel->price = 0;
                                   $productsCitiesByCityAndProduct[$city][$productsArray[$offer['Код']]] = $cityProductModel;
                               }
                               $tempRelationCity = $productsCitiesByCityAndProduct[$city][$productsArray[$offer['Код']]];
                               if(!empty($offer['Цены']) && !empty($offer['Цены']['Цена']) && !empty($offer['Цены']['Цена'][0]))
                                   $tempRelationCity['price'] = floatval($offer['Цены']['Цена'][0]['ЦенаЗаЕдиницу']);
                               if(!empty($offer['Количество']))
                                   $tempRelationCity['count'] = $tempRelationCity['count'] + $offer['Количество'];
                               $tempRelationCity->save();
                               $productsCitiesByCityAndProduct[$city][$productsArray[$offer['Код']]] = $tempRelationCity;
                           }
                       }
                   }
               }
               unlink($filePath);
           }
       }
    }


    /*
       AJAX request
       */
    public function getModels(Request $request){

        $search = $request->search;

        if($search == ''){
            $employees = ModelItem::orderby('name','asc')->select('id','name')->limit(5)->get();
        }else{
            $employees = ModelItem::orderby('name','asc')->select('id','name')->where('name', 'like', '%' .$search . '%')->limit(5)->get();
        }

        $response = array();
        foreach($employees as $employee){
            $response[] = array(
                "id"=>$employee->id,
                "text"=>$employee->name
            );
        }

        echo json_encode($response);
        exit;
    }


    /*
       AJAX request
       */
    public function getCities(Request $request){

        $search = $request->search;

        if($search == ''){
            $employees = Cities::orderby('name','asc')->select('id','name')->limit(5)->get();
        }else{
            $employees = Cities::orderby('name','asc')->select('id','name')->where('name', 'like', '%' .$search . '%')->limit(5)->get();
        }

        $response = array();
        foreach($employees as $employee){
            $response[] = array(
                "id"=>$employee->id,
                "text"=>$employee->name
            );
        }

        echo json_encode($response);
        exit;
    }


    /*
       AJAX request
       */
    public function getMark(Request $request){

        $search = $request->search;

        if($search == ''){
            $employees = Mark::orderby('name','asc')->select('id','name')->limit(5)->get();
        }else{
            $employees = Mark::orderby('name','asc')->select('id','name')->where('name', 'like', '%' .$search . '%')->limit(5)->get();
        }

        $response = array();
        foreach($employees as $employee){
            $response[] = array(
                "id"=>$employee->id,
                "text"=>$employee->name
            );
        }

        echo json_encode($response);
        exit;
    }


    /*
       AJAX request
       */
    public function getCategories(Request $request){

        $search = $request->search;

        if($search == ''){
            $employees = Category::orderby('name','asc')->select('id','name')->limit(5)->get();
        }else{
            $employees = Category::orderby('name','asc')->select('id','name')->where('name', 'like', '%' .$search . '%')->limit(5)->get();
        }

        $response = array();
        foreach($employees as $employee){
            $response[] = array(
                "id"=>$employee->id,
                "text"=>$employee->name
            );
        }

        echo json_encode($response);
        exit;
    }


    /*
       AJAX request
       */
    public function getInfo(Request $request){

        $productId = $request->product_id;
        $productDetails = ProductDetail::where('product_id',$productId)->with(['model', 'mark', 'category'])->get();
        $productCities = ProductHasCity::where('product_id',$productId)->with('city')->get();
        $this->layout = null;
        return view('details',compact('productDetails','productCities'));

    }

}