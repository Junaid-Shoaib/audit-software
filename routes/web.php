<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


use Illuminate\Http\Request;

use App\Http\Controllers\UserController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\YearController;
use App\Http\Controllers\AccountGroupController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\FileMangementController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\DetailController;
use App\Http\Controllers\DefaultFoldersCreation;
use Illuminate\Support\Facades\Artisan;

//Confirmation Controller

use App\Http\Controllers\BankController;
use App\Http\Controllers\AdviserAccountController;
use App\Http\Controllers\AdvisorController;
use App\Http\Controllers\BankBranchController;
use App\Http\Controllers\BankAccountController;
use App\Http\Controllers\BankBalanceController;
use App\Http\Controllers\BankConfirmationController;
use App\Http\Controllers\AdviserConfirmationController;

// To read Excel file
use App\Http\Controllers\Excel;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
});


// To read the Excel File
Route::get('trial', [Excel::class, 'index'])->name('trial.index')->middleware('auth');
Route::post('trial/read', Excel::class)->name('trial.read')->middleware('auth');
Route::get('lead', [Excel::class, 'lead'])->name('lead')->middleware('auth');
Route::get('materiality-download', [Excel::class, 'materiality'])->name('materiality')->middleware('auth');

//COMPANIES -------------------- STARTS ---------------------------
Route::get('companies', [CompanyController::class, 'index'])
    ->name('companies')
    ->middleware('auth');

Route::get('companies/create', [CompanyController::class, 'create'])
    ->name('companies.create')
    ->middleware('auth');

Route::post('companies', [CompanyController::class, 'store'])
    ->name('companies.store')
    ->middleware('auth');

Route::get('companies/{company}/edit', [CompanyController::class, 'edit'])
    ->name('companies.edit')
    ->middleware('auth');

Route::put('companies/{company}', [CompanyController::class, 'update'])
    ->name('companies.update')
    ->middleware('auth');

Route::delete('companies/{company}', [CompanyController::class, 'destroy'])
    ->name('companies.destroy')
    ->middleware('auth');

//TO CHANGE COMPANY THE FROM DROPDOWN
Route::get('companies/coch/{id}', [CompanyController::class, 'coch'])
    ->name('companies.coch');

Route::get('company-pdf/{fiscal}', [CompanyController::class, 'companypdf'])
->name('companypdf')
->middleware('auth');


Route::get('trialpattern', [CompanyController::class, 'trial_pattern'])->name('trial.pattern')->middleware('auth');


//COMPANIES -------------------- END ---------------------------


//YEARS ------------------------------------ STARTS ------------------
Route::get('years', [YearController::class, 'index'])
    ->name('years')
    ->middleware('auth');

Route::get('years/create', [YearController::class, 'create'])
    ->name('years.create')
    ->middleware('auth');

Route::post('years', [YearController::class, 'store'])
    ->name('years.store')
    ->middleware('auth');

Route::get('years/{year}/edit', [YearController::class, 'edit'])
    ->name('years.edit')
    ->middleware('auth');

Route::put('years/{year}', [YearController::class, 'update'])
    ->name('years.update')
    ->middleware('auth');

Route::delete('years/{year}', [YearController::class, 'destroy'])
    ->name('years.destroy')
    ->middleware('auth');

//TO CHANGE YEAR THE FROM DROPDOWN
Route::get('years/yrch/{id}', [YearController::class, 'yrch'])
    ->name('years.yrch');

Route::get('years/{year}/close', [YearController::class, 'close'])
    ->name('years.close')
    ->middleware('auth');
//YEARS ------------------------------------ END ------------------
//New User ------------------------------------ START ------------------
Route::get('users', [UserController::class, 'index'])
    ->name('users')
    ->middleware('auth');
Route::get('users/create', [UserController::class, 'create'])
    ->name('users.create')
    ->middleware('auth');
Route::post('users', [UserController::class, 'store'])
    ->name('users.store')
    ->middleware('auth');
//New User ------------------------------------ END ------------------



//Template ----------------------- STARTS --------------------
Route::get('templates', [TemplateController::class, 'index'])
    ->name('templates')
    ->middleware('auth');

