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
    public function filing($parent_name_id)
    {
        //condition to deal with url parameter- it will be name if hiting the link from dashboard otherwise it will be id
        if($parent_name_id == 'planing' || $parent_name_id == 'execution' || $parent_name_id == 'completion')
        {
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

        //if get parent then we can show their childrens otherwise we can't track folders or file
        if($parent)
        {
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

            $balances = $query
                ->where('company_id', session('company_id'))
                ->where('year_id', session('year_id'))
                ->where('parent_id', $parent['id'])
                ->paginate(10)
                ->through(
                    function ($obj) {
                        return
                            [
                                'id' => $obj->id,
                                'name' => $obj->name,
                                'is_folder' => $obj->is_folder,
                                'parent_id' => $obj->parent_id,
                                // 'delete' => Entry::where('account_id', $account->id)->first() ? false : true,
                            ];
                    }
                );

            $first = FileManager::where('company_id', session('company_id'))
                ->where('year_id', session('year_id'))
                ->where('parent_id', $parent['id'])
                ->first();

            return Inertia::render('Filing/Index', [
                'balances' => $balances,
                'first' => $first,
                'company' => Company::where('id', session('company_id'))->first(),
                'companies' => Auth::user()->companies,
                'parent' => $parent,
            ]);
        } else {
            return Redirect::route('companies')->with('warning', 'Please create company first to excess these folders.');
        }
    }

    public function folder($folder_id = null)
    {
        if(Company::first())
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

            if($folder_id)
            {
                if($folder_id == 1) {

                return Redirect::route('filing', ['planing'])->with('success', 'File uploaded successfully');

                } elseif($folder_id == 2) {
                return Redirect::route('filing', ['completion'])->with('success', 'File uploaded successfully');

                } else {


                    $selected_folder = FileManager::where('company_id', session('company_id'))
                        ->where('year_id', session('year_id'))
                        ->where('id', $folder_id)
                        ->where('parent_id', $execution['id'])
                        ->get()
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
                $selected_folder = FileManager::all()->where('company_id', session('company_id'))
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
            }
            //if get parent then we can show their childrens otherwise we can't track folders or file
            if($selected_folder)
            {
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

                $balances = $query
                    ->where('company_id', session('company_id'))
                    ->where('year_id', session('year_id'))
                    ->where('parent_id', $selected_folder['id'])
                    ->paginate(10)
                    ->through(
                        function ($obj) {
                            return
                                [
                                    'id' => $obj->id,
                                    'name' => $obj->name,
                                    'is_folder' => $obj->is_folder,
                                    'parent_id' => $obj->parent_id,
                                    // 'delete' => Entry::where('account_id', $account->id)->first() ? false : true,
                                ];
                        }
                    );

                return Inertia::render('Filing/FolderIndex', [
                    'folders' => $folders,
                    'selected_folder' => $selected_folder,
                    'balances' => $balances,
                    'company' => Company::where('id', session('company_id'))->first(),
                    'companies' => Auth::user()->companies,
                ]);
            } else {
                return Redirect::route('companies')->with('warning', 'Please create company first to excess these folders.');
            }
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
        //sending parameter value "execution" because we can only create folder/directories in Executino folder that's why redirecting their
        return Redirect::route("filing", ["execution"])->with('success', 'Folder created.');
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
        Request::validate([
            'avatar'=> ['required'],
        ]);

        $parent = FileManager::find($parent_id);
        $grand_parent = FileManager::where('id', $parent->parent_id)->first();
        if($grand_parent)
        {
            $path = session('company_id') . '/' . session('year_id') . '/' . $grand_parent->id . '/' . $parent_id;
        } else {
            $path = session('company_id') . '/' . session('year_id') . '/' . $parent_id;
        }
        $name = time() . '_' . Request::file('avatar')->getClientOriginalName();

        $pathWithFileName = Request::file('avatar')->storeAs($path, $name, 'public');

        $folderObj = FileManager::create([
            'name' => $name,
            'is_folder' => 1,
            'parent_id' => $parent_id,
            'path' => $pathWithFileName,
            'year_id' => session('year_id'),
            'company_id' => session('company_id'),
        ]);
        //sending parameter value "$parent->id" because we have to show the folder where we upload the file

        // return Redirect::route("filing", [$parent->id])->with('success', 'File upload.');
        //because of new logic
        return Redirect::route("filing.folder", [$parent->id])->with('success', 'File upload.');
    }

    public function downloadFile($file_id)
    {
        $file_obj = FileManager::find($file_id);
        return response()->download(storage_path('app/public/' . $file_obj->path));
    }

    public function deleteFileFolder(FileManager $file_folder_id)
    {
        try {
            if($file_folder_id->is_folder == 0)
            {
                $type = 'Folder';
                $files = FileManager::where('parent_id', $file_folder_id->id)->get();
                if(count($files) > 0)
                {
                    foreach($files as $file)
                    {
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
        } catch(Throwable $e) {
            return back()->with('error', $e);
        }
        return back()->with('error', 'Something went wrong, check network connection and try again');
    }




    // ------------- TO CREEATE DEFAULT FOLDER ON COMPANY and YEAR GENERATION -------
        public function defaultFolders()
    {
        $constFoldersName = [
            'planing', 'completion', 'execution',
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
        foreach($constFoldersName as $name)
        {
            $folderObj = FileManager::create([
                'name' => $name,
                'is_folder' => 0,
                'parent_id' => $parent_id,
                'year_id' => session('year_id'),
                'company_id' => session('company_id'),
                'path' => session('company_id') . '/' . session('year_id'),
            ]);

            // for those objects which are without parent folders ------- planing, completion and execution
            if($parent_id == null)
            {
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
            if($name == 'execution')
            {
                $parent_id = $folderObj->id;
            }
        }
        return true;
    }



    public function index_temp($type)
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
        if(count($year->users))
        {
            request()->validate([
                'direction' => ['in:asc,desc'],
                'field' => ['in:name,email']
            ]);

            //Searching request
            $query = Template::query();
            if (request('search')) {
                $query->where('name', 'LIKE', '%' . request('search') . '%');
            }
            $balances = $query
                ->where('type',lcfirst($type))
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
                // dd($balances);
                $balances_name = Template::where('type',$type)->get()->pluck('name');


                return Inertia::render('Filing/IndexTemplate', [
                    'filters' => request()->all(['search', 'field', 'direction']),
                    'balances' => $balances,
                    'balances_name' => $balances_name,

                    'company' => Company::where('id', session('company_id'))->first(),
                    'companies' => auth()->user()->companies,
                    'type' => ucfirst($type),
                    'folders' => $folders,
                ]);
          }else{
            return back()->with('warning' , 'Please Create Team First');
          }
    }

    public function multi_download_temp(Req $request)
    {
        if($request->selected_arr != null)
        {
            $arr = explode(",",$request->selected_arr);
            if(count($arr) == 1)
            {
                $template = $this->download_temp($arr);
                return response()->download(storage_path('app/public/' . $template->path));
            } else {
                if(File::exists(public_path().'/templates.zip'))
                {
                    File::delete(public_path().'/templates.zip');
                }
                $templates = Template::whereIn('name',$arr)->get();
                $this->file_temp($templates);

            // if($request->has('download')) {
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
            return back()->with('warning', 'Tempalate Not Selected');
        }
    }


        public function file_temp($templates){


            // $template = Template::find($id);
            if($templates){
                $year = Year::where('company_id', session('company_id'))
                ->where('id', session('year_id'))->first();
                $partner = $year->users()->role('partner')->first();
                $manager = $year->users()->role('manager')->first();
                $staff = $year->users()->role('staff')->first();
            foreach ($templates as $key => $template) {
                # code...
            $extension =   explode(".",($template->name));
            //   dd($partner->name , $manager->name , $staff->name);

            if($partner != null && $manager != null && $staff != null){
            $start = $year->begin ? new Carbon($year->begin) : null;
            $end = $year->end ? new Carbon($year->end) : null;
            $names = str_replace(["&"], "&amp;", $year->company->name);
            $name = $year->company->name;
            if(strtolower($extension[1]) == 'docx' || strtolower($extension[1]) == 'docs'){
                $templateProcessor = new  \PhpOffice\PhpWord\TemplateProcessor(public_path('temp/' . $template->name));
                $templateProcessor->setValue('client', $names);
                $templateProcessor->setValue('partner', ucwords($partner->name));
                $templateProcessor->setValue('manager', ucwords($manager->name));
                $templateProcessor->setValue('user', ucwords($staff->name));
                $templateProcessor->setValue('start', $start->format("F j Y"));
                $templateProcessor->setValue('end', $end->format("F j Y"));
                $templateProcessor->saveAs(storage_path('app/public/' . $template->path));
                // return response()->download(storage_path('app/public/' . $template->path));
                }
                else{
                    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load(public_path('temp/' . $template->name));
                    $worksheet = $spreadsheet->getActiveSheet();
                    $worksheet->getCell('C2')->setValue($name);
                    $worksheet->getCell('C3')->setValue($start->format("F j Y"). ' - '.$end->format("F j Y"));
                    $worksheet->getCell('C5')->setValue(ucwords($staff->name));
                    $worksheet->getCell('C6')->setValue(ucwords($manager->name));
                    $worksheet->getCell('C7')->setValue(ucwords($partner->name));
                    $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xls');
                    $writer->save(storage_path('app/public/' . $template->path));
                    // return response()->download(storage_path('app/public/' . $template->path));
                }
            }else{
                    return back()->with('warning', 'Please Create Team First');
            }
            }

            }else{
                return back()->with('warning', 'tempalate Not Fount');
            };
        }




    public function download_temp($templates)
    {
        $template = Template::where('name',$templates[0])->first();
        if($template)
        {
            $extension =   explode(".",($template->name));
            //   dd($extension);
            $year = Year::where('company_id', session('company_id'))
            ->where('id', session('year_id'))->first();
            $partner = $year->users()->role('partner')->first();
            $manager = $year->users()->role('manager')->first();
            $staff = $year->users()->role('staff')->first();
            //   dd($partner->name , $manager->name , $staff->name);


            if($partner != null && $manager != null && $staff != null){
            $start = $year->begin ? new Carbon($year->begin) : null;
            $end = $year->end ? new Carbon($year->end) : null;
            $names = str_replace(["&"], "&amp;", $year->company->name);
            $name = $year->company->name;
            if(strtolower($extension[1]) == 'docx' || strtolower($extension[1]) == 'docs'){
                $templateProcessor = new  \PhpOffice\PhpWord\TemplateProcessor(public_path('temp/' . $template->name));
                $templateProcessor->setValue('client', $names);
                $templateProcessor->setValue('partner', ucwords($partner->name));
                $templateProcessor->setValue('manager', ucwords($manager->name));
                $templateProcessor->setValue('user', ucwords($staff->name));
                $templateProcessor->setValue('start', $start->format("F j Y"));
                $templateProcessor->setValue('end', $end->format("F j Y"));
                $templateProcessor->saveAs(storage_path('app/public/' . $template->path));
                return $template;
                    }
                else{
                    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load(public_path('temp/' . $template->name));
                    $worksheet = $spreadsheet->getActiveSheet();
                    $worksheet->getCell('C2')->setValue($name);
                    $worksheet->getCell('C3')->setValue($start->format("F j Y"). ' - '.$end->format("F j Y"));
                    $worksheet->getCell('C5')->setValue(ucwords($staff->name));
                    $worksheet->getCell('C6')->setValue(ucwords($manager->name));
                    $worksheet->getCell('C7')->setValue(ucwords($partner->name));
                    $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xls');
                    $writer->save(storage_path('app/public/' . $template->path));
                    return response()->download(storage_path('app/public/' . $template->path));
                }
            }else{
                    return back()->with('warning', 'Please Create Team First');
            }
            }else{
                return back()->with('warning', 'tempalate Not Fount');
            };
        }


        public function include_templates()
        {
            $type = Request::input('type');
            if($type == 'Execution')
            {
                Request::validate([
                    'folder' => ['required'],
                ]);
            }
            $folder = Request::input('folder');
            $templatesArray = Request::input('selected_arr');
            $templates = Template::whereIn('name', $templatesArray)->get();

            if(count($templates) > 0)
            {
                // dd(count(Request::input('selected_arr')), $templatesArray, $templates, count($templates));
                for($i = 0; $i < count($templates); $i++)
                {
                    $template = Template::where('name', $templatesArray[$i])->first();
                    if($template)
                    {
                        $extension = explode(".",($template->name));
                        $year = Year::where('company_id', session('company_id'))
                            ->where('id', session('year_id'))->first();
                        $partner = $year->users()->role('partner')->first();
                        $manager = $year->users()->role('manager')->first();
                        $staff = $year->users()->role('staff')->first();

                        if($partner != null && $manager != null && $staff != null)
                        {
                            //------------ creating path to save file
                            $parent = FileManager::where('company_id', session('company_id'))
                                ->where('year_id', session('year_id'))
                                ->where('name', $template->type)
                                ->where('is_folder', '0')
                                ->first();
                            if($type == 'Execution')
                            {
                                $path = session('company_id') . '/' . session('year_id') . '/' . $parent->id . '/' . $folder['id'] . '/' . $template->name;
                            } else {
                                $path = session('company_id') . '/' . session('year_id') . '/' . $parent->id . '/' . $template->name;
                            }
                            //------------ creating path to save file

                            $start = $year->begin ? new Carbon($year->begin) : null;
                            $end = $year->end ? new Carbon($year->end) : null;
                            $names = str_replace(["&"], "&amp;", $year->company->name);
                            $name = $year->company->name;
                            if(strtolower($extension[1]) == 'docx' || strtolower($extension[1]) == 'docs')
                            {
                                $templateProcessor = new  \PhpOffice\PhpWord\TemplateProcessor(public_path('temp/' . $template->name));
                                $templateProcessor->setValue('client', $names);
                                $templateProcessor->setValue('partner', ucwords($partner->name));
                                $templateProcessor->setValue('manager', ucwords($manager->name));
                                $templateProcessor->setValue('user', ucwords($staff->name));
                                $templateProcessor->setValue('start', $start->format("F j Y"));
                                $templateProcessor->setValue('end', $end->format("F j Y"));
                                $templateProcessor->saveAs(storage_path('app/public/' . $path));
                                // return response()->download(storage_path('app/public/' . $template->path));
                            }
                            else{
                                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load(public_path('temp/' . $template->name));
                                $worksheet = $spreadsheet->getActiveSheet();
                                $worksheet->getCell('C2')->setValue($name);
                                $worksheet->getCell('C3')->setValue($start->format("F j Y"). ' - '.$end->format("F j Y"));
                                $worksheet->getCell('C5')->setValue(ucwords($staff->name));
                                $worksheet->getCell('C6')->setValue(ucwords($manager->name));
                                $worksheet->getCell('C7')->setValue(ucwords($partner->name));
                                $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xls');
                                // $writer->save(storage_path('app/public/' . $template->path));
                                $writer->save(storage_path('app/public/' . $path));
                                // return response()->download(storage_path('app/public/' . $template->path));
                            }
                            //----------------------------------------------
                            $file_exists = FileManager::where('name', $template->name)
                                ->where('company_id', session('company_id'))
                                ->where('year_id', session('year_id'))->first();
                            if($type == 'Execution')
                            {
                                $file_exists = FileManager::where('name', $template->name)
                                    ->where('company_id', session('company_id'))
                                    ->where('year_id', session('year_id'))
                                    ->where('parent_id', $folder['id'])
                                    ->first();
                                if(!$file_exists)
                                {
                                    $folderObj = FileManager::create([
                                        'name' => $template->name,
                                        'is_folder' => 1,
                                        'parent_id' => $folder['id'],
                                        'path' => $path,
                                        'year_id' => session('year_id'),
                                        'company_id' => session('company_id'),
                                    ]);
                                }
                            } else {
                                $file_exists = FileManager::where('name', $template->name)
                                    ->where('company_id', session('company_id'))
                                    ->where('year_id', session('year_id'))
                                    ->first();
                                if(!$file_exists)
                                {
                                    $folderObj = FileManager::create([
                                        'name' => $template->name,
                                        'is_folder' => 1,
                                        'parent_id' => $parent->id,
                                        'path' => $path,
                                        'year_id' => session('year_id'),
                                        'company_id' => session('company_id'),
                                    ]);
                                }
                            }
                            //-----------------------------------------------
                        }else{
                            return back()->with('warning', 'Please Create Team First');
                        }
                    }else{
                        return back()->with('warning', 'Tempalate Not Found');
                    };
                }
                if($type == 'Execution')
                {
                    return Redirect::route("filing.folder", [$folder['id']])->with('success', 'Templates included successfully');
                }else {
                    return Redirect::route("filing", [lcfirst($type)])->with('success', 'Templates included successfully');
                    // return back()->with('success', 'Templates included successfully');
                }

            } else {
                return back()->with('warning', 'Tempalate Not Selected');
            }
        }




}
