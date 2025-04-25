<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductGeoResource;
use App\Models\ProductGeo;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ProductGeoController extends Controller
{
    public function getProductGeos(Request $request)
    {
        $user = Auth::guard('sanctum')->setRequest($request)->user();
        if($user){
            return ProductGeoResource::collection(ProductGeo::where('geo_id', $request->geo_id)->whereNot(function ($query) use ($user) {
                $query->whereRelation('likes', 'user_id', '=',  $user->id);
            })->with(['product'])->paginate(1));
        }
        return ProductGeoResource::collection(ProductGeo::where('geo_id', $request->geo_id)->with(['product'])->paginate(1));
    }
    public function getLikedProductGeos(Request $request){
        $user = Auth::guard('sanctum')->setRequest($request)->user();
        if($user){
             return ProductGeoResource::collection(ProductGeo::whereRelation('likes', 'user_id', '=', $user->id)->with(['`product`'])->paginate(1));

        }
    }

    public function like(ProductGeo $product_geo){
        return $product_geo->like();
    }

    public function unlike(ProductGeo $product_geo){
        $product_geo->unlike();
    }
}