Route::get('templates/create', [TemplateController::class, 'create'])
    ->name('templates.create')
    ->middleware('auth');

Route::post('templates', [TemplateController::class, 'store'])
    ->name('templates.store')
    ->middleware('auth');

Route::get('template/download/{id}', [TemplateController::class, 'temp_download'])
    ->name('temp_download')
    ->middleware('auth');


Route::delete('template/delete/{id}', [TemplateController::class, 'destroy'])
    ->name('templates.destroy')
    ->middleware('auth');


//ACCOUNTS GROUPS ----------------------- END --------------------
// Route::get('accountgroups/generate', [GroupSeeder::class, 'run'])
//     ->name('accountgroups.generate')
//     ->middleware('auth');

Route::get('accountgroups', [AccountGroupController::class, 'index'])
    ->name('accountgroups')
    ->middleware('auth');

// Route::get('accountgroups/create', [AccountGroupController::class, 'create'])
//     ->name('accountgroups.create')
//     ->middleware('auth');

// Route::post('accountgroups/create', [AccountGroupController::class, 'create'])
//     ->name('accountgroups.create')
//     ->middleware('auth');

Route::post('accountgroups', [AccountGroupController::class, 'store'])
    ->name('accountgroups.store')
    ->middleware('auth');

Route::get('accountgroups/{accountgroup}/edit', [AccountGroupController::class, 'edit'])
    ->name('accountgroups.edit')
    ->middleware('auth');

Route::put('accountgroups/{accountgroup}', [AccountGroupController::class, 'update'])
    ->name('accountgroups.update')
    ->middleware('auth');

Route::delete('accountgroups/{accountgroup}', [AccountGroupController::class, 'destroy'])
    ->name('accountgroups.destroy')
    ->middleware('auth');
//ACCOUNTS GROUPS ----------------------- END --------------------


//ACCOUNTS ----------------------- STARTS --------------------
Route::get('accounts', [AccountController::class, 'index'])
    ->name('accounts')
    ->middleware('auth');

Route::get('accounts/create', [AccountController::class, 'create'])
    ->name('accounts.create')
    ->middleware('auth');

Route::post('accounts', [AccountController::class, 'store'])
    ->name('accounts.store')
    ->middleware('auth');

Route::get('accounts/{account}/edit', [AccountController::class, 'edit'])
    ->name('accounts.edit')
    ->middleware('auth');

Route::put('accounts/{account}', [AccountController::class, 'update'])
    ->name('accounts.update')
    ->middleware('auth');

Route::delete('accounts/{account}', [AccountController::class, 'destroy'])
    ->name('accounts.destroy')
    ->middleware('auth');
//ACCOUNTS ----------------------- END --------------------



//File Management ------------------------- STARTS --------------------------------------

Route::get('filing/createFolder', [FileMangementController::class, 'createFolder'])
    ->name('filing.createFolder')
    ->middleware('auth');

Route::post('filing/folder', [FileMangementController::class, 'storeFolder'])
    ->name('filing.store.folder')
    ->middleware('auth');

Route::get('filing/uploadFile/{folder_id}', [FileMangementController::class, 'uploadFile'])
    ->name('filing.uploadFile')
    ->middleware('auth');

Route::post('filing/file/{parent_id}', [FileMangementController::class, 'storeFile'])
    ->name('filing.store.file')
    ->middleware('auth');

Route::get('filing/downloadFile/{file_id}', [FileMangementController::class, 'downloadFile'])
    ->name('filing.downloadFile')
    ->middleware('auth');


Route::get('filing/deleteFileFolder/{file_folder_id}', [FileMangementController::class, 'deleteFileFolder'])
    ->name('filing.deleteFileFolder')
    ->middleware('auth');

Route::get('filing/folder', [FileMangementController::class, 'folder'])
    ->name('filing.folder')
    ->middleware('auth');


