<?php

namespace App\Http\Controllers\Admin;

use App\Models\ScheduleLayout;
use App\Http\Requests\StoreScheduleLayoutRequest;
use App\Http\Requests\UpdateScheduleLayoutRequest;

class ScheduleLayoutController extends Controller
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
     * @param  \App\Http\Requests\StoreScheduleLayoutRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreScheduleLayoutRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ScheduleLayout  $scheduleLayout
     * @return \Illuminate\Http\Response
     */
    public function show(ScheduleLayout $scheduleLayout)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ScheduleLayout  $scheduleLayout
     * @return \Illuminate\Http\Response
     */
    public function edit(ScheduleLayout $scheduleLayout)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateScheduleLayoutRequest  $request
     * @param  \App\Models\ScheduleLayout  $scheduleLayout
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateScheduleLayoutRequest $request, ScheduleLayout $scheduleLayout)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ScheduleLayout  $scheduleLayout
     * @return \Illuminate\Http\Response
     */
    public function destroy(ScheduleLayout $scheduleLayout)
    {
        //
    }
}
