<?php

namespace App\Http\Controllers;

// use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;
use App\Models\Company;
use App\Models\Year;
use App\Models\Setting;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Auth;

class TeamController extends Controller
{
    public function index()
    {
        // if(Company::first())
        if(auth()->user()->companies()->first())
        {
            //Validating request
            request()->validate([
                'direction' => ['in:asc,desc'],
                'field' => ['in:name,email']
            ]);

            $year = Year::find(session('year_id'));

            if($year)
            {
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
                                    // 'delete' => Year::where('company_id', $comp->id)->first() != null ? true : false,
                                ],
                            );

                if (request('search')) {
                    $query->where('name', 'LIKE', '%' . request('search') . '%');
                }

                if (request()->has(['field', 'direction'])) {
                    $query->orderBy(
                        request('field'),
                        request('direction')
                    );
                }
            } else {
                return Redirect::route('years')->with('warning', 'Year not found');
            }

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
        } else {
            return Redirect::route('companies')->with('warning', 'Create Company first');
        }
    }


    public function create()
    {
        $partner = User::role('partner')->first();
        $staf = User::role('staff')->first();
        $manager = User::role('manager')->first();

        if($partner && $manager && $staf)
        {
            $partners = User::role('partner')->get();
            $staff = User::role('staff')->get()->toArray();
            $managers = User::role('manager')->get();

            return Inertia::render('Teams/Create', [
                'partners' => $partners,
                'staff' => $staff,
                'managers' => $managers,

                'partner' => $partner,
                'staf' => $staf,
                'manager' => $manager,
            ]);
        } else {
            if(!$partner)
            {
                return Redirect::route('teams')->with('warning', 'Partner not found, Can\'t create team without partner.');
            } elseif(!$manager)
            {
                return Redirect::route('teams')->with('warning', 'Manager not found, Can\'t create team without manager.');
            } elseif(!$staf)
            {
                return Redirect::route('teams')->with('warning', 'Staff not found, Can\'t create team without staff.');
            } else
            {
                return Redirect::route('teams')->with('warning', 'User not found, May some members are missing to create a team.');
            }
        }
    }

    public function store()
    {
        Request::validate([
            'partner' => ['required'],
            'manager' => ['required'],
            'staff' => ['required'],
        ]);

        $year = Year::find(session('year_id'));
        $company = Company::find(session('company_id'));

        $partner_id = Request::input('partner')['id'];
        $manager_id = Request::input('manager')['id'];

        // dd($company, $year, $company->users()->where('user_id', $partner_id)->first());
        if(!$company->users()->where('user_id', $partner_id)->first())
        {
            $company->users()->attach($partner_id);
            Setting::create([
                'key' => 'active_company',
                'value' => $company->id,
                'user_id' => $partner_id,
            ]);
            Setting::create([
                'key' => 'active_year',
                'value' => $year->id,
                'user_id' => $partner_id,
            ]);
        }
        if(!$company->users()->where('user_id', $manager_id)->first())
        {
            $company->users()->attach($manager_id);
            Setting::create([
                'key' => 'active_company',
                'value' => $company->id,
                'user_id' => $manager_id,
            ]);
            Setting::create([
                'key' => 'active_year',
                'value' => $year->id,
                'user_id' => $manager_id,
            ]);
        }

        $year->users()->attach($partner_id);
        $year->users()->attach($manager_id);

        $staff = Request::input('staff');
        foreach($staff as $staf)
        {
            $year->users()->attach($staf['id']);
            if(!$company->users()->where('user_id', $staf['id'])->first())
            {
                $company->users()->attach($staf['id']);
                Setting::create([
                    'key' => 'active_company',
                    'value' => $company->id,
                    'user_id' => $staf['id'],
                ]);
                Setting::create([
                    'key' => 'active_year',
                    'value' => $year->id,
                    'user_id' => $staf['id'],
                ]);
            }
        }
        return Redirect::route('teams')->with('success', 'Team created.');
    }

    public function edit()
    {
        $year = Year::find(session('year_id'));

        $partner = $year->users()->role('partner')->first();
        $manager = $year->users()->role('manager')->first();
        $staf = $year->users()->role('staff')->get();

        $partners = User::role('partner')->get();
        $staff = User::role('staff')->get();
        $managers = User::role('manager')->get();

        return Inertia::render('Teams/Edit', [
            'partners' => $partners,
            'staff' => $staff,
            'managers' => $managers,

            'partner' => $partner,
            'staf' => $staf,
            'manager' => $manager,
        ]);
    }

    public function update()
    {
        Request::validate([
            'partner' => ['required'],
            'manager' => ['required'],
            'staff' => ['required'],
        ]);

        $year = Year::find(session('year_id'));
        $pre_users = $year->users()->get();
        foreach($pre_users as $pre_user)
        {
            $year->users()->detach($pre_user->id);
        }

        $company = Year::find(session('company_id'));
        $pre_users = $company->users()->get();
        foreach($pre_users as $pre_user)
        {
            $company->users()->detach($pre_user->id);
        }


        $partner_id = Request::input('partner')['id'];
        $manager_id = Request::input('manager')['id'];
        $staff = Request::input('staff');

        $year->users()->attach($partner_id);
        $year->users()->attach($manager_id);
        $company->users()->attach($partner_id);
        $company->users()->attach($manager_id);
        foreach($staff as $staf)
        {
            $year->users()->attach($staf['id']);
            $company->users()->attach($staf['id']);
        }

        return Redirect::route('teams')->with('success', 'Team updated.');
    }
}
