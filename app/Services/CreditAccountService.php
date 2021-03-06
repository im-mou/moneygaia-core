<?php

namespace App\Services;

use App\Http\Resources\CreditAccountResource;
use App\Models\CreditAccount;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CreditAccountService
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $credit_accounts = CreditAccount::where("user_id", "=", Auth::user()->id)->paginate(
            Config::get("constants.pagination.per_page")
        );

        return CreditAccountResource::collection($credit_accounts);
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
            "balance" => ["required", Config::get("constants.validate.money")],
            "credit_account_type" => "required|integer|exists:credit_account_types,id",
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    "errors" => $validator->errors(),
                ],
                422
            );
        }

        $new_credit_account = CreditAccount::create([
            "title" => $request->title,
            "description" => $request->description,
            "balance" => $request->balance,
            "user_id" => Auth::user()->id,
            "credit_account_type_id" => $request->credit_account_type,
        ]);

        return new CreditAccountResource($new_credit_account);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $credit_account = CreditAccount::where("user_id", Auth::user()->id)->findOrFail($id);

        return new CreditAccountResource($credit_account);
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
            "credit_account_type" => "integer|exists:credit_account_types,id",
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    "errors" => $validator->errors(),
                ],
                422
            );
        }

        $credit_account = CreditAccount::where("user_id", Auth::user()->id)->findOrFail($id);

        if ($request->filled("title")) {
            $credit_account->title = $request->title;
        }
        if ($request->filled("description")) {
            $credit_account->description = $request->description;
        }
        if ($request->filled("credit_account_type")) {
            $credit_account->credit_account_type_id = $request->credit_account_type;
        }

        try {
            if ($credit_account->isDirty()) {
                $credit_account->save();
            }
            return new CreditAccountResource($credit_account);
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
        $credit_account = CreditAccount::where("user_id", Auth::user()->id)->findOrFail($id);

        try {
            return $credit_account->delete();
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
