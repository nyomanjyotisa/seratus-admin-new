<?php

namespace App\Http\Controllers;

use App\Models\GlobalData;
use Illuminate\Http\Request;

class GlobalDataController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\GlobalData  $globalData
     * @return \Illuminate\Http\Response
     */
    public function show(GlobalData $globalData)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\GlobalData  $globalData
     * @return \Illuminate\Http\Response
     */
    public function edit(GlobalData $globalData)
    {
        //
    }

    public function get(Request $request)
    {
        $key = $request->query('key');
        $record = GlobalData::where('key', $key)->first();
        return response()->json([
            'value' => $record ? $record->value : null
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\GlobalData  $globalData
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $request->validate([
            'key'   => 'required|string',
            'value' => 'required|string',
        ]);

        GlobalData::updateOrCreate(
            ['key' => $request->key],
            ['value' => $request->value]
        );

        return response()->json(['message' => 'Global data updated successfully']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\GlobalData  $globalData
     * @return \Illuminate\Http\Response
     */
    public function destroy(GlobalData $globalData)
    {
        //
    }
}
