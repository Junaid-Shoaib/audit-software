<?php

namespace App\Http\Controllers;

use App\Models\AdviserAccount;
use App\Models\AdviserConfirmation;
use App\Models\Advisor;
use Illuminate\Http\Request;
use Illuminate\Http\Request as Req;
use Illuminate\Support\Facades\Redirect;
use App\Models\Year;
use App\Models\Setting;
use App\Models\Company;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Carbon\Carbon;
use PDO;

class AdviserAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        request()->validate([
            'direction' => ['in:asc,desc'],
            'field' => ['in:name,address'],
        ]);

        if (request()->has('search')) {
            $obj_data = AdviserAccount::where('company_id', session('company_id'))
                ->where('name', 'LIKE', '%' . $req->search . '%');
        } else {
            $obj_data = AdviserAccount::where('company_id', session('company_id'));
        }

        if (Advisor::all()->first()) {
            return Inertia::render(
                'Advisor_accounts/Index',
                [
                    'filters' => request()->all(['search', 'field', 'direction']),
                    'mapped_data' => $obj_data
                        ->get()
                        ->map(
                            function ($branch) {
                                return
                                    [
                                        'id' => $branch->id,
                                        'advisor_id' => $branch->advisor->name,
                                        'company_id' => $branch->company->name,
                                        // 'currency' => $branch->currency,
                                        // 'branches' => $branch->Advisor->bank->name . " - " . $branch->Advisor->address,
                                        'delete' => AdviserConfirmation::where('advisor_id', $branch->id)->first() ? false : true,
                                        // 'delete' => true,
                                    ];
                            }
                        ),

                    'dataEdit' => AdviserAccount::where('company_id', session('company_id'))->first(),

                ],

            );
        } else {
            return Redirect::route('advisors.create',)->with('success', 'Create Advisor First');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // ]);
        // dd($balances);
        $data  = Advisor::all()->map->only('id')->first();
        $company = Company::where('id', session('company_id'))->first();
        // dd(session('year_id'));
        // dd($company->name);
        if ($data) {

            return Inertia::render('Advisor_accounts/Create', [

                'company_name' => $company ? $company->name : '',
                //just fetch crete
                'balances' => AdviserAccount::where('company_id', session('company_id'))->get()->map(
                    function ($branch) use ($company) {
                        return
                            [
                                'id' => $branch->id,
                                'company_name' => $company ? $company->name : '',
                                'advisors' => $branch->Advisor->name . " - " . $branch->Advisor->type,
                                // 'delete' => BankBalance::where('account_id', $branch->id)->first() ? false : true,
                                // 'delete' => true,
                            ];
                    }
                ),

                // $branches = Advisor::all()
                'advisors' => Advisor::all()
                    ->map(function ($branch) {
                        return
                            [
                                'id' => $branch->id,
                                'address' => $branch->name . " - " . $branch->type,

                            ];
                    }),


            ]);
        } else {
            return Redirect::route('advisors.create')->with('success', 'Create Advisor First');
        }
    }
    //


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $advisor_arr = array();

        // $company_id = [];
        // $year_id = [];

        foreach ($request->accounts as $key => $account) {


            $adv = AdviserAccount::where('advisor_id', $account['advisor_id'])
                ->where('company_id', session('company_id'))
                ->where('year_id', session('year_id'))
                ->first();
            if ($adv == null) {
                AdviserAccount::create([
                    'advisor_id' => $account['advisor_id'],
                    'company_id' => session('company_id'),
                    'year_id' => session('year_id'),
                ]);
            } else {
                return Redirect::route('advisor_accounts.create')->with('error', 'Advisor Account ' . $adv->advisor->name . ' Already Taken');
            }
        }
        return Redirect::route('advisor_accounts',)->with('success', 'Advisor Account Created');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\AdviserAccount  $adviserAccount
     * @return \Illuminate\Http\Response
     */
    public function show(AdviserAccount $adviserAccount)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\AdviserAccount  $adviserAccount
     * @return \Illuminate\Http\Response
     */
    public function edit(AdviserAccount $adviserAccount)
    {
        //
        // $data = AdviserAccount::where('company_id', session('company_id'))
        //             ->where('year_id', session('year_id'))
        //             ->get()
        //             ->map(
        //                 function ($account) {
        //                     return
        //                         [
        //                             'id' => $account->id,
        //                             'name' => $account->company->name,
        //                             // 'type' => $account->type,
        //                             // 'currency' => $account->currency,
        //                             'advisor_id' => $account->advisor->name . " - " . $account->advisor->type,
        //                         ];
        //                 }
        //             );
        //     dd($data);

        return Inertia::render(
            'Advisor_accounts/Edit',
            [
                'data' => AdviserAccount::where('company_id', session('company_id'))
                    ->where('year_id', session('year_id'))
                    ->get()
                    ->map(
                        function ($account) {
                            return
                                [
                                    'id' => $account->id,
                                    'name' => $account->company->name,
                                    // 'type' => $account->type,
                                    // 'currency' => $account->currency,
                                    'advisor_id' => $account->advisor->name . " - " . $account->advisor->type,
                                ];
                        }
                    )

            ]
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\AdviserAccount  $adviserAccount
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AdviserAccount $adviserAccount)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\AdviserAccount  $adviserAccount
     * @return \Illuminate\Http\Response
     */
    public function destroy(AdviserAccount $adviserAccount)
    {
        $adviserAccount->delete();
        return Redirect::back()->with('success', 'Advisor Account deleted.');
    }
}
