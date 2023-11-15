<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Http\Request as Req;
use Illuminate\Support\Facades\Auth;
use App\Models\Year;
use App\Models\Setting;
use App\Models\Company;
use App\Models\Team;
use Illuminate\Support\Facades\Artisan;
use Inertia\Inertia;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;


class YearController extends FileMangementController
{
    public function index(Req $req)
    {
        // session()->forget('team_id');
        // dd(session('team_id'));
        if (Company::first()) {
            // $query = Year::query();

            if (request()->has(
                'search'
            )) {
                $obj_data =
                    auth()->user()->years()->where(
                        'begin',
                        'LIKE',
                        '%' . $req->search . '%'
                    )
                    ->wherePivot('company_id', session('company_id'));
            } else {
                $obj_data = auth()->user()->years()->wherePivot('company_id', session('company_id'));
            }

            $mapped_data = $obj_data->get()->map(function ($year, $key) {
                return [
                    $begin = new Carbon($year->begin),
                    $end = new Carbon($year->end),
                    'id' => $year->id,
                    'closed' => $year->closed,
                    'begin' => $begin->format('F,j Y'),
                    'end' => $end->format('F,j Y'),
                    'company_name' => $year->company->name,
                    'company_id' => $year->company_id,
                    ];
            });

            return Inertia::render('Years/Index', [
                'mapped_data' => $mapped_data,
                'company' => Company::where('id', session('company_id'))->first(),
                'companies' => Auth::user()->companies,

            ]);
        } else {
            return Redirect::route('companies')->with('warning', 'Create Company first');
        }
    }

    // Add Year function
    public function create()
    {
        $year = Year::where('company_id', session('company_id'))->latest()->first();
        $begin = explode('-', $year->begin);
        $begin[0]++;
        $end = explode('-', $year->end);
        $end[0]++;
        $newBegin = implode('-', $begin);
        $newEnd = implode('-', $end);

        $year = Year::create([
            'begin' => $newBegin,
            'end' => $newEnd,
            'company_id' => session('company_id'),
        ]);
        // $year->users()->attach(auth()->user()->id);
        $year->users()->attach(auth()->user()->id, ['company_id' => session('company_id')]);
        session(['year_id' => $year->id]);
        if (count($year->users()->get()) > 2) {
            session(['team_id' => $year->users()->first()->id]);
        } else {
            session(['team_id' => null]);
        }

        Storage::makeDirectory('/public/' . $year->company_id . '/' . $year->id);
        // Calling the function from DefaultFoldersCreation controller ---- to generate the default folder
        $this->defaultFolders();
        Artisan::call('db:seed --class=TemplateSeeder');
        return Redirect::back()->with('success', 'Year created.');
    }

    public function edit(Year $year)
    {
        return Inertia::render('Years/Edit', [
            'year' => [
                'id' => $year->id,
                'begin' => $year->begin,
                'end' => $year->end,
                'company_id' => session('company_id'),
            ],
        ]);
    }

    public function update(Year $year)
    {
        Request::validate([
            'begin' => ['required', 'date'],
            'end' => ['required', 'date'],
        ]);

        $begin = new carbon(Request::input('begin'));
        $end = new carbon(Request::input('end'));

        $year->begin = $begin->format('Y-m-d');
        $year->end = $end->format('Y-m-d');
        $year->company_id = session('company_id');
        $year->save();

        return Redirect::route('years')->with('success', 'Year updated.');
    }

    public function destroy(Year $year)
    {

        $year->delete();
        if (Year::where('company_id', session('company_id'))->first()) {
            return Redirect::back()->with('success', 'Year deleted.');
        } else {
            session(['year_id' => null]);
            return Redirect::route('years.create')->with('success', 'YEAR NOT FOUND. Please create an Year for selected Company.');
        }
        // }
    }

    // Year Change Dropdown functionality
    public function yrch($id)
    {
        $active_yr = Setting::where('user_id', Auth::user()->id)->where('key', 'active_year')->first();

        $active_yr->value = $id;
        $active_yr->save();
        session(['year_id' => $id]);

        $years = Year::find($id);
        if ($years) {
            if (count($years->users()->get()) > 2) {
                session(['team_id' => $years->users()->first()->id]);
            } else {
                session(['team_id' => null]);
            }
        } else {
            return Redirect::back();
        }
        return Redirect::back();
    }
}
