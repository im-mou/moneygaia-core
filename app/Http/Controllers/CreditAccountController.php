<?php

namespace App\Http\Controllers;

use App\Models\CreditAccount;
use App\Services\CreditAccountService;
use Illuminate\Http\Request;

class CreditAccountController extends Controller
{

    private $creditAccountService;

    public function __construct()
    {
        $this->creditAccountService = new CreditAccountService();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->creditAccountService->index();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return $this->creditAccountService->store($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CreditAccount  $creditAccount
     * @return \Illuminate\Http\Response
     */
    public function show(CreditAccount $creditAccount)
    {
        return $this->creditAccountService->show($creditAccount);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CreditAccount  $creditAccount
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CreditAccount $creditAccount)
    {
        return $this->creditAccountService->update($request, $creditAccount);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CreditAccount  $creditAccount
     * @return \Illuminate\Http\Response
     */
    public function destroy(CreditAccount $creditAccount)
    {
        return $this->creditAccountService->destroy($creditAccount);
    }
}
