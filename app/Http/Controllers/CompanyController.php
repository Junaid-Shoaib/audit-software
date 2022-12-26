<?php

namespace App\Http\Controllers;
use App\Models\AccountGroup;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Company;
use App\Models\Year;
use App\Models\Setting;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use App;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class CompanyController extends FileMangementController
{

    // Listing
    public function index()
    {
        //Validating request
        request()->validate([
            'direction' => ['in:asc,desc'],
            'field' => ['in:name,email']
        ]);

        $query =
        auth()->user()->companies()->getQuery()->paginate(10)
            ->withQueryString()
            ->through(
                fn ($comp) =>
                [
                    //if getting data from companies_users table then the "id = $comp->company_id" otherwise "id = $comp->id"
                    'id' => $comp->company_id,
                    'name' => $comp->name,
                    'address' => $comp->address,
                    'email' => $comp->email,
                    'web' => $comp->web,
                    'phone' => $comp->phone,
                    'fiscal' => $comp->fiscal,
                    'incorp' => $comp->incorp,
                    'delete' => Year::where('company_id', $comp->id)->first() != null ? true : false,
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

        return Inertia::render('Company/Index', [
            // 'can' => [
            //     'edit' => auth()->user()->can('edit'),
            //     'create' => auth()->user()->can('create'),
            //     'delete' => auth()->user()->can('delete'),
            //     'read' => auth()->user()->can('read'),
            // ],
            'balances' => $query,
            'filters' => request()->all(['search', 'field', 'direction'])
        ]);
    }

    //Craeat Function
    public function create()
    {
        $fiscals = ['June', 'March', 'September', 'December'];
        $fiscal_first = 'June';

        return Inertia::render('Company/Create', [
            'fiscals' => $fiscals, 'fiscal_first' => $fiscal_first
        ]);
    }

    // Store function
    public function store()
    {
        Request::validate([
            'name' => ['required', 'unique:companies'],
            'fiscal' => ['required'],
        ]);

        DB::transaction(function () {

            // Create Company
            $company = Company::create([
                'name' => strtoupper(Request::input('name')),
                'address' => Request::input('address'),
                'email' => Request::input('email'),
                'web' => Request::input('web'),
                'phone' => Request::input('phone'),
                'fiscal' => Request::input('fiscal'),
                'incorp' => Request::input('incorp'),
            ]);

            //Attach Company_user table.
            $company->users()->attach(auth()->user()->id);

            // Create Year Functionality start

            //Start Month & End Month
            $startMonth = Carbon::parse($company->fiscal)->month + 1;
            $endMonth = Carbon::parse($company->fiscal)->month;
            if ($startMonth == 13) {
                $startMonth = 1;
            }

            //Start Month Day & End Month Day
            $startMonthDays = 1;
            $endMonthDays = Carbon::create()->month($endMonth)->daysInMonth;

            // Year Get
            $today = Carbon::today();
            $startYear = 0;
            $endYear = 0;
            if ($startMonth == 1) {
                $startYear = $today->year;
                $endYear = $today->year;
            } else {
                $endYear = ($today->month >= $startMonth) ? $today->year + 1 : $today->year;
                $startYear = $endYear - 1;
            }

            $startDate = $startYear . '-' . '0' . $startMonth . '-' . $startMonthDays;
            $endDate = $endYear . '-' . '0' . $endMonth . '-' . $endMonthDays;

            $year = Year::create([
                'begin' => $startDate,
                'end' => $endDate,
                'company_id' => $company->id,
            ]);

            // Create Year Functionality End

            // Create Active Company Setting
            Setting::create([
                'key' => 'active_company',
                'value' => $company->id,
                'user_id' => Auth::user()->id,
            ]);

            // Create Active Year Setting
            Setting::create([
                'key' => 'active_year',
                'value' => $year->id,
                'user_id' => Auth::user()->id,
            ]);

            session(['company_id' => $company->id]);
            session(['year_id' => $year->id]);

            Storage::makeDirectory('/public/' . $company->id);

            Storage::makeDirectory('/public/' . $company->id . '/' . $year->id);


            // Calling the function from DefaultFoldersCreation controller ---- to generate the default folder
            $this->defaultFolders();
        });
            session(['team_id' => null]);
        return Redirect::route('companies')->with('success', 'Company created');
    }

    //Edit Function
    public function edit(Company $company)
    {
        return Inertia::render('Company/Edit', [
            'company' => [
                'id' => $company->id,
                'name' => $company->name,
                'address' => $company->address,
                'email' => $company->email,
                'web' => $company->web,
                'phone' => $company->phone,
                'fiscal' => $company->fiscal,
                'incorp' => $company->incorp,
            ],
        ]);
    }

    // Update function
    public function update(Company $company)
    {
        Request::validate([
            'name' => ['required'],
            'address' => ['nullable'],
            'email' => ['nullable'],
            'web' => ['nullable'],
            'phone' => ['nullable'],
            'fiscal' => ['required'],
            'incorp' => ['nullable'],
        ]);

        $company->name = Request::input('name');
        $company->address = Request::input('address');
        $company->email = Request::input('email');
        $company->web = Request::input('web');
        $company->phone = Request::input('phone');
        $company->fiscal = Request::input('fiscal');

        $incorp = new carbon(Request::input('incorp'));
        $company->incorp = $incorp->format('Y-m-d');

        $company->save();

        return Redirect::route('companies')->with('success', 'Company updated.');
    }

    public function destroy(Company $company)
    {
        $company->delete();
        return Redirect::back()->with('success', 'Company deleted.');
    }

    //TO CHANGE THE COMPANY IN SESSION FROM DROPDOWN
    public function coch($id)
    {
        $active_co = Setting::where('user_id', Auth::user()->id)->where('key', 'active_company')->first();
        $active_co->value = $id;
        $active_co->save();
        session(['company_id' => $id]);

        if (Year::where('company_id', $id)->latest()->first()) {
            $active_yr = Setting::where('user_id', Auth::user()->id)->where('key', 'active_year')->first();
            $active_yr->value = Year::where('company_id', $id)->latest()->first()->id;
            $active_yr->save();

            $active_yr = Year::where('company_id', $id)->latest()->first();
            session(['year_id' => $active_yr->id]);
            if($active_yr->users()->first()){
                session(['team_id' => $active_yr->users()->first()->id]);
            }else{
                session(['team_id' => null]);
            }
            return Redirect::back();
        } else {
            session(['year_id' => null]);
            return Redirect::route('years.create')->with('success', 'YEAR NOT FOUND. Please create an Year for selected Company.');
        }
    }


    // Trial Template Download Function
    public function trial_pattern(){
        return response()->download(public_path('/trial_upload.xlsx'));
    }


}
