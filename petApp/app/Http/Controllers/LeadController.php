<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLeadRequest;
use App\Http\Requests\UpdateLeadRequest;
use App\Http\Resources\LeadResource;
use App\Models\Lead;

class LeadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return  LeadResource::collection(Lead::with(['product_geo'])->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLeadRequest $request)
    {
        $lead = Lead::create($request->validated());
        return  LeadResource::make($lead->with(['product_geo'])->find($lead->id));
    }

    /**
     * Display the specified resource.
     */
    public function show(Lead $lead)
    {
        return new LeadResource($lead->with(['product_geo'])->find($lead->id));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLeadRequest $request, Lead $lead)
    {
        $lead->update($request->validated());
        return  LeadResource::make($lead->with(['product_geo'])->find($lead->id));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(Lead $lead)
    {
        return $lead->delete();
    }
}