Route::middleware('auth')->controller(FileMangementController::class)->group(function () {
    Route::get('/filing/{parent_name_id}', 'filing')->name('filing');
    Route::get('/template/{type}', 'index_temp')->name('index_temp');
    // Route::get('/filing/execution/{parent_name_id?}', 'folder')->name('folder');
    Route::get('filing/createFolder', 'createFolder')->name('filing.createFolder');
    Route::post('filing', 'storeFolder')->name('filing.storeFolder');
    Route::get('/template-download/{id?}', 'download_temp')->name('download_temp');
    Route::get('/multiple-template-download', 'multi_download_temp')->name('multi_download_temp');
    Route::post('/include-templates', 'include_templates')->name('include_templates');
    Route::post('/approve-files', 'approve_files')->name('approve_files');
    Route::post('/reject-files/{review}', 'reject_files')->name('reject_files');
});



//File Management ------------------------- END --------------------------------------


//TEAMS ----------------------- STARTS --------------------
Route::get('teams', [TeamController::class, 'index'])
    ->name('teams')
    ->middleware('auth');

Route::get('teams/create', [TeamController::class, 'create'])
    ->name('teams.create')
    ->middleware('auth');

Route::post('teams', [TeamController::class, 'store'])
    ->name('teams.store')
    ->middleware('auth');

Route::get('teams/edit', [TeamController::class, 'edit'])
    ->name('teams.edit')
    ->middleware('auth');

Route::put('teams', [TeamController::class, 'update'])
    ->name('teams.update')
    ->middleware('auth');

Route::delete('teams/{team}', [TeamController::class, 'destroy'])
    ->name('teams.destroy')
    ->middleware('auth');
//TEAMS ----------------------- END --------------------

//Details ----------------------- STARTS --------------------
Route::get('details', [DetailController::class, 'index'])
    ->name('details')
    ->middleware('auth');

Route::get('details/create', [DetailController::class, 'create'])
    ->name('details.create')
    ->middleware('auth');

Route::post('details', [DetailController::class, 'store'])
    ->name('details.store')
    ->middleware('auth');

Route::get('details/edit/{account_id}', [DetailController::class, 'edit'])
    ->name('details.edit')
    ->middleware('auth');

Route::put('details/{detail}', [DetailController::class, 'update'])
    ->name('details.update')
    ->middleware('auth');

Route::delete('details/{account_id}', [DetailController::class, 'destroy'])
    ->name('details.destroy')
    ->middleware('auth');


Route::get('download-details/{account_id}', [DetailController::class, 'download_details'])
    ->name('download.details')
    ->middleware('auth');

Route::post('import-details', [DetailController::class, 'import_details'])
    ->name('import.details')
    ->middleware('auth');

//Details ----------------------- END --------------------



//Confirmation Start Route-------------------------------------------------


// Adviosr

Route::get('advisors', [AdvisorController::class, 'index'])
    ->name('advisors')
    ->middleware('auth');


Route::get('advisors/create', [AdvisorController::class, 'create'])
    ->name('advisors.create')
    ->middleware('auth');

Route::get('advisors/{advisor}', [AdvisorController::class, 'show'])
    ->name('advisors.show')
    ->middleware('auth');

Route::post('advisors', [AdvisorController::class, 'store'])
    ->name('advisors.store')
    ->middleware('auth');

Route::get('advisors/{advisor}/edit', [AdvisorController::class, 'edit'])
    ->name('advisors.edit')
    ->middleware('auth');

Route::put('advisors/{advisor}', [AdvisorController::class, 'update'])
    ->name('advisors.update')
    ->middleware('auth');

Route::delete('advisors/{advisor}', [AdvisorController::class, 'destroy'])
    ->name('advisors.destroy')
    ->middleware('auth');

//AdviserAccount

Route::get('advisor_accounts', [AdviserAccountController::class, 'index'])
    ->name('advisor_accounts')
    ->middleware('auth');

Route::get('advisor_accounts/create', [AdviserAccountController::class, 'create'])
    ->name('advisor_accounts.create')
    ->middleware('auth');

// Route::get('advisor_accounts/{account}', [AdviserAccountController::class, 'show'])
//     ->name('accounts.show')
//     ->middleware('auth');

Route::post('advisor_accounts', [AdviserAccountController::class, 'store'])
    ->name('advisor_accounts.store')
    ->middleware('auth');

Route::get('advisor_account/edit', [AdviserAccountController::class, 'edit'])
    ->name('advisor_account.edit')
    ->middleware('auth');

