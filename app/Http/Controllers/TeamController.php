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
use Illuminate\Support\Facades\DB;

class TeamController extends Controller
{
    public function index()
    {
        // checking the current user have a company or not
        if (auth()->user()->companies()->first()) {
            //Validating request
            request()->validate([
                'direction' => ['in:asc,desc'],
                'field' => ['in:name,email']
            ]);

            $year = Year::find(session('year_id'));
            // checking the year for current user
            if ($year) {
                $query =
                    $year->users()->getQuery()->paginate(10)
                    ->withQueryString()
                    ->through(
                        fn ($user) =>
                        [
                            // dd($user),
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

                // For ant-design data table ---------

                $obj_data = $year->users()->get();
                $mapped_data = $obj_data->map(function ($user, $key) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        $cur_user = User::find($user->id),
                        'role' => $cur_user->getRoleNames()[0],
                        // 'delete' => Year::where('company_id', $comp->id)->first() != null ? true : false,
                    ];
                });
            } else {
                return Redirect::route('years')->with('warning', 'Year not found');
            }

            return Inertia::render('Teams/Index', [
                'mapped_data' => $mapped_data,
                // 'can' => [
                //     'edit' => auth()->user()->can('edit'),
                //     'create' => auth()->user()->can('create'),
                //     'delete' => auth()->user()->can('delete'),
                //     'read' => auth()->user()->can('read'),
                // ],
                'balances' => $query,
                'filters' => request()->all(['search', 'field', 'direction']),

                'team_exists' => count($query) > 0 ? true : false,
            ]);
        } else {
            return Redirect::route('companies')->with('warning', 'Create Company first');
        }
    }


