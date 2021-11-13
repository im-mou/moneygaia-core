<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Services\BudgetService;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    private $budgetService;

    public function __construct()
    {
        $this->budgetService = new BudgetService();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->budgetService->index();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return $this->budgetService->store($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $budget
     * @return \Illuminate\Http\Response
     */
    public function show(int $budget)
    {
        return $this->budgetService->show($budget);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $budget
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $budget)
    {
        return $this->budgetService->update($request, $budget);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $budget
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $budget)
    {
        return $this->budgetService->destroy($budget);
    }
}