Route::put('advisor_accounts/{account}', [AdviserAccountController::class, 'update'])
    ->name('advisor_accounts.update')
    ->middleware('auth');

Route::delete('advisor_accounts/{adviserAccount}', [AdviserAccountController::class, 'destroy'])
    ->name('advisor_accounts.destroy')
    ->middleware('auth');

// Banks

Route::get('banks', [BankController::class, 'index'])
    ->name('banks')
    ->middleware('auth');

// Route::get('banks/create', [BankController::class, 'create'])
//     ->name('banks.create')
//     ->middleware('auth');

Route::get('banks/{accounts}/create', [BankController::class, 'create'])
    ->name('banks.create')
    ->middleware('auth');

Route::get('banks/{bank}', [BankController::class, 'show'])
    ->name('banks.show')
    ->middleware('auth');

Route::post('banks', [BankController::class, 'store'])
    ->name('banks.store')
    ->middleware('auth');

Route::get('banks/{bank}/edit', [BankController::class, 'edit'])
    ->name('banks.edit')
    ->middleware('auth');

Route::put('banks/{bank}', [BankController::class, 'update'])
    ->name('banks.update')
    ->middleware('auth');

Route::delete('banks/{bank}', [BankController::class, 'destroy'])
    ->name('banks.destroy')
    ->middleware('auth');

// Bank Branches

Route::get('branches', [BankBranchController::class, 'index'])
    ->name('branches')
    ->middleware('auth');

Route::get('branches/{accounts}create', [BankBranchController::class, 'create'])
    ->name('branches.create')
    ->middleware('auth');

Route::get('branches/branchchange/{id}', [BankBranchController::class, 'branchchange'])
    ->name('branches.branchchange');

Route::get('branches/{branch}', [BankBranchController::class, 'show'])
    ->name('branches.show')
    ->middleware('auth');

Route::post('branches', [BankBranchController::class, 'store'])
    ->name('branches.store')
    ->middleware('auth');

Route::get('branches/{branch}/edit', [BankBranchController::class, 'edit'])
    ->name('branches.edit')
    ->middleware('auth');

Route::put('branches/{branch}', [BankBranchController::class, 'update'])
    ->name('branches.update')
    ->middleware('auth');

Route::delete('branches/{branch}', [BankBranchController::class, 'destroy'])
    ->name('branches.destroy')
    ->middleware('auth');


// Bank Accounts

Route::get('bank_accounts', [BankAccountController::class, 'index'])
    ->name('bank_accounts')
    ->middleware('auth');

Route::get('bank_accounts/create', [BankAccountController::class, 'create'])
    ->name('bank_accounts.create')
    ->middleware('auth');

Route::get('bank_accounts/{account}', [BankAccountController::class, 'show'])
    ->name('bank_accounts.show')
    ->middleware('auth');

Route::post('bank_accounts', [BankAccountController::class, 'store'])
    ->name('bank_accounts.store')
    ->middleware('auth');

Route::get('bank_accountss/edit', [BankAccountController::class, 'edit'])
    ->name('bank_accounts.edit')
    ->middleware('auth');

Route::put('bank_accounts/{account}', [BankAccountController::class, 'update'])
    ->name('bank_accounts.update')
    ->middleware('auth');

Route::delete('bank_accounts/{account}', [BankAccountController::class, 'destroy'])
    ->name('bank_accounts.destroy')
    ->middleware('auth');

// Bank Balances

Route::get('balances', [BankBalanceController::class, 'index'])
    ->name('balances')
    ->middleware('auth');

Route::get('balances/create', [BankBalanceController::class, 'create'])
    ->name('balances.create')
    ->middleware('auth');

Route::get('balances/{balance}', [BankBalanceController::class, 'show'])
    ->name('balances.show')
    ->middleware('auth');

Route::post('balances', [BankBalanceController::class, 'store'])
    ->name('balances.store')
    ->middleware('auth');

Route::get('bal/edit', [BankBalanceController::class, 'edity'])
    ->name('bal.edit')
    ->middleware('auth');



Route::put('balances/{balance?}', [BankBalanceController::class, 'update'])
    ->name('balances.update')
    ->middleware('auth');

