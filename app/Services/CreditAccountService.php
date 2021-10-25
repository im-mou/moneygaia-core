<?php

namespace App\Services;

use App\Http\Resources\CreditAccountResource;
use App\Models\CreditAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
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
        return CreditAccountResource::collection(Auth::user()->accounts);
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
                Rule::unique('credit_accounts')
                    ->where(function ($query) {
                        return $query->where('user_id', Auth::user()->id);
                    })
            ],
            'description'  => 'string',
            'balance'  => ['required', Config::get('constants.validate.money')],
            'credit_account_type'  => 'required|integer|exists:credit_account_types,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        return CreditAccount::create([
            'title' => $request->title,
            'description' => $request->description,
            'balance' => $request->balance,
            'user_id' => Auth::user()->id,
            'credit_account_type_id' => $request->credit_account_type,
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
        $credit_account = CreditAccount::where('user_id', Auth::user()->id)->get($id);

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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
