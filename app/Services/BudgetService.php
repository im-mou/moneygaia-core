<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use App\Http\Resources\BudgetResource;
use App\Models\Budget;
use Illuminate\Support\Facades\Config;

class BudgetService
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return BudgetResource::collection(Auth::user()->budgets);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title'  => [
                'required',
                'max:255',
                Rule::unique('budgets')
                    ->where(function ($query) {
                        return $query->where('user_id', Auth::user()->id);
                    })
            ],
            'description'  => 'string',
            'ammount'  => ['required', Config::get('constants.validate.money')],
            'start_date'  => 'required|string',
            'end_date'  => 'string',
            'transaction_type'  => 'required|integer|exists:transaction_types,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        return Budget::create([
            'title' => $request->title,
            'description' => $request->description,
            'ammount' => $request->ammount,
            'start_date' => Carbon::parse($request->start_date)->format('Y-m-d H:i:s'),
            'end_date' => Carbon::parse($request->end_date)->format('Y-m-d H:i:s'),
            'user_id' => Auth::user()->id,
            'transaction_type_id' => $request->transaction_type,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $budget = Budget::findOrFail($id)->where('user_id', Auth::user()->id);

        return new BudgetResource($budget);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title'  => [
                'required',
                'max:255',
                Rule::unique('budgets')
                    ->where(function ($query) {
                        return $query->where('user_id', Auth::user()->id);
                    })
            ],
            'description'  => 'string',
            'ammount'  => ['required', Config::get('constants.validate.money')],
            'start_date'  => 'string',
            'end_date'  => 'string',
            'transaction_type'  => 'integer|exists:transaction_types,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $budget = Budget::where('user_id', Auth::user()->id)->findOrFail($id);

        if ($request->filled('title')) {
            $budget->title = $request->title;
        }
        if ($request->filled('description')) {
            $budget->description = $request->description;
        }
        if ($request->filled('ammount')) {
            $budget->ammount = $request->ammount;
        }
        if ($request->filled('start_date')) {
            $budget->start_date = Carbon::parse($request->start_date)->format('Y-m-d H:i:s');
        }
        if ($request->filled('end_date')) {
            $budget->end_date = Carbon::parse($request->end_date)->format('Y-m-d H:i:s');
        }
        if ($request->filled('transaction_type')) {
            $budget->transaction_type_id = $request->transaction_type;
        }

        return $budget->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $budget = Budget::where('user_id', Auth::user()->id)->findOrFail($id);

        return $budget->delete();
    }
}
