<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use App\Models\Account;
use App\Models\Company;
// use App\Models\Entry;
use App\Models\AccountGroup;
use Inertia\Inertia;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Http\Request as Req;

class AccountController extends Controller
{
    public function index(Req $req)
    {
        if (AccountGroup::where('company_id', session('company_id'))->first()) {

            if(request()->has(
                // ['select', 'search']
                'search'
                )){
                $obj_data = Account::where('company_id', session('company_id'))
                    ->where(function ($query) use($req) {
                        $query
                        ->whereHas('accountGroup', function($q) use ($req) {
                            $q->where('name', 'like', '%' . $req->search . '%');
                        })
                        ->orWhere('name', 'like', '%' . $req->search . '%');
                    })->get();
            }
            else{
                $obj_data = Account::where('company_id', session('company_id'))->get();
            }
            $mapped_data = $obj_data->map(function($account, $key) {
                return [
                    'id' => $account->id,
                    'name' => $account->name,
                    'group_name' => $account->accountGroup->name,
                ];
            });

            return Inertia::render('Accounts/Index', [
                'mapped_data' => $mapped_data,
                'filters' => request()->all(['search', 'field', 'direction']),
                // 'balances' => $balances,
                'company' => Company::where('id', session('company_id'))->first(),
                'companies' => auth()->user()->companies,
            ]);
        } else {
            return Redirect::route('trial.index')->with('warning', 'Please upload Excel to generate Accounts and Account Groups.');
        }
    }

    public function create()
    {
        $groups = AccountGroup::where('company_id', session('company_id'))->tree()->get()->toTree();
        $group_first = AccountGroup::all()->where('company_id', session('company_id'))->map->only('id', 'name')->first();

        if ($group_first) {
            return Inertia::render('Accounts/Create', [
                'groups' => $groups,
                'group_first' => $group_first,
            ]);
        } else {
            return Redirect::route('accountgroups.create')->with('success', 'ACCOUNTGROUP NOT FOUND, Please create account group first.');
        }
    }

    public function store()
    {
        Request::validate([
            'name' => ['required'],
             'group' => ['required'],
        ]);

        $account = Account::create([
            'name' => Request::input('name'),
            // 'number' => Request::input('number'),
            'group_id' => Request::input('group'),
            'company_id' => session('company_id'),
        ]);
        $account->update(['number' => $this->snum($account)]);

        return Redirect::route('accounts')->with('success', 'Account created.');
    }

    public function edit(Account $account)
    {
        $groups = AccountGroup::all()->where('company_id', session('company_id'))->map->only('id', 'name');
        $group_first = AccountGroup::where('id', $account->group_id)->first();

        return Inertia::render('Accounts/Edit', [
            'account' => [
                'id' => $account->id,
                'company_id' => $account->company_id,
                'group_id' => $account->accountGroup->name,
                'name' => $account->name,
                'number' => $account->number,
            ],
            'groups' => $groups,
            'group_first' => $group_first,
        ]);
    }

    public function update(Account $account)
    {
        Request::validate([
            'name' => ['required'],
        ]);
        $account->company_id = session('company_id');
        $account->name = Request::input('name');
        $account->save();

        return Redirect::route('accounts')->with('success', 'Account updated.');
    }

    public function destroy(Account $account)
    {
        $account->delete();
        return Redirect::back()->with('success', 'Account deleted.');
    }



    // TO generate account number automatically
    function snum($account)
    {
        $ty = $account->accountGroup->accountType;
        $grs = $ty->accountGroups->where('company_id', session('company_id'));
        $grindex = 1;
        $grselindex = 0;
        $grsel = null;
        $number = 0;
        foreach ($grs as $gr) {
            if ($gr->name == $account->accountGroup->name) {
                $grselindex = $grindex;
                $grsel = $gr;
            }
            ++$grindex;
        }
        if (count($grsel->accounts) == 1) {
            $number = $ty->id . sprintf("%'.03d", $grselindex) . sprintf("%'.03d", 1);
        } else {
            $lastac = Account::orderBy('id', 'desc')->where('company_id', session('company_id'))->where('group_id', $grsel->id)->skip(1)->first()->number;
            $lastst = Str::substr($lastac, 4, 3);
            $number = $ty->id . sprintf("%'.03d", $grselindex) . sprintf("%'.03d", ++$lastst);
        }
        return $number;
    }
}
