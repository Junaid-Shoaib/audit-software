<?php

namespace App\Http\Controllers;

// use Illuminate\Http\Request;
use Illuminate\Http\Request as Req;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\FileManager;
use App\Models\Template;
use App\Models\Company;
use App\Models\Year;
use Inertia\Inertia;
use Carbon\Carbon;
use ZipArchive;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class FileMangementController extends Controller
{
    //----------- url parameter can be name if hiting the link from dashboard otherwise it will be id
    public function filing($parent_name_id, Req $req)
    {
        $folders = null;
        $fold = null;
        $plan_comp_name = '';
        // -------- if the value of parameter is ID then we get an objec in $fold variable otherwise it'll be null
        $fold = FileManager::find($parent_name_id);

        //------- if fold variable is not null then we are temprary saving the name of that folder in $plan_comp_name
        if ($fold) {
            $plan_comp_name = $fold->name;
        }

        // --- condition -------- if parameter is String and equal to planing or completion  OR  we get an object of planing or completion folder
        if (
            $parent_name_id == 'planing' || $parent_name_id == 'completion' || $parent_name_id == 'report'
            || $plan_comp_name == 'planing' || $plan_comp_name == 'completion' || $plan_comp_name == 'report'
        ) {
            /*  ----- if $plan_comp_name is not null and we pass the upper condition then it's mean
                  we get an object of Planing or completion folder
                  in this scenerio we have id in $parent_name_id and we are using it as a name of folder
                  that's why we set that with the object name we store in $plan_comp_name
                  */
            if ($plan_comp_name != '') {
                $parent_name_id = $plan_comp_name;
            }
            $parent = FileManager::all()->where('company_id', session('company_id'))
                ->where('year_id', session('year_id'))
                ->where('name', $parent_name_id)
                ->map(function ($obj) {
                    return [
                        'id' => $obj->id,
                        'name' => ucfirst($obj->name),
                        'is_folder' => $obj->is_folder,
                        'parent_id' => $obj->parent_id,
                        'type' => $obj->name == 'execution' ? 'Folder' : 'File',
                    ];
                })
                ->first();
        } else {
            // if we are in this condition then it's mean the id we get is belong's to the execution folder children
            $execution = FileManager::all()->where('company_id', session('company_id'))
                ->where('year_id', session('year_id'))
                ->where('name', 'execution')
                ->map(function ($obj) {
                    return [
                        'id' => $obj->id,
                        'name' => ucfirst($obj->name),
                        'is_folder' => $obj->is_folder,
                        'parent_id' => $obj->parent_id,
                        'type' => $obj->name == 'execution' ? 'Folder' : 'File',
                    ];
                })
                ->first();

            if ($execution) {
                $folders = FileManager::where('company_id', session('company_id'))
                    ->where('year_id', session('year_id'))
                    ->where('parent_id', $execution['id'])
                    ->where('is_folder', '0')
                    ->get();


                if ($parent_name_id == 'execution') {
                    $parent = FileManager::all()->where('company_id', session('company_id'))
                        ->where('year_id', session('year_id'))
                        ->where('parent_id', $execution['id'])
                        ->map(function ($obj) {
                            return [
                                'id' => $obj->id,
                                'name' => ucfirst($obj->name),
                                'is_folder' => $obj->is_folder,
                                'parent_id' => $obj->parent_id,
                                'type' => $obj->name == 'execution' ? 'Folder' : 'File',
                            ];
                        })
                        ->first();
                } else {
                    $parent = FileManager::all()->where('company_id', session('company_id'))
                        ->where('year_id', session('year_id'))
                        ->where('id', $parent_name_id)
                        ->map(function ($obj) {
                            return [
                                'id' => $obj->id,
                                'name' => ucfirst($obj->name),
                                'is_folder' => $obj->is_folder,
                                'parent_id' => $obj->parent_id,
                                'type' => $obj->name == 'execution' ? 'Folder' : 'File',
                            ];
                        })
                        ->first();
                }
            } else {
                return Redirect::route('trial.index')->with('warning', 'Please Upload Trial first.');
            }
        }

        //if get parent then we can show their childrens otherwise we can't track folders or file
        if ($parent) {
            //Validating request
            request()->validate([
                'direction' => ['in:asc,desc'],
                'field' => ['in:name,email']
            ]);

            //Searching request
            $query = FileManager::query();
            if (request('search')) {
                $query->where('name', 'LIKE', '%' . request('search') . '%');
            }

            // checking the Auth user to render the data according to user roles and permissions
            if (Auth::user()->roles[0]->name == "staff" || Auth::user()->roles[0]->name == "super-admin")
            {
                // For ant-design data table ----------------
                if(request()->has('search'))
                {
                    $obj_data = FileManager::where('company_id', session('company_id'))
                        ->where('year_id', session('year_id'))
                        ->where('is_folder', 1)
                        ->where('parent_id', $parent['id'])
                        ->where('name', 'like', '%' . $req->search . '%')
                        ->get();
                }
                else {
                    $obj_data = FileManager::where('company_id', session('company_id'))
                        ->where('year_id', session('year_id'))
                        ->where('is_folder', 1)
                        ->where('parent_id', $parent['id'])
                        ->get();
                }
                $mapped_data = $obj_data->map(function($obj, $key) {
                return [
                        'id' => $obj->id,
                        'name' => $obj->name,
                        'is_folder' => $obj->is_folder,
                        'parent_id' => $obj->parent_id,
                        // 'review' => $obj->staff_review == 1 ? $obj->manager_approval == 1 ? 'Approved' : 'Pending' : $obj->manager_review,
                        'review' => $obj->manager_review == null ? '' : $obj->manager_review,
                        // 'delete' => Entry::where('account_id', $account->id)->first() ? false : true,
                        'approve' => $obj->staff_approval == 1 ? true : false,
                    ];
                });
                $balances_name = FileManager::where('company_id', session('company_id'))
                    ->where('year_id', session('year_id'))
                    ->where('is_folder', 1)
                    ->where('parent_id', $parent['id'])->get()->pluck('name');

            } else if (Auth::user()->roles[0]->name == "manager") {
                // For ant-design data table ----------------
                if(request()->has('search'))
                {
                    $obj_data = FileManager::where('company_id', session('company_id'))
                        ->where('year_id', session('year_id'))
                        ->where('is_folder', 1)
                        ->where('staff_approval', 1)
                        ->where('parent_id', $parent['id'])
                        ->where('name', 'like', '%' . $req->search . '%')
                        ->get();
                }
                else {
                    $obj_data = FileManager::where('company_id', session('company_id'))
                        ->where('year_id', session('year_id'))
                        ->where('is_folder', 1)
                        ->where('staff_approval', 1)
                        ->where('parent_id', $parent['id'])
                        ->get();
                }
                $mapped_data = $obj_data->map(function($obj, $key) {
                return [
                        'id' => $obj->id,
                        'name' => $obj->name,
                        'is_folder' => $obj->is_folder,
                        'parent_id' => $obj->parent_id,
                        'review' => $obj->partner_review == null ? '' : $obj->partner_review,
                        // 'review' => $obj->staff_approval == 1 ? $obj->manager_approval == 1 ? 'Approved' : 'Pending' : $obj->manager_review,
                        // 'delete' => Entry::where('account_id', $account->id)->first() ? false : true,
                        'approve' => $obj->manager_approval == 1 ? true : false,
                    ];
                });

                $balances_name = FileManager::where('company_id', session('company_id'))
                    ->where('year_id', session('year_id'))
                    ->where('is_folder', 1)
                    ->where('staff_approval', 1)
                    ->where('parent_id', $parent['id'])->get()->pluck('name');
            } else if (Auth::user()->roles[0]->name == "partner") {
                // For ant-design data table ----------------
                if(request()->has('search'))
                {
                    $obj_data = FileManager::where('company_id', session('company_id'))
                        ->where('year_id', session('year_id'))
                        ->where('is_folder', 1)
                        ->where('staff_approval', 1)
                        ->where('manager_approval', 1)
                        ->where('parent_id', $parent['id'])
                        ->where('name', 'like', '%' . $req->search . '%')
                        ->get();
                }
                else {
                    $obj_data = FileManager::where('company_id', session('company_id'))
                        ->where('year_id', session('year_id'))
                        ->where('is_folder', 1)
                        ->where('staff_approval', 1)
                        ->where('manager_approval', 1)
                        ->where('parent_id', $parent['id'])
                        ->get();
                }
                $mapped_data = $obj_data->map(function($obj, $key) {
                return [
                        'id' => $obj->id,
                        'name' => $obj->name,
                        'is_folder' => $obj->is_folder,
                        'parent_id' => $obj->parent_id,
                        // 'review' => $obj->staff_approval == 1 ? $obj->manager_approval == 1 ? 'Approved' : 'Pending' : $obj->manager_review,
                        // 'delete' => Entry::where('account_id', $account->id)->first() ? false : true,
                        'approve' => $obj->partner_approval == 1 ? true : false,
                    ];
                });

                $balances_name = FileManager::where('company_id', session('company_id'))
                    ->where('year_id', session('year_id'))
                    ->where('is_folder', 1)
                    ->where('staff_approval', 1)
                    ->where('manager_approval', 1)
                    ->where('parent_id', $parent['id'])->get()->pluck('name');
            }

            $first = FileManager::where('company_id', session('company_id'))
                ->where('year_id', session('year_id'))
                ->where('parent_id', $parent['id'])
                ->first();

            return Inertia::render('Filing/Index', [
                'mapped_data' => $mapped_data,
                'filters' => request()->all(['search', 'field', 'direction']),
                'user_role' => Auth::user()->roles[0]->name,
                'balances_name' => $balances_name,
                'first' => $first,
                'company' => Company::where('id', session('company_id'))->first(),
                'companies' => Auth::user()->companies,
                'parent' => $parent,
                'folders' => $folders,
            ]);
        } else {
            return Redirect::route('companies')->with('warning', 'Please create company first to excess these folders.');
        }
    }

    // to display all the folder of execution(of current company & current year) for folder modification
    public function folder(Req $req)
    {
        if (Company::first()) {
            $execution = FileManager::all()->where('company_id', session('company_id'))
                ->where('year_id', session('year_id'))
                ->where('name', 'execution')
                ->map(function ($obj) {
                    return [
                        'id' => $obj->id,
                        'name' => ucfirst($obj->name),
                        'is_folder' => $obj->is_folder,
                        'parent_id' => $obj->parent_id,
                        'type' => $obj->name == 'execution' ? 'Folder' : 'File',
                    ];
                })
                ->first();

            // //Validating request
            // request()->validate([
            //     'direction' => ['in:asc,desc'],
            //     'field' => ['in:name,email']
            // ]);

            //Searching request
            $query = FileManager::query();
            // if (request('search')) {
            //     $query->where('name', 'LIKE', '%' . request('search') . '%');
            // }

            $balances = $query
                ->where('company_id', session('company_id'))
                ->where('year_id', session('year_id'))
                ->where('is_folder', 0)
                ->where('parent_id', $execution['id'])
                ->paginate(10)
                ->through(
                    function ($obj) {
                        return
                            [
                                'id' => $obj->id,
                                'name' => $obj->name,
                                'is_folder' => $obj->is_folder,
                                'parent_id' => $obj->parent_id,
                                'delete' => FileManager::where('company_id', session('company_id'))
                                    ->where('year_id', session('year_id'))
                                    ->where('parent_id', $obj->id)
                                    ->where('is_folder', 1)
                                    ->first() ? false : true,
                            ];
                    }
                );

            if(request()->has('search')){
                $obj_data = FileManager::where('company_id', session('company_id'))
                ->where('year_id', session('year_id'))
                ->where('is_folder', 0)
                ->where('parent_id', $execution['id'])
                ->where('name','LIKE', '%'.$req->search.'%')
                ->get();
            }
            else{
                $obj_data = FileManager::where('company_id', session('company_id'))
                ->where('year_id', session('year_id'))
                ->where('is_folder', 0)
                ->where('parent_id', $execution['id'])->get();
            }
            $mapped_data = $obj_data->map(function($obj, $key) {
                return [
                    'id' => $obj->id,
                    'name' => $obj->name,
                    'is_folder' => $obj->is_folder,
                    'parent_id' => $obj->parent_id,
                    'delete' => FileManager::where('company_id', session('company_id'))
                        ->where('year_id', session('year_id'))
                        ->where('parent_id', $obj->id)
                        ->where('is_folder', 1)
                        ->first() ? false : true,
                    ];
                });

            return Inertia::render('Filing/FolderIndex', [
                'mapped_data' => $mapped_data,
                'filters' => request()->all(['search', 'field', 'direction']),
                'balances' => $balances,
                'company' => Company::where('id', session('company_id'))->first(),
                'companies' => Auth::user()->companies,
            ]);
        } else {
            return Redirect::route('companies')->with('warning', 'Please create company first to excess these folders.');
        }
    }

    public function createFolder()
    {
        return Inertia::render('Filing/CreateFolder');
    }

    public function storeFolder()
    {
        // to create a directory in execution folder of current company & current year directory
        Request::validate([
            'name' => ['required'],
        ]);

        $parent = FileManager::where('company_id', session('company_id'))
            ->where('year_id', session('year_id'))
            ->where('name', 'execution')
            ->first();

        $folderObj = FileManager::create([
            // 'name' => strtoupper(Request::input('name')),   //FOLDER
            'name' => ucfirst(strtolower(Request::input('name'))),  //Folder
            'is_folder' => 0,
            'parent_id' => $parent->id,
            'year_id' => session('year_id'),
            'company_id' => session('company_id'),
            'path' => session('company_id') . '/' . session('year_id') . '/' . $parent->id,
        ]);
        $folderObj->path = $folderObj->path . '/' . $folderObj->id;
        $folderObj->save();
        Storage::makeDirectory('/public/' . $folderObj->company_id . '/' . $folderObj->year_id . '/' . $folderObj->parent_id . '/' . $folderObj->id);
        return Redirect::route("filing.folder")->with('success', 'Folder created.');
    }

    public function uploadFile($folder_id)
    {
        $parent = FileManager::find($folder_id);
        return Inertia::render('Filing/UploadFile', [
            'parent' => $parent,
        ]);
    }

    public function storeFile(Request $request, $parent_id)
    {
        // Request::validate([
        //     'avatar'=> ['required'],
        //     // 'avatar'=> 'required | mimes:pdf,docx,xlsx,jpeg,jpg,png',
        // ]);
        if(!Request::file('avatar')) {
            return back()->with('error', 'File not selected');
        }

        //Custome validation of file type ...because laravel validation giving error on some files
        $extension = Request::file('avatar')->getClientOriginalExtension();
        if ($extension == 'pdf' || $extension == 'docx' || $extension == 'xlsx' || $extension == 'jpeg' || $extension == 'jpg' || $extension == 'png') {
        } else {
            return back()->with('error', 'The file must be a file of type: pdf, docx, xlsx, jpeg, jpg, png.');
        }

        $parent = FileManager::find($parent_id);
        $grand_parent = FileManager::where('id', $parent->parent_id)->first();
        /*
        grand parent exists if the user uploading a file in the folder which exists in execution folder
            if grand_parent doesn't exist then its mean user uploading a file in planning or completion folder
        */
        if ($grand_parent) {
            $path = session('company_id') . '/' . session('year_id') . '/' . $grand_parent->id . '/' . $parent_id;
        } else {
            $path = session('company_id') . '/' . session('year_id') . '/' . $parent_id;
        }
        // $name = time() . '_' . Request::file('avatar')->getClientOriginalName();
        $name = Request::file('avatar')->getClientOriginalName();


        $file_exists = FileManager::where('company_id', session('company_id'))
            ->where('year_id', session('year_id'))
            ->where('name', $name)->where('parent_id', $parent_id)
            ->where('is_folder', 1)
            ->first();
        if (!$file_exists) {
            $pathWithFileName = Request::file('avatar')->storeAs($path, $name, 'public');
            $folderObj = FileManager::create([
                'name' => $name,
                'is_folder' => 1,
                'parent_id' => $parent_id,
                'path' => $pathWithFileName,
                'year_id' => session('year_id'),
                'company_id' => session('company_id'),
            ]);
        } else {
            $approve_check = Auth::user()->roles[0]->name . '_approval';
            if ($file_exists->$approve_check == 0) {
                $pathWithFileName = Request::file('avatar')->storeAs($path, $name, 'public');
            } else {
                return back()->with('error', 'File exists and approved.');
            }
        }
        //sending parameter value "$parent->id" because we have to show the folder where we upload the file
        return Redirect::route("filing", [$parent->id])->with('success', 'File upload.');
    }

    public function downloadFile($file_id)
    {
        $file_obj = FileManager::find($file_id);
        return response()->download(storage_path('app/public/' . $file_obj->path));
    }

    public function deleteFileFolder(FileManager $file_folder_id)
    {
        try {
            /* if is_folder property is 0 then its mean that object is a folder so
              we have to delete it files first then can delete the folder
            */
            if ($file_folder_id->is_folder == 0) {
                $type = 'Folder';
                $files = FileManager::where('parent_id', $file_folder_id->id)->get();
                if (count($files) > 0) {
                    foreach ($files as $file) {
                        Storage::delete('public/' . $file->path);
                        $file->delete();
                    }
                }
                Storage::deleteDirectory('public/' . $file_folder_id->path);
            } else {
                $type = 'File';
                Storage::delete('public/' . $file_folder_id->path);
            }
            $file_folder_id->delete();
            return back()->with('success', $type . ' deleted');
        } catch (Throwable $e) {
            return back()->with('error', $e);
        }
        return back()->with('error', 'Something went wrong, check network connection and try again');
    }




    // ------------- TO CREEATE DEFAULT FOLDER ON COMPANY and YEAR GENERATION -------
    public function defaultFolders()
    {
        $constFoldersName = [
            'planing', 'completion', 'report', 'execution',
            //ASSETS
            'Fixed Assets', 'Investment Properties', 'Investments',
            'Long Term Loans And Advances', 'Long Term Deposits And Prepayments', 'Stores, Spares And Stock-In-Trade',
            'Trade Debts', 'Advances, Deposits, Prepayments & Other Receivable', 'Cash & Bank Balances',
            //LIABILITIES
            'Accrued Expenses', 'Contingencies & Commitments', 'Deferred Liabilities',
            'Direct Taxation', 'Dividend Payable', 'Equity',
            'Liabilities Against Assets', 'Long Term Debt', 'Long Term Deposit',
            'Payables', 'Short Term Borrowings', 'Surplus on Revaluation',
            //PROFIT AND LOSS
            'Sales', 'Cost Of Sales', 'Admin Expense',
            'Financial Charges', 'Other Income',
        ];

        $parent_id = null;
        foreach ($constFoldersName as $name) {
            $folderObj = FileManager::create([
                'name' => $name,
                'is_folder' => 0,
                'parent_id' => $parent_id,
                'year_id' => session('year_id'),
                'company_id' => session('company_id'),
                'path' => session('company_id') . '/' . session('year_id'),
            ]);

            // for those objects which are without parent folders ------- planing, completion and execution
            if ($parent_id == null) {
                $folderObj->path = $folderObj->path . '/' . $folderObj->id;
                $folderObj->save();
                Storage::makeDirectory('/public/' . $folderObj->company_id . '/' . $folderObj->year_id . '/' . $folderObj->id);
            } else {
                // object with parent(excution)
                $folderObj->path = $folderObj->path . '/' . $folderObj->parent_id . '/' . $folderObj->id;
                $folderObj->save();
                Storage::makeDirectory('/public/' . $folderObj->company_id . '/' . $folderObj->year_id . '/' . $folderObj->parent_id . '/' . $folderObj->id);
            }

            // storing execution object id in parent id
            if ($name == 'execution') {
                $parent_id = $folderObj->id;
            }
        }
        return true;
    }



    /*
     type paramter because templates are divided into 3 categories
        planning, completion and execution
    */
    public function index_temp(Req $req, $type)
    {
        $execution = FileManager::all()->where('company_id', session('company_id'))
            ->where('year_id', session('year_id'))
            ->where('name', 'execution')
            ->map(function ($obj) {
                return [
                    'id' => $obj->id,
                    'name' => ucfirst($obj->name),
                    'is_folder' => $obj->is_folder,
                    'parent_id' => $obj->parent_id,
                    'type' => $obj->name == 'execution' ? 'Folder' : 'File',
                ];
            })
            ->first();

        $folders = FileManager::where('company_id', session('company_id'))
            ->where('year_id', session('year_id'))
            ->where('parent_id', $execution['id'])
            ->where('is_folder', '0')
            ->get();

        //Validating request
        $year = Year::where('company_id', session('company_id'))
            ->where('id', session('year_id'))->first();
        /*
        checking the current year have team or not .... for team scenerio we have pivot table of years_users
            if team is created only then we show the templates otherwise redirect the user to create the temm
        */
        if (count($year->users)) {
            request()->validate([
                'direction' => ['in:asc,desc'],
                'field' => ['in:name,email']
            ]);

            //Searching request
            $query = Template::query();
            if (request('search')) {
                $query->where('name', 'LIKE', '%' . request('search') . '%');
            }

            // getting templates of user requested type
            $balances = $query
                ->where('type', lcfirst($type))
                ->get()
                ->map(
                    function ($temp) {
                        return
                            [
                                'id' => $temp->id,
                                'name' => $temp->name,
                                'path' => $temp->path,
                                'type' => $temp->type,
                            ];
                    }
                );

            // names of the templates to perform the select all or multi-select functionality
            $balances_name = Template::where('type', $type)->get()->pluck('name');

            if(request()->has(
                // ['select', 'search']
                'search'
                )){
                $obj_data = Template::where('company_id', session('company_id'))
                    ->where('type', lcfirst($type))
                    ->where(
                        // $req->select
                        'name'
                        ,'LIKE', '%'.$req->search.'%')
                    ->get();
            }
            else{
                $obj_data = Template::where('company_id', session('company_id'))
                    ->where('type', lcfirst($type))
                    ->get();
            }
            $mapped_data = $obj_data->map(function($temp, $key) {
                return [
                    'id' => $temp->id,
                    'name' => $temp->name,
                    'path' => $temp->path,
                    'type' => $temp->type,
                ];
            });

            return Inertia::render('Filing/IndexTemplate', [
                'mapped_data' => $mapped_data,
                'filters' => request()->all(['search', 'field', 'direction']),
                'balances' => $balances,
                'balances_name' => $balances_name,

                'company' => Company::where('id', session('company_id'))->first(),
                'companies' => auth()->user()->companies,
                'type' => ucfirst($type),
                'folders' => $folders,
            ]);
        } else {
            return back()->with('warning', 'Please Create Team First');
        }
    }

    // to download the template or templates
    public function multi_download_temp(Req $request)
    {
        if ($request->selected_arr != null) {
            $arr = explode(",", $request->selected_arr);
            // checking the request is to download single file or multiple files
            if (count($arr) == 1) {
                $template = $this->download_temp($arr);
                return response()->download(storage_path('app/public/' . $template->path));
            } else {
                if (File::exists(public_path() . '/templates.zip')) {
                    File::delete(public_path() . '/templates.zip');
                }
                $templates = Template::whereIn('name', $arr)->get();
                $this->file_temp($templates);

                // making zip folder for multiple files
                $zip   = new ZipArchive;
                $fileName = 'templates.zip';
                if ($zip->open(public_path($fileName), ZipArchive::CREATE) === TRUE) {
                    foreach ($templates as $key => $template) {
                        $file = storage_path('app/public/' . $template->path);
                        // $relativeName = basename($value);
                        $zip->addFile($file, $template->name);
                    }
                    $zip->close();
                }
                return response()->download(public_path($fileName));
            }
        } else {
            return back()->with('warning', 'Template Not Selected');
        }
    }


    // override dynamic data when Import templates to Planning, completion or execution folder's
    public function file_temp($templates)
    {
        if ($templates) {
            $year = Year::where('company_id', session('company_id'))
                ->where('id', session('year_id'))->first();
            $partner = $year->users()->role('partner')->first();
            $manager = $year->users()->role('manager')->first();
            $staff = $year->users()->role('staff')->first();
            foreach ($templates as $key => $template) {
                # code...
                $extension =   explode(".", ($template->name));
                //   dd($partner->name , $manager->name , $staff->name);

                if ($partner != null && $manager != null && $staff != null) {
                    $start = $year->begin ? new Carbon($year->begin) : null;
                    $end = $year->end ? new Carbon($year->end) : null;
                    $names = str_replace(["&"], "&amp;", $year->company->name);
                    $name = $year->company->name;
                    if (strtolower($extension[1]) == 'docx' || strtolower($extension[1]) == 'docs') {
                        $templateProcessor = new  \PhpOffice\PhpWord\TemplateProcessor(public_path('temp/' . $template->name));
                        $templateProcessor->setValue('client', $names);
                        $templateProcessor->setValue('partner', ucwords($partner->name));
                        $templateProcessor->setValue('manager', ucwords($manager->name));
                        $templateProcessor->setValue('user', ucwords($staff->name));
                        $templateProcessor->setValue('start', $start->format("F j Y"));
                        $templateProcessor->setValue('end', $end->format("F j Y"));
                        $templateProcessor->saveAs(storage_path('app/public/' . $template->path));
                        // return response()->download(storage_path('app/public/' . $template->path));
                    } else {
                        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load(public_path('temp/' . $template->name));
                        $worksheet = $spreadsheet->getActiveSheet();
                        $worksheet->getCell('C2')->setValue($name);
                        $worksheet->getCell('C3')->setValue($start->format("F j Y") . ' - ' . $end->format("F j Y"));
                        $worksheet->getCell('C5')->setValue(ucwords($staff->name));
                        $worksheet->getCell('C6')->setValue(ucwords($manager->name));
                        $worksheet->getCell('C7')->setValue(ucwords($partner->name));
                        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xls');
                        $writer->save(storage_path('app/public/' . $template->path));
                        // return response()->download(storage_path('app/public/' . $template->path));
                    }
                } else {
                    return back()->with('warning', 'Please Create Team First');
                }
            }
        } else {
            return back()->with('warning', 'Template Not Found');
        };
    }

    public function download_temp($templates)
    {
        $template = Template::where('name', $templates[0])->first();
        if ($template) {
            $extension =   explode(".", ($template->name));
            //   dd($extension);
            $year = Year::where('company_id', session('company_id'))
                ->where('id', session('year_id'))->first();
            $partner = $year->users()->role('partner')->first();
            $manager = $year->users()->role('manager')->first();
            $staff = $year->users()->role('staff')->first();
            //   dd($partner->name , $manager->name , $staff->name);


            if ($partner != null && $manager != null && $staff != null) {
                $start = $year->begin ? new Carbon($year->begin) : null;
                $end = $year->end ? new Carbon($year->end) : null;
                $names = str_replace(["&"], "&amp;", $year->company->name);
                $name = $year->company->name;
                if (strtolower($extension[1]) == 'docx' || strtolower($extension[1]) == 'docs') {
                    $templateProcessor = new  \PhpOffice\PhpWord\TemplateProcessor(public_path('temp/' . $template->name));
                    $templateProcessor->setValue('client', $names);
                    $templateProcessor->setValue('partner', ucwords($partner->name));
                    $templateProcessor->setValue('manager', ucwords($manager->name));
                    $templateProcessor->setValue('user', ucwords($staff->name));
                    $templateProcessor->setValue('start', $start->format("F j Y"));
                    $templateProcessor->setValue('end', $end->format("F j Y"));
                    $templateProcessor->saveAs(storage_path('app/public/' . $template->path));
                    return $template;
                } else {
                    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load(public_path('temp/' . $template->name));
                    $worksheet = $spreadsheet->getActiveSheet();
                    $worksheet->getCell('C2')->setValue($name);
                    $worksheet->getCell('C3')->setValue($start->format("F j Y") . ' - ' . $end->format("F j Y"));
                    $worksheet->getCell('C5')->setValue(ucwords($staff->name));
                    $worksheet->getCell('C6')->setValue(ucwords($manager->name));
                    $worksheet->getCell('C7')->setValue(ucwords($partner->name));
                    $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xls');
                    $writer->save(storage_path('app/public/' . $template->path));
                    return response()->download(storage_path('app/public/' . $template->path));
                }
            } else {
                return back()->with('warning', 'Please Create Team First');
            }
        } else {
            return back()->with('warning', 'Template Not Found');
        };
    }


    public function include_templates()
    {
        $type = Request::input('type');
        if ($type == 'Execution') {
            Request::validate([
                'folder' => ['required'],
            ]);
        }
        $folder = Request::input('folder');
        $templatesArray = Request::input('selected_arr');
        $templates = Template::whereIn('name', $templatesArray)->get();
        if (count($templates) > 0) {
            // dd(count(Request::input('selected_arr')), $templatesArray, $templates, count($templates));
            foreach ($templates as $template) {
                $extension = explode(".", ($template->name));
                $year = Year::where('company_id', session('company_id'))
                    ->where('id', session('year_id'))->first();
                $partner = $year->users()->role('partner')->first();
                $manager = $year->users()->role('manager')->first();
                $staff = $year->users()->role('staff')->first();

                if ($partner != null && $manager != null && $staff != null) {
                    //------------ creating path to save file
                    $parent = FileManager::where('company_id', session('company_id'))
                        ->where('year_id', session('year_id'))
                        ->where('name', $template->type)
                        ->where('is_folder', '0')
                        ->first();
                    if ($type == 'Execution') {
                        $path = session('company_id') . '/' . session('year_id') . '/' . $parent->id . '/' . $folder . '/' . $template->name;
                    } else {
                        $path = session('company_id') . '/' . session('year_id') . '/' . $parent->id . '/' . $template->name;
                    }
                    //------------ creating path to save file

                    $start = $year->begin ? new Carbon($year->begin) : null;
                    $end = $year->end ? new Carbon($year->end) : null;
                    $names = str_replace(["&"], "&amp;", $year->company->name);
                    $name = $year->company->name;
                    if (strtolower($extension[1]) == 'docx' || strtolower($extension[1]) == 'docs') {
                        $templateProcessor = new  \PhpOffice\PhpWord\TemplateProcessor(public_path('temp/' . $template->name));
                        $templateProcessor->setValue('client', $names);
                        $templateProcessor->setValue('partner', ucwords($partner->name));
                        $templateProcessor->setValue('manager', ucwords($manager->name));
                        $templateProcessor->setValue('user', ucwords($staff->name));
                        $templateProcessor->setValue('start', $start->format("F j Y"));
                        $templateProcessor->setValue('end', $end->format("F j Y"));
                        $templateProcessor->saveAs(storage_path('app/public/' . $path));
                        // return response()->download(storage_path('app/public/' . $template->path));
                    } else {
                        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load(public_path('temp/' . $template->name));
                        $worksheet = $spreadsheet->getActiveSheet();
                        $worksheet->getCell('C2')->setValue($name);
                        $worksheet->getCell('C3')->setValue($start->format("F j Y") . ' - ' . $end->format("F j Y"));
                        $worksheet->getCell('C5')->setValue(ucwords($staff->name));
                        $worksheet->getCell('C6')->setValue(ucwords($manager->name));
                        $worksheet->getCell('C7')->setValue(ucwords($partner->name));
                        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xls');
                        // $writer->save(storage_path('app/public/' . $template->path));
                        $writer->save(storage_path('app/public/' . $path));
                        // return response()->download(storage_path('app/public/' . $template->path));
                    }
                    //----------------------------------------------
                    if ($type == 'Execution') {
                        $file_exists = FileManager::where('name', $template->name)
                            ->where('company_id', session('company_id'))
                            ->where('year_id', session('year_id'))
                            ->where('parent_id', $folder)
                            ->first();
                        if (!$file_exists) {
                            $folderObj = FileManager::create([
                                'name' => $template->name,
                                'is_folder' => 1,
                                'parent_id' => $folder,
                                'path' => $path,
                                'year_id' => session('year_id'),
                                'company_id' => session('company_id'),
                            ]);
                        }
                    } else {
                        $file_exists = FileManager::where('name', $template->name)
                            ->where('company_id', session('company_id'))
                            ->where('year_id', session('year_id'))
                            ->where('parent_id', $parent->id)
                            ->first();
                        if (!$file_exists) {
                            $folderObj = FileManager::create([
                                'name' => $template->name,
                                'is_folder' => 1,
                                'parent_id' => $parent->id,
                                'path' => $path,
                                'staff_approval' => Auth::user()->roles[0]->name == "manager" ? 1 : 0,
                                'year_id' => session('year_id'),
                                'company_id' => session('company_id'),
                            ]);
                        }
                    }
                    //-----------------------------------------------
                } else {
                    return back()->with('warning', 'Please Create Team First');
                }
            }
            if ($type == 'Execution') {
                return Redirect::route("filing", [$folder])->with('success', 'Templates included successfully');
            } else {
                return Redirect::route("filing", [lcfirst($type)])->with('success', 'Templates included successfully');
            }
        } else {
            return back()->with('warning', 'Template Not Selected');
        }
    }

    // for the file approval according to the user role
    public function approve_files()
    {
        $type = Request::input('type');
        $folder = FileManager::where('company_id', session('company_id'))
            ->where('year_id', session('year_id'))
            ->where('is_folder', 0)
            ->where('name', $type)->first();
        $filesArray = Request::input('selected_arr');
        $files = FileManager::where('company_id', session('company_id'))
            ->where('year_id', session('year_id'))
            ->where('parent_id', $folder->id)
            ->where('is_folder', 1)
            ->whereIn('name', $filesArray)->get();

        if (count($files) > 0) {
            if (Auth::user()->roles[0]->name == 'staff') {
                foreach ($files as $file) {
                    $file->staff_approval = 1;
                    $file->manager_review = null;

                    $file->save();
                }
            } else if (Auth::user()->roles[0]->name == 'manager') {
                foreach ($files as $file) {
                    $file->manager_approval = 1;
                    $file->partner_review = null;
                    $file->save();
                }
            } else if (Auth::user()->roles[0]->name == 'partner') {
                foreach ($files as $file) {
                    $file->partner_approval = 1;
                    $file->save();
                }
            } else {
                return back()->with('warning', 'You didn\'t belongs to authentic User');
            }
        } else {
            return back()->with('warning', 'File Not Selected');
        }
        if ($type == 'Planing' || $type == 'Completion') {
            return Redirect::route("filing", [lcfirst($type)])->with('success', 'Files approved successfully');
        } else {
            return Redirect::route("filing", [$folder->id])->with('success', 'Files approved successfully');
        }
    }

    public function reject_files($review)
    {
        $type = Request::input('type');
        $folder = FileManager::where('company_id', session('company_id'))
            ->where('year_id', session('year_id'))
            ->where('is_folder', 0)
            ->where('name', $type)->first();
        $filesArray = Request::input('selected_arr');
        $files = FileManager::where('company_id', session('company_id'))
            ->where('year_id', session('year_id'))
            ->where('parent_id', $folder->id)
            ->where('is_folder', 1)
            ->whereIn('name', $filesArray)->get();


        if (count($files) > 0) {
            // if(Auth::user()->roles[0]->name == 'staff')
            // {
            //     foreach($files as $file)
            //     {
            //         $file->staff_approval = 1;
            //         $file->save();
            //     }
            // }
            // else
            if (Auth::user()->roles[0]->name == 'manager') {
                foreach ($files as $file) {
                    $file->staff_approval = 0;
                    $file->manager_review = $review;
                    $file->save();
                }
            } else if (Auth::user()->roles[0]->name == 'partner') {
                foreach ($files as $file) {
                    $file->manager_approval = 0;
                    $file->partner_review = $review;
                    $file->save();
                }
            } else {
                return back()->with('warning', 'You didn\'t belongs to authentic User');
            }
        } else {
            return back()->with('warning', 'File Not Selected');
        }
        if ($type == 'Planing' || $type == 'Completion') {
            return Redirect::route("filing", [lcfirst($type)])->with('success', 'File rejected successfully');
        } else {
            return Redirect::route("filing", [$folder->id])->with('success', 'File rejected successfully');
        }
    }
}
