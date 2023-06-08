<?php

namespace App\Http\Controllers\Admin;

use App\Models\TimeBlock;
use App\Http\Requests\StoreTimeBlockRequest;
use App\Http\Requests\UpdateTimeBlockRequest;

class TimeBlockController extends Controller
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
     * @param  \App\Http\Requests\StoreTimeBlockRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreTimeBlockRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\TimeBlock  $timeBlock
     * @return \Illuminate\Http\Response
     */
    public function show(TimeBlock $timeBlock)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\TimeBlock  $timeBlock
     * @return \Illuminate\Http\Response
     */
    public function edit(TimeBlock $timeBlock)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateTimeBlockRequest  $request
     * @param  \App\Models\TimeBlock  $timeBlock
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateTimeBlockRequest $request, TimeBlock $timeBlock)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TimeBlock  $timeBlock
     * @return \Illuminate\Http\Response
     */
    public function destroy(TimeBlock $timeBlock)
    {
        //
    }
}