Route::delete('balances/{balance}', [BankBalanceController::class, 'destroy'])
    ->name('balances.destroy')
    ->middleware('auth');

// Bank confirmations

Route::get('confirmations', [BankConfirmationController::class, 'index'])
    ->name('confirmations')
    ->middleware('auth');

Route::get('confirmations/create', [BankConfirmationController::class, 'create'])
    ->name('confirmations.create')
    ->middleware('auth');

Route::get('confirmations/{confirmation}', [BankConfirmationController::class, 'show'])
    ->name('confirmations.show')
    ->middleware('auth');

Route::post('confirmations', [BankConfirmationController::class, 'store'])
    ->name('confirmations.store')
    ->middleware('auth');


Route::get('confirmation/edit', [BankConfirmationController::class, 'edit'])
    ->name('confirmations.edit')
    ->middleware('auth');

Route::put('balances_updated/{id}', [BankConfirmationController::class, 'updated'])
->name('balances.updated')
->middleware('auth');

Route::put('confirmations/{balance}', [BankConfirmationController::class, 'update'])
    ->name('confirmations.update')
    ->middleware('auth');

Route::delete('confirmations/{confirmation}', [BankConfirmationController::class, 'destroy'])
    ->name('confirmations.destroy')
    ->middleware('auth');

Route::get('bankConfig', [BankConfirmationController::class, 'bankConfig'])
->name('bankConfig')
->middleware('auth');

Route::get('bankconfirmUpload/{id}', [BankConfirmationController::class, 'bankconfirmUpload'])
->name('bankconfirmUpload')
->middleware('auth');


Route::get('pd', [BankConfirmationController::class, 'pd'])
    ->name('pd')
    ->middleware('auth');
Route::get('ex', [BankConfirmationController::class, 'ex'])
    ->name('ex')
    ->middleware('auth');

Route::get('word', [BankConfirmationController::class, 'word'])
    ->name('word')
    ->middleware('auth');

Route::get('branchespdf', [BankConfirmationController::class, 'branchespdf'])
->name('branchespdf')
->middleware('auth');



// Advisor confirmations

Route::get('advisor_confirmations', [AdviserConfirmationController::class, 'index'])
    ->name('advisor_confirmations')
    ->middleware('auth');

Route::get('advisor_confirmations/create', [AdviserConfirmationController::class, 'create'])
    ->name('advisor_confirmations.create')
    ->middleware('auth');

Route::get('advisor_confirmations/{advisor_confirmation}', [AdviserConfirmationController::class, 'show'])
    ->name('advisor_confirmations.show')
    ->middleware('auth');

Route::post('advisor_confirmations', [AdviserConfirmationController::class, 'store'])
    ->name('advisor_confirmations.store')
    ->middleware('auth');


Route::get('advisor_confirmationsP/edit', [AdviserConfirmationController::class, 'edit'])
    ->name('advisor_confirmations.edit')
    ->middleware('auth');

Route::put('advisor_updated/{id}', [AdviserConfirmationController::class, 'advisorupload'])
->name('advisor.updated')
->middleware('auth');

Route::get('advisorconfirmUpload/{id}', [AdviserConfirmationController::class, 'advisorconfirmUpload'])
->name('advisorconfirmUpload')
->middleware('auth');

Route::put('advisor_confirmations/{advisor_confirmation}', [AdviserConfirmationController::class, 'update'])
    ->name('advisor_confirmations.update')
    ->middleware('auth');

Route::delete('advisor_confirmations/{advisor_confirmation}', [AdviserConfirmationController::class, 'destroy'])
    ->name('advisor_confirmations.destroy')
    ->middleware('auth');

Route::get('advisor_word', [AdviserConfirmationController::class, 'advisor_word'])
->name('advisor_word')
->middleware('auth');

Route::get('advisorspdf', [AdviserConfirmationController::class, 'advisorspdf'])
->name('advisorspdf')
->middleware('auth');


//Confirmation End Route---------------------------------------------------














Route::get('/routes', function () {
    //Clear Route cache:
    $exitCode = Artisan::call('route:clear');
    //Route cache:
    $exitCode2 = Artisan::call('route:cache');
    return back()->with('success', 'Cache clear');
    return '<h1>Route cache cleared</h1>';
});
