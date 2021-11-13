<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use App\Http\Resources\BudgetResource;
use App\Models\Budget;
use Exception;
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
        $budgets = Budget::where("user_id", "=", Auth::user()->id)->simplePaginate(
            Config::get("constants.pagination.per_page")
        );
        return BudgetResource::collection($budgets);
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
            "title" => [
                "required",
                "max:255",
                Rule::unique("budgets")->where(function ($query) {
                    return $query->where("user_id", Auth::user()->id);
                }),
            ],
            "description" => "string",
            "ammount" => ["required", Config::get("constants.validate.money")],
            "start_date" => "required|date",
            "end_date" => "date|after:start_date",
            "transaction_type" => "required|integer|exists:transaction_types,id",
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    "errors" => $validator->errors(),
                ],
                422
            );
        }

        $new_budget = Budget::create([
            "title" => $request->title,
            "description" => $request->description,
            "ammount" => $request->ammount,
            "start_date" => Carbon::parse($request->start_date),
            "end_date" => Carbon::parse($request->end_date),
            "user_id" => Auth::user()->id,
            "transaction_type_id" => $request->transaction_type,
        ]);

        return new BudgetResource($new_budget);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        $budget = Budget::where("user_id", Auth::user()->id)->findOrFail($id);

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
            "title" => [
                "max:255",
                Rule::unique("budgets")->where(function ($query) {
                    return $query->where("user_id", Auth::user()->id);
                }),
            ],
            "description" => "string",
            "ammount" => [Config::get("constants.validate.money")],
            "start_date" => "date",
            "end_date" => "date|after:start_date",
            "transaction_type" => "integer|exists:transaction_types,id",
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    "errors" => $validator->errors(),
                ],
                422
            );
        }

        $budget = Budget::where("user_id", Auth::user()->id)->findOrFail($id);

        if ($request->filled("title")) {
            $budget->title = $request->title;
        }
        if ($request->filled("description")) {
            $budget->description = $request->description;
        }
        if ($request->filled("ammount")) {
            $budget->ammount = $request->ammount;
        }
        if ($request->filled("start_date")) {
            $budget->start_date = Carbon::parse($request->start_date);
        }
        if ($request->filled("end_date")) {
            $budget->end_date = Carbon::parse($request->end_date);
        }
        if ($request->filled("transaction_type")) {
            $budget->transaction_type_id = $request->transaction_type;
        }

        try {
            $budget->save();
            return new BudgetResource($budget);
        } catch (Exception $e) {
            return response()->json(
                [
                    "errors" => ["message" => "Could not update the resource"],
                ],
                501
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $budget = Budget::where("user_id", Auth::user()->id)->findOrFail($id);

        try {
            return $budget->delete();
        } catch (Exception $e) {
            return response()->json(
                [
                    "errors" => ["message" => "Could not delete the resource"],
                ],
                501
            );
        }
    }
}
