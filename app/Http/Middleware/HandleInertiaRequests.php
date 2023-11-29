<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use Illuminate\Support\Facades\Auth;
use App\Models\Company;
use App\Models\Year;
use Carbon\Carbon;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Defines the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'co_id' => session('company_id'),
            'yr_id' => session('year_id'),
            'team_id' => session('team_id'),
            'company' =>
            Company::where('id', session('company_id'))->first(),
            'companies' => Auth::check() ?
                Auth::user()->companies ?
                Auth::user()->companies()->whereHas('users', function ($query) {
                    $query->where('location', Auth::user()->location);
                })->get()
                : null
                : Company::get(),
            'year' => Year::where('company_id', session('company_id'))->where('id', session('year_id'))->get()->map(function($row){
                $start = Carbon::create($row->begin)->format('F j, Y');
                $end = Carbon::create($row->end)->format    ('F j, Y');
                return [
                    'id' => $row->id,
                    'end' => $start .' - '. $end,
                ];    
            })->first(),
            'years' => Auth::check() ?
                Auth::user()->years() ?
                Auth::user()->years()->wherePivot('company_id', session('company_id'))->get()->map(function($row){
                    $start = Carbon::create($row->begin)->format('F j, Y');
                    $end = Carbon::create($row->end)->format('F j, Y');
                    return [
                        'id' => $row->id,
                        'end' => $start .' - '. $end,
                    ];    
                })
                : null
                : Year::get(),
            'team_id' => session('team_id'),
            'team_id' => session('team_id'),
            'flash' => function () use ($request) {
                return [
                    'success' => $request->session()->get('success'),
                    'warning' => $request->session()->get('warning'),
                    'error' => $request->session()->get('error'),
                ];
            },
        ]);
    }
}
