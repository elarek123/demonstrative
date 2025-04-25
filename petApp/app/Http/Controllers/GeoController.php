<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGeoRequest;
use App\Http\Requests\UpdateGeoRequest;
use App\Http\Resources\GeoResource;
use App\Models\Geo;

class GeoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return  GeoResource::collection(Geo::with(['products', 'leads', 'product_geos'])->paginate(3));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreGeoRequest $request)
    {
        $data = $request->validated();
        $geo = Geo::create($data);
        $geo->products()->attach($data['product_ids']);
        return  GeoResource::make($geo->with(['products', 'leads', 'product_geos'])->find($geo->id));
    }

    /**
     * Display the specified resource.
     */
    public function show(Geo $geo)
    {
        return  GeoResource::make($geo->with(['products', 'leads', 'product_geos'])->find($geo->id));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateGeoRequest $request, Geo $geo)
    {
        $data = $request->validated();
        if(!empty($data['product_ids'])) {
            $geo->products()->sync($data['product_ids']);
        }
        $geo->update($data);
        return  GeoResource::make($geo->with(['products', 'leads', 'product_geos'])->find($geo->id));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(Geo $geo)
    {
        return $geo->delete();
    }

}
