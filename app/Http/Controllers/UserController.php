<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request as Req;
use App\Models\User;
use App\Models\Company;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Jetstream\Jetstream;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Inertia\Inertia;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Redirect;

class UserController extends Controller
{
    public function index(Req $req)
    {
        if (auth()->user()->can('manage') || auth()->user()->can('edit')) {
            //Validating request
            request()->validate([
                'direction' => ['in:asc,desc'],
                'field' => ['in:name,email']
            ]);

            // $user = User::first();
            //             dd($user->getRoleNames()[0]);

            $query =
                // auth()->user()->companies()->getQuery()->paginate(10)
                User::getQuery()->paginate(10)
                ->withQueryString()
                ->through(
                    fn ($user) =>
                    [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        $cur_user = User::find($user->id),
                        'role' => $cur_user->getRoleNames()[0],
                        // 'delete' => Year::where('company_id', $comp->id)->first() != null ? true : false,
                    ],
                );

            // if (request('search')) {
            //     $query->where('name', 'LIKE', '%' . request('search') . '%');
            // }

            // if (request()->has(['field', 'direction'])) {
            //     $query->orderBy(
            //         request('field'),
            //         request('direction')
            //     );
            // }
            if (request()->has(
                // ['select', 'search']
                'search'
            )) {
                $obj_data = User::where('location', auth()->user()->location)
                    ->where(function ($query) use ($req) {
                        $query->where('name', 'like', '%' . $req->search . '%')
                            ->orWhere('email', 'like', '%' . $req->search . '%');
                        // whereHas('roles', function($q) use ($req) {
                        //     $q->where('roles.name',$req->search);
                        // })

                        // ->orWhere($cur_user->getRoleNames()[0], 'like', '%' . $req->search . '%');
                    })
                    ->get();
            } else {
                $obj_data = User::where('location', auth()->user()->location)->get();
            }
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


            return Inertia::render('Users/Index', [
                'can' => [
                    'manage' => auth()->user()->can('manage'),
                    'edit' => auth()->user()->can('edit'),
                    'create' => auth()->user()->can('create'),
                    'delete' => auth()->user()->can('delete'),
                    'read' => auth()->user()->can('read'),
                ],
                'mapped_data' => $mapped_data,
                'balances' => $query,
                'filters' => request()->all(['search', 'field', 'direction']),
            ]);
        } else {
            return Redirect::route('companies')->with('error', 'You don\'t have access for this page');
        }
    }

    public function create()
    {
        return Inertia::render('Users/Create');
    }

    public function store()
    {
        Request::validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            // 'password' => $this->passwordRules(),
            'password' => ['required', 'min:8', 'same:password_confirmation'],
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ]);

        $role = Role::where('name', Request::input('role'))->first();

        User::create([
            'name' => Request::input('name'),
            'location' => auth()->user()->location,
            'email' => Request::input('email'),
            'password' => Hash::make(Request::input('password')),
        ])->assignRole($role);
        return Redirect::route('users')->with('success', 'User created');
    }
}
