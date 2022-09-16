<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;
use App\Models\Template;
use App\Models\Company;
use Inertia\Inertia;
use File;

class TemplateController extends Controller
{
    public function index()
    {      //Validating request

            request()->validate([
                'direction' => ['in:asc,desc'],
                'field' => ['in:name,email']
            ]);

            //Searching request
            $query = Template::query();
            if (request('search')) {
                $query->where('name', 'LIKE', '%' . request('search') . '%');
                $query->orwhere('type', 'LIKE', '%' . request('search') . '%');
            }
            $balances = $query
                ->paginate(10)
                ->through(
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

            return Inertia::render('Template/Index', [
                'filters' => request()->all(['search', 'field', 'direction']),
                'balances' => $balances,
                'company' => Company::where('id', session('company_id'))->first(),
                'companies' => auth()->user()->companies,
            ]);

    }

    public function create()
    {
        $types = ['Planing', 'Execution', 'Completion'];
            return Inertia::render('Template/Create', [
                'types' => $types,
            ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'avatar'=> 'required|mimes:xlsx, xls , docx, docs'
        ]);
        // dd($request);
            $path = strtolower($request->type);
            $name = $request->file('avatar')->getClientOriginalName();
            if(file_exists(storage_path('app/public/'.$path.'/'.$name))){
                return back()->with('success', $name.' Already Taken');
            }else{
                $pathWithFileName = $request->file('avatar')->storeAs($path, $name, 'public');
                Template::create([
                    'name' => $name,
                    'path' => $pathWithFileName,
                    'type' => strtolower($request->type),
                    'year_id' => session('year_id'),
                    'company_id' => session('company_id'),
                ]);
                return Redirect::route('templates')->with('success', 'File upload.');
            }
    }


    public function download_temp($id)
    {
        // dd($id);
        $file_obj = Template::find($id);
        return response()->download(storage_path('app/public/' . $file_obj->path));
    }


    public function destroy($id)
    {
        try {

                $temp = Template::find($id)->first();
                // dd($temp->path);
                if(File::exists(storage_path('app/public/'.$temp->path))){
                // dd($temp->path);
                    File::delete(storage_path('app/public/'.$temp->path));
                    $temp->delete();
                    return back()->with('success', $temp->name . ' deleted');
                }else{
                    dd('File does not exists.');
                }


        } catch(Throwable $e) {
            return back()->with('error', $e);
        }
        return back()->with('error', 'Something went wrong, check network connection and try again');
    }




}
