<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return  ProductResource::collection(Product::with(['geos', 'product_geos', 'leads'])->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        $data = $request->validated();
        $product = Product::create($data);
        $product->geos()->attach($data['geo_ids']);
        return  ProductResource::make($product->with(['geos', 'product_geos', 'leads'])->find($product->id));
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return new ProductResource($product->with(['geos', 'product_geos', 'leads'])->find($product->id));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        $data = $request->validated();
        $product->update($data);
        if(!empty($data['geo_ids'])) {
            $product->geos()->sync($data['geo_ids']);
        }
        $product->update($data);
        return  ProductResource::make($product->with(['geos', 'product_geos', 'leads'])->find($product->id));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(Product $product)
    {
        return $product->delete();
    }



}