    public function create()
    {
        $partner = User::where('location', auth()->user()->location)->role('partner')->first();
        $staf = [User::where('location', auth()->user()->location)->role('staff')->first()];
        $manager = User::where('location', auth()->user()->location)->role('manager')->first();

        // if we have at least one manager, partner and 1 staff only then user can
        // redirect to create page otherwise he/she have to create these
        if ($partner && $manager && $staf) {
            $partners = User::where('location', auth()->user()->location)->role('partner')->get();
            $staff = User::where('location', auth()->user()->location)->role('staff')->get()->toArray();
            $managers = User::where('location', auth()->user()->location)->role('manager')->get();

            return Inertia::render('Teams/Create', [
                'partners' => $partners,
                'staff' => $staff,
                'managers' => $managers,

                'partner' => $partner,
                'staf' => $staf,
                'manager' => $manager,
            ]);
        } else {
            if (!$partner) {
                return Redirect::route('teams')->with('warning', 'Partner not found, Can\'t create team without partner.');
            } elseif (!$manager) {
                return Redirect::route('teams')->with('warning', 'Manager not found, Can\'t create team without manager.');
            } elseif (!$staf) {
                return Redirect::route('teams')->with('warning', 'Staff not found, Can\'t create team without staff.');
            } else {
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


        $pre_team_memb = $year->users()->get();
        foreach ($pre_team_memb as $team_mem) {
            $year->users()->detach($team_mem->id, ['company_id' => $company->id]);
        }


        $partner_id = Request::input('partner');
        $manager_id = Request::input('manager');
        $auth_connect = false;
        if (Auth::user()->id == $partner_id || Auth::user()->id == $manager_id) {
            $auth_connect = true;
        }



        /* attaching the users with the company & year to create team
                    we have pivot table of companies_users & years_users
                    to create team we attach the user with the year because team can be change for next year
                    and attaching company to the user because we showing/listing companies according to the users
                */

        // checking ig already we have connection with this user or not
        if (!$company->users()->where('user_id', $partner_id)->first()) {
            $company->users()->attach($partner_id);
            // Setting::create([
            //     'key' => 'active_company',
            //     'value' => $company->id,
            //     'user_id' => $partner_id,
            // ]);
            // Setting::create([
            //     'key' => 'active_year',
            //     'value' => $year->id,
            //     'user_id' => $partner_id,
            // ]);
            $set_comp = Setting::where('user_id', $partner_id)->where('key', 'active_company')->first();
            $set_year = Setting::where('user_id', $partner_id)->where('key', 'active_year')->first();
            if ($set_comp) {
                $set_comp->value = $company->id;
                $set_comp->save();
            } else {
                // Create Active Company Setting
                Setting::create([
                    'key' => 'active_company',
                    'value' => $company->id,
                    'user_id' => $partner_id,
                ]);
            }
            if ($set_year) {
                $set_year->value = $year->id;
                $set_year->save();
            } else {
                // Create Active Year Setting
                Setting::create([
                    'key' => 'active_year',
                    'value' => $year->id,
                    'user_id' => $partner_id,
                ]);
            }
        }
        if (!$company->users()->where('user_id', $manager_id)->first()) {
            $company->users()->attach($manager_id);
            $set_comp = Setting::where('user_id', $manager_id)->where('key', 'active_company')->first();
            $set_year = Setting::where('user_id', $manager_id)->where('key', 'active_year')->first();
            if ($set_comp) {
                $set_comp->value = $company->id;
                $set_comp->save();
            } else {
                // Create Active Company Setting
                Setting::create([
                    'key' => 'active_company',
                    'value' => $company->id,
                    'user_id' => $manager_id,
                ]);
            }
            if ($set_year) {
                $set_year->value = $year->id;
                $set_year->save();
            } else {
                // Create Active Year Setting
                Setting::create([
                    'key' => 'active_year',
                    'value' => $year->id,
                    'user_id' => $manager_id,
                ]);
            }
        }

        $year->users()->attach($partner_id, ['company_id' => session('company_id')]);
        $year->users()->attach($manager_id, ['company_id' => session('company_id')]);

        //staff can be multiple that's why using foreach loop
        // if user select only one staff member
        $staff = Request::input('staff');
        if (is_int($staff)) {
            $staf = $staff;
            if (Auth::user()->id == $staf) {
                $auth_connect = true;
            }
            $year->users()->attach($staf, ['company_id' => session('company_id')]);
            if (!$company->users()->where('user_id', $staf)->first()) {
                $company->users()->attach($staf, ['company_id' => session('company_id')]);

                $set_comp = Setting::where('user_id', $staf)->where('key', 'active_company')->first();
                $set_year = Setting::where('user_id', $staf)->where('key', 'active_year')->first();
                if ($set_comp) {
                    $set_comp->value = $company->id;
                    $set_comp->save();
                } else {
                    // Create Active Company Setting
                    Setting::create([
                        'key' => 'active_company',
                        'value' => $company->id,
                        'user_id' => $staf,
                    ]);
                }
                if ($set_year) {
                    $set_year->value = $year->id;
                    $set_year->save();
                } else {
                    // Create Active Year Setting
                    Setting::create([
                        'key' => 'active_year',
                        'value' => $year->id,
                        'user_id' => $staf,
                    ]);
                }
            }
            if (count($year->users()->get()) > 2) {
                session(['team_id' => 54554]);
            } else {
                session(['team_id' => null]);
            }
        } else {
            foreach ($staff as $staf) {
                if (Auth::user()->id == $staf) {
                    $auth_connect = true;
                }
                $year->users()->attach($staf, ['company_id' => session('company_id')]);
                if (!$company->users()->where('user_id', $staf)->first()) {
                    $company->users()->attach($staf, ['company_id' => session('company_id')]);

                    $set_comp = Setting::where('user_id', $staf)->where('key', 'active_company')->first();
                    $set_year = Setting::where('user_id', $staf)->where('key', 'active_year')->first();
                    if ($set_comp) {
                        $set_comp->value = $company->id;
                        $set_comp->save();
                    } else {
                        // Create Active Company Setting
                        Setting::create([
                            'key' => 'active_company',
                            'value' => $company->id,
                            'user_id' => $staf,
                        ]);
                    }
                    if ($set_year) {
                        $set_year->value = $year->id;
                        $set_year->save();
                    } else {
                        // Create Active Year Setting
                        Setting::create([
                            'key' => 'active_year',
                            'value' => $year->id,
                            'user_id' => $staf,
                        ]);
                    }
                }
            }
            if (count($year->users()->get()) > 2) {
                session(['team_id' => 54554]);
            } else {
                session(['team_id' => null]);
            }
        }

        if (!$auth_connect) {
            $year->users()->detach(Auth::user()->id, ['company_id' => $company->id]);

            $cur_comp_year = Year::where('company_id', $company->id)->where('id', '!=', $year->id)->get();
            $auth_user_year = Auth::user()->years()->wherePivot('company_id', session('company_id'))->get();
            if (count($cur_comp_year) > 0 && count($auth_user_year) > 0) {
                $condition_break = true;
                foreach ($cur_comp_year as $comp_year) {
                    foreach ($auth_user_year as $auth_year) {
                        if ($comp_year->id == $auth_year->id) {
                            // $comp_year->users()->attach($auth_year->id, ['company_id' => $company->id]);
                            session(['year_id' => $auth_year->id]);
                            $set_year = Setting::where('user_id', $staf)->where('key', 'active_year')->first();
                            $set_year->value = $auth_year->id;
                            $set_year->save();
                            if (count($auth_year->users()->get()) > 2) {
                                // session(['team_id' => $auth_year->users()->first()->id]);
                                session(['team_id' => 25252]);
                            } else {
                                session(['team_id' => null]);
                            }
                            $condition_break = false;
                            break;
                        }
                    }
                }
                if ($condition_break) {
                    $company->users()->detach(Auth::user()->id);
                    $auth_set_comp = Setting::where('user_id', Auth::user()->id)->where('key', 'active_company')->first();
                    $auth_set_year = Setting::where('user_id', Auth::user()->id)->where('key', 'active_year')->first();
                    if (Auth::user()->companies()->first()) {
                        $auth_comp = Auth::user()->companies()->first()->id;

                        $year_to_set = Year::where('company_id', $auth_comp)->latest()->first();
                        $auth_set_comp->value = $auth_comp;
                        $auth_set_comp->save();
                        $auth_set_year->value = $year_to_set->id;
                        $auth_set_year->save();
                        session(['company_id' => $auth_comp]);
                        session(['year_id' => $year_to_set->id]);
                        if (count($year_to_set->users()->get()) > 2) {
                            // session(['team_id' => $auth_year->users()->first()->id]);
                            session(['team_id' => 3563563]);
                        } else {
                            session(['team_id' => null]);
                        }
                    } else {
                        $auth_set_comp->delete();
                        $auth_set_year->delete();
                        session(['company_id' => null]);
                        session(['year_id' => null]);
                        session(['team_id' => null]);
                    }
                }
            } else {
                $company->users()->detach(Auth::user()->id);
                $auth_set_comp = Setting::where('user_id', Auth::user()->id)->where('key', 'active_company')->first();
                $auth_set_year = Setting::where('user_id', Auth::user()->id)->where('key', 'active_year')->first();
                if (Auth::user()->companies()->first()) {
                    $auth_comp = Auth::user()->companies()->first()->id;

                    $year_to_set = Year::where('company_id', $auth_comp)->latest()->first();
                    $auth_set_comp->value = $auth_comp;
                    $auth_set_comp->save();
                    $auth_set_year->value = $year_to_set->id;
                    $auth_set_year->save();
                    session(['company_id' => $auth_comp]);
                    session(['year_id' => $year_to_set->id]);
                    if (count($year_to_set->users()->get()) > 2) {
                        // session(['team_id' => $year_to_set->users()->first()->id]);
                        session(['team_id' => 1451451]);
                    } else {
                        session(['team_id' => null]);
                    }
                } else {
                    $auth_set_comp->delete();
                    $auth_set_year->delete();
                    session(['company_id' => null]);
                    session(['year_id' => null]);
                    session(['team_id' => null]);
                }
            }
        }

        // if ($year) {

        //     if (count($year->users()->get()) > 2) {
        //         session(['team_id' => 54554]);
        //     } else {
        //         session(['team_id' => null]);
        //     }
        // } else {
        //     session(['team_id' => null]);
        // }




        return Redirect::route('teams')->with('success', 'Team created.');
    }

    public function edit()
    {
        $year = Year::find(session('year_id'));

        $partner = $year->users()->role('partner')->get();
        $manager = $year->users()->role('manager')->get();
        $staf = $year->users()->role('staff')->get();

        $partners = User::where('location', auth()->user()->location)->role('partner')->get();
        $staff = User::where('location', auth()->user()->location)->role('staff')->get();
        $managers = User::where('location', auth()->user()->location)->role('manager')->get();

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
        // same scenerio as storing team


        $year = Year::find(session('year_id'));
        $pre_users = $year->users()->get();
        $settings_year = Setting::where('value', session('year_id'))
            ->where('key', 'active_year')->get();

        foreach ($settings_year as $s_year) {
            if ($s_year) {
                $s_year->delete();
            }
        }

        $settings_company = Setting::where('value', session('company_id'))
            ->where('key', 'active_company')->get();

        foreach ($settings_company as $s_company) {
            if ($s_company) {
                $s_company->delete();
            }
        }


        foreach ($pre_users as $pre_user) {
            $year->users()->detach($pre_user->id);
        }

        $company = Company::find(session('company_id'));
        $pre_users = $company->users()->get();
        foreach ($pre_users as $pre_user) {
            $company->users()->detach($pre_user->id);
        }


        $partner_id = Request::input('partner');
        $manager_id = Request::input('manager');
        $staff = Request::input('staff');

        $year->users()->attach($partner_id);
        $company->users()->attach($partner_id);
        // dd($partner_id[0]);
        if (is_int($partner_id)) {
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
        } else {
            Setting::create([
                'key' => 'active_company',
                'value' => $company->id,
                'user_id' => $partner_id[0],
            ]);
            Setting::create([
                'key' => 'active_year',
                'value' => $year->id,
                'user_id' => $partner_id[0],
            ]);
        }

        $year->users()->attach($manager_id);
        $company->users()->attach($manager_id);
        if (is_int($manager_id)) {
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
        } else {
            Setting::create([
                'key' => 'active_company',
                'value' => $company->id,
                'user_id' => $manager_id[0],
            ]);
            Setting::create([
                'key' => 'active_year',
                'value' => $year->id,
                'user_id' => $manager_id[0],
            ]);
        }
        foreach ($staff as $staf) {
            $year->users()->attach($staf);
            $company->users()->attach($staf);
            Setting::create([
                'key' => 'active_company',
                'value' => $company->id,
                'user_id' => $staf,
            ]);
            Setting::create([
                'key' => 'active_year',
                'value' => $year->id,
                'user_id' => $staf,
            ]);
        }

        $user = User::where('email', auth()->user()->email)->first();
        if ($user) {

            if ($user->settings()->where('key', 'active_company')->first()) {
                session(['company_id' => $user->settings()->where('key', 'active_company')->first()->value]);
            } else {
                session(['company_id' => null]);
            }
            if ($user->settings()->where('key', 'active_year')->first()) {
                session(['year_id' => $user->settings()->where('key', 'active_year')->first()->value]);
                $active_yr = Year::where('id', session('year_id'))->first();
                if ($active_yr->users()->first()) {
                    session(['team_id' => $active_yr->users()->first()->id]);
                } else {
                    session(['team_id' => null]);
                }
            } else {
                session(['year_id' => null]);
            }
        }

        return Redirect::route('teams')->with('success', 'Team updated.');
    }
}
