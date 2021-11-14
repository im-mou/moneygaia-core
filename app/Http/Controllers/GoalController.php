<?php

namespace App\Http\Controllers;

use App\Models\Goal;
use App\Services\GoalService;
use Illuminate\Http\Request;

class GoalController extends Controller
{
    private $goalService;

    public function __construct()
    {
        $this->goalService = new GoalService();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->goalService->index();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return $this->goalService->store($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $goal
     * @return \Illuminate\Http\Response
     */
    public function show(int $goal)
    {
        return $this->goalService->show($goal);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $goal
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $goal)
    {
        return $this->goalService->update($request, $goal);
    }

    /**
     * Update "achived" column of a the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $goal
     * @return \Illuminate\Http\Response
     */
    public function setAchived(Request $request, int $goal)
    {
        return $this->goalService->setAchived($request, $goal);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $goal
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $goal)
    {
        return $this->goalService->destroy($goal);
    }
}
