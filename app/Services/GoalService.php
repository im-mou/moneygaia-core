<?php

namespace App\Services;

use App\Http\Resources\GoalResource;
use App\Models\Goal;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class GoalService
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $goals = Goal::where("user_id", "=", Auth::user()->id)->paginate(Config::get("constants.pagination.per_page"));
        return GoalResource::collection($goals);
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
                Rule::unique("credit_accounts")->where(function ($query) {
                    return $query->where("user_id", Auth::user()->id);
                }),
            ],
            "description" => "string",
            "ammount" => ["required", Config::get("constants.validate.money")],
            "due_date" => "required|date|after:today",
            "icon" => "integer|exists:icons,id",
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    "errors" => $validator->errors(),
                ],
                422
            );
        }

        $new_goal = Goal::create([
            "title" => $request->title,
            "description" => $request->description,
            "ammount" => $request->ammount,
            "due_date" => Carbon::parse($request->due_date),
            "achived" => false,
            "user_id" => Auth::user()->id,
            "icon_id" => $request->icon,
        ]);

        return new GoalResource($new_goal);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $goal = Goal::where("user_id", Auth::user()->id)->findOrFail($id);

        return new GoalResource($goal);
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
                Rule::unique("credit_accounts")->where(function ($query) {
                    return $query->where("user_id", Auth::user()->id);
                }),
            ],
            "description" => "string",
            "ammount" => [Config::get("constants.validate.money")],
            "due_date" => "date|after:today",
            "icon" => "integer|exists:icons,id",
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    "errors" => $validator->errors(),
                ],
                422
            );
        }

        $goal = Goal::where("user_id", Auth::user()->id)->findOrFail($id);

        if ($request->filled("title")) {
            $goal->title = $request->title;
        }
        if ($request->filled("description")) {
            $goal->description = $request->description;
        }
        if ($request->filled("ammount")) {
            $goal->ammount = $request->ammount;
        }
        if ($request->filled("due_date")) {
            $goal->due_date = Carbon::parse($request->due_date);
        }
        if ($request->filled("icon")) {
            $goal->icon_id = $request->icon;
        }

        try {
            if ($goal->isDirty()) {
                $goal->save();
            }
            return new GoalResource($goal);
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
     * Update "achived" column of a the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function setAchived(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            "achived" => "required|boolean",
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    "errors" => $validator->errors(),
                ],
                422
            );
        }

        $goal = Goal::where("user_id", Auth::user()->id)->findOrFail($id);

        $goal->achived = $request->achived;

        try {
            $goal->save();
            return response()->json(["achived" => $goal->achived], 200);
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
        $goal = Goal::where("user_id", Auth::user()->id)->findOrFail($id);

        try {
            return $goal->delete();
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
