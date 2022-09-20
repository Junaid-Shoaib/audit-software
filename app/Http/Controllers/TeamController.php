<?php

namespace App\Http\Controllers;

// use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;
use App\Models\Company;
use App\Models\Year;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Auth;

class TeamController extends Controller
{
    public function index()
    {
        //Validating request
        request()->validate([
            'direction' => ['in:asc,desc'],
            'field' => ['in:name,email']
        ]);

        // auth()->user()->companies()->getQuery()->paginate(10)
        $year = Year::find(session('year_id'));

        // dd($year->users());
        $query =
        $year->users()->getQuery()->paginate(10)
          ->withQueryString()
                ->through(
                        fn ($user) =>
                        [
                            'id' => $user->id,
                            'name' => $user->name,
                            'email' => $user->email,
                            $cur_user = User::find($user->user_id),
                            'role' => $cur_user->getRoleNames()[0],
                            // dd($user, $cur_user->getRoleNames()),
                            // 'delete' => Year::where('company_id', $comp->id)->first() != null ? true : false,
                        ],
                    );

        //         echo "<pre>";
        // print_r($query);
        // exit();

        if (request('search')) {
            $query->where('name', 'LIKE', '%' . request('search') . '%');
        }

        if (request()->has(['field', 'direction'])) {
            $query->orderBy(
                request('field'),
                request('direction')
            );
        }

        // $tem =count($query) > 0;
        // dd($tem);
        return Inertia::render('Teams/Index', [
            // 'can' => [
            //     'edit' => auth()->user()->can('edit'),
            //     'create' => auth()->user()->can('create'),
            //     'delete' => auth()->user()->can('delete'),
            //     'read' => auth()->user()->can('read'),
            // ],
            'balances' => $query,
            'filters' => request()->all(['search', 'field', 'direction']),
            'company' => Company::where('id', session('company_id'))->first(),
            'companies' => Auth::user()->companies,
            'year' => Year::where('company_id', session('company_id'))->where('id', session('year_id'))->first(),
            'years' => Year::where('company_id', session('company_id'))->get(),
            'team_exists' => count($query) > 0 ? true : false,
        ]);
    }


    public function create()
    {
        // $groups = \App\Models\AccountGroup::where('company_id', session('company_id'))->map->only('id', 'name')->get();
        // $groups = AccountGroup::where('company_id', session('company_id'))->tree()->get()->toTree();
        // $partners = User::first();
        // dd($partners->getRoleNames()[0]);
        $partners = User::role('partner')->get();
        $staff = User::role('staff')->get()->toArray();
        $managers = User::role('manager')->get();


        // where('company_id', session('company_id'))->tree()->get()->toTree();
        // $group_first = AccountGroup::all()->where('company_id', session('company_id'))->map->only('id', 'name')->first();

        // if ($group_first) {
            return Inertia::render('Teams/Create', [
                'partners' => $partners,
                'staff' => $staff,
                'managers' => $managers,

                'partner' => User::role('partner')->first(),
                'staf' => User::role('staff')->first(),
                'manager' => User::role('manager')->first(),
            ]);
        // } else {
        //     return Redirect::route('accountgroups.create')->with('success', 'ACCOUNTGROUP NOT FOUND, Please create account group first.');
        // }
    }

    public function store()
    {
        Request::validate([
            'partner' => ['required'],
            'manager' => ['required'],
            'staff' => ['required'],
        ]);

        $year = Year::find(session('year_id'));

        $partner_id = Request::input('partner')['id'];
        $manager_id = Request::input('manager')['id'];

        $year->users()->attach($partner_id);
        $year->users()->attach($manager_id);

        $staff = Request::input('staff');
        foreach($staff as $staf)
        {
            $year->users()->attach($staf['id']);
        }

        return Redirect::route('teams')->with('success', 'Team created.');
    }

}
