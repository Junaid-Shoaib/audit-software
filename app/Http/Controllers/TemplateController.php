<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;
use App\Models\Template;
use App\Models\Company;
use Inertia\Inertia;
use File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request as Req;

class TemplateController extends Controller
{
    public function index(Req $req)
    {
        if (request()->has('search')) {
            $obj_data = Template::where(function ($query) use ($req) {
                $query->where('name', 'like', '%' . $req->search . '%')
                    ->orWhere('type', 'like', '%' . $req->search . '%');
            })->get();
        } else {
            $obj_data = Template::get();
        }
        $mapped_data = $obj_data->map(function ($temp, $key) {
            return [
                'id' => $temp->id,
                'name' => $temp->name,
                'path' => $temp->path,
                'type' => ucfirst($temp->type),
            ];
        });

        return Inertia::render('Template/Index', [
            'mapped_data' => $mapped_data,
            'filters' => request()->all(['search', 'field', 'direction']),
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
            'avatar' => 'required|mimes:xlsx, xls , docx, docs'
            // 'title' => 'required|unique:table_name,type_field_name,' . $this->type_field_name,
        ]);
        $path = strtolower($request->type);
        $name = $request->file('avatar')->getClientOriginalName();
        if (file_exists(public_path('temp/' . $name))) {
            return back()->with('success', $name . ' Already Taken');
        } else {
            $request->file('avatar')->storeAs('/', $name, 'temp');
            File::copy(public_path() . '/temp/' . $name, storage_path('app/public/' . $path . '/' . $name));
            Template::create([
                'name' => $name,
                'path' => $path . '/' . $name,
                'type' => $path,
                'year_id' => session('year_id'),
                'company_id' => session('company_id'),
            ]);
            return Redirect::route('templates')->with('success', 'File upload.');
        }
    }


    // Template DOwnload Function FOr Admin Side
    public function temp_download($id)
    {
        $file_obj = Template::find($id);
        return response()->download(public_path() . '/temp/' . $file_obj->name);
    }


    public function destroy($id)
    {
        // Delete Template
        try {
            $temp = Template::find($id);
            if (File::exists(public_path() . '/temp/' . $temp->name)) {
                File::delete(public_path() . '/temp/' . $temp->name);
                File::delete(storage_path('app/public/' . $temp->path));
                $temp->delete();
                return back()->with('success', $temp->name . ' deleted');
            } else {
                return Redirect::route('templates')->with('File does not exists.');
            }
        } catch (Throwable $e) {
            return back()->with('error', $e);
        }
        return back()->with('error', 'Something went wrong, check network connection and try again');
    }
}
