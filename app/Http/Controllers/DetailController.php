<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request as Req;
use App\Models\Detail;
use App\Models\Account;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Carbon\Carbon;
use App\Models\Company;
use App\Models\FileManager;
use App\Models\Year;
use Inertia\Inertia;
use \PhpOffice\PhpSpreadsheet\Style;
use File;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Trial;


class DetailController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //Validating request
        request()->validate([
            'direction' => ['in:asc,desc'],
            'field' => ['in:name,email']
        ]);
        // dd($query = Detail::first());
        if($query = Detail::first()){
            $query = Detail::groupBy('account_id')->get()
                ->map(function($obj){
                return
                    [
                        'id' => $obj->id,
                        'date' => Carbon::parse($obj->date)->format('F,j Y'),
                        'description' => $obj->description,
                        'cheque' => $obj->cheque,
                        'voucher_no' => $obj->voucher_no,
                        'amount' => $obj->amount,
                        'cash' => $obj->cash,
                        'bank' => $obj->bank,
                        'adjustment' => $obj->adjustment,
                        'a' => $obj->a,
                        'b' => $obj->b,
                        'c' => $obj->c,
                        'd' => $obj->d,
                        'e' => $obj->e,
                        'f' => $obj->f,
                        'remark' => $obj->remark,
                        'company_id' => $obj->company_id,
                        'year_id' => $obj->year_id,
                        'account_id' => $obj->account_id,
                        // 'delete' => Year::where('company_id', $comp->id)->first() != null ? true : false,
                    ];
                });
                // dd($query);
            if (request('search')) {
                $query = $query->where('name', 'LIKE', '%' . request('search') . '%');
            }

            if (request()->has(['field', 'direction'])) {
                $query = $query->orderBy(
                    request('field'),
                    request('direction')
                );
            }
        }else{

            $query = null;
        }

        $file = FileManager::where('company_id',session('company_id'))
            ->where('year_id',session('year_id'))
            ->where('is_folder',0)
            ->where('name', '!=', 'execution')
            ->get(['id','name']);
        // dd($file);

        return Inertia::render('Detail/Index', [
            // 'can' => [
            //     'edit' => auth()->user()->can('edit'),
            //     'create' => auth()->user()->can('create'),
            //     'delete' => auth()->user()->can('delete'),
            //     'read' => auth()->user()->can('read'),
            // ],
            'files' => $file,
            'balances' => $query,
            'filters' => request()->all(['search', 'field', 'direction'])
        ]);
    }

    public function create()
    {
        $account = Account::where('company_id', session('company_id'))->first();
        $accounts = Account::where('company_id', session('company_id'))->get();
        $query = Detail::all()->where('company_id', session('company_id'))
            ->where('year_id', session('year_id'))
            ->where('account_id', $account->id)
            ->map(
                fn ($obj) =>
                [
                    'id' => $obj->id,
                    'date' => $obj->date,
                    'description' => $obj->description,
                    'cheque' => $obj->cheque,
                    'voucher_no' => $obj->voucher_no,
                    'amount' => $obj->amount,
                    'cash' => $obj->cash,
                    'bank' => $obj->bank,
                    'adjustment' => $obj->adjustment,
                    'modeOfPay' => $obj->cash == 0 ?  $obj->bank == '1' ? 'bank' :
                                                    'adjustment' : "cash",
                    'a' => $obj->a == 1 ? true : false,
                    'b' => $obj->b == 1 ? true : false,
                    'c' => $obj->c == 1 ? true : false,
                    'd' => $obj->d == 1 ? true : false,
                    'e' => $obj->e == 1 ? true : false,
                    'f' => $obj->f == 1 ? true : false,
                    'remark' => $obj->remark,
                    'company_id' => $obj->company_id,
                    'year_id' => $obj->year_id,
                    'account_id' => $obj->account_id,
                ],
            );
        $i = 0;
        $details = [];
        foreach ($query as $detail) {
            $details[$i] = $detail;
            $i++;
        }
         return Inertia::render('Detail/Edit', [
            'balances' => $details,
            'accounts' => $accounts,
            'account' => $account,
            'filters' => request()->all(['search', 'field', 'direction'])
        ]);
    }

    public function store()
    {
        Request::validate([
            'date' =>['required'],
            'description' =>['required'],
            'cheque' =>['required'],
            'voucher_no' =>['required'],
            'amount' =>['required'],
            // 'cash' =>['required'],
            // 'bank' =>['required'],
            // 'adjustment' =>['required'],
            'modeOfPay' =>['required', 'not_in:0'],

            // 'a' =>['required'],
            // 'b' =>['required'],
            // 'c' =>['required'],
            // 'd' =>['required'],
            // 'e' =>['required'],
            // 'f' =>['required'],
            'remark' =>['required'],
            // 'account_id' =>['required'],
            // 'conclusion' =>['required'],
        ]);
        $detail = Detail::create([
            'company_id' => session('company_id'),
            'year_id' => session('year_id'),
            'account_id' => Request::input('account_id')['id'],
            'date' => Request::input('date'),
            'description' => Request::input('description'),
            'cheque' => Request::input('cheque'),
            'voucher_no' => Request::input('voucher_no'),
            'amount' => Request::input('amount'),
            // 'cash' => Request::input('cash'),
            // 'bank' => Request::input('bank'),
            // 'adjustment' => Request::input('adjustment'),
            'cash' => Request::input('modeOfPay') == 'cash' ? 1 : 0,
            'bank' => Request::input('modeOfPay') == 'bank' ? 1 : 0,
            'adjustment' => Request::input('modeOfPay') == 'adjustment' ? 1 : 0,
            // 'modeOfPay' => Request::input('modeOfPay'),
            'a' => Request::input('a') ? 1 : 0,
            'b' => Request::input('b') ? 1 : 0,
            'c' => Request::input('c') ? 1 : 0,
            'd' => Request::input('d') ? 1 : 0,
            'e' => Request::input('e') ? 1 : 0,
            'f' => Request::input('f') ? 1 : 0,
            'remark' => Request::input('remark'),
            // 'conclusion' => Request::input('conclusion'),
        ]);
        return Redirect::route('details')->with('success', 'Detail created');
    }

    public function edit($account_id)
    {
        // $details = Detail::where('account_id', $account_id)->get();
        $query = Detail::all()->where('company_id', session('company_id'))
            ->where('year_id', session('year_id'))
            ->where('account_id', $account_id)
        // getQuery()
        // ->paginate(10)
        //     ->withQueryString()
            ->map(
                fn ($obj) =>
                [
                    'id' => $obj->id,
                    'date' => $obj->date,
                    'description' => $obj->description,
                    'cheque' => $obj->cheque,
                    'voucher_no' => $obj->voucher_no,
                    'amount' => $obj->amount,
                    'cash' => $obj->cash,
                    'bank' => $obj->bank,
                    'adjustment' => $obj->adjustment,
                    'modeOfPay' => $obj->cash == 0 ?  $obj->bank == '1' ? 'bank' :
                                                    'adjustment' : "cash",

                                        // $obj->adjustment == 1 ? 'adjustment' :
                    'a' => $obj->a == 1 ? true : false,
                    'b' => $obj->b == 1 ? true : false,
                    'c' => $obj->c == 1 ? true : false,
                    'd' => $obj->d == 1 ? true : false,
                    'e' => $obj->e == 1 ? true : false,
                    'f' => $obj->f == 1 ? true : false,
                    'remark' => $obj->remark,
                    'company_id' => $obj->company_id,
                    'year_id' => $obj->year_id,
                    'account_id' => $obj->account_id,
                    // 'delete' => Year::where('company_id', $comp->id)->first() != null ? true : false,
                ],
            );

        $i = 0;
        $details = [];
        foreach ($query as $detail) {
            $details[$i] = $detail;
            $i++;
        }
         return Inertia::render('Detail/Edit', [
            'balances' => $details,
            'accounts' => Account::where('company_id', session('company_id'))->get(),
            'account' => Account::find($account_id),
            'filters' => request()->all(['search', 'field', 'direction'])
        ]);
    }

    public function update(Req $req, $account_id)
    {
        // dd($account_id, $req->balances);
        Request::validate([
            'balances.*.date' =>['required'],
            'balances.*.description' =>['required'],
            'balances.*.cheque' =>['required'],
            'balances.*.voucher_no' =>['required'],
            'balances.*.amount' =>['required'],
            'balances.*.remark' =>['required'],
            // 'balances.*.account_id' =>['required'],
            'balances.*.modeOfPay' =>['required', 'not_in:0'],
            // 'conclusion' =>['required'],
        ]);

        $preDetails = Detail::where('company_id', session('company_id'))
            ->where('year_id', session('year_id'))
            ->where('account_id', $account_id)->get();


        DB::transaction(function () use ($req, $account_id, $preDetails) {
            try {
                foreach ($preDetails as $preDetail) {
                    $preDetail->delete();
                }

                $data = $req->balances;
                foreach ($data as $detail) {
                    Detail::create([
                        'company_id' => session('company_id'),
                        'year_id' => session('year_id'),

                        'account_id' => $account_id,
                        'date' => $detail['date'],
                        'description' => $detail['description'],
                        'cheque' => $detail['cheque'],
                        'voucher_no' => $detail['voucher_no'],
                        'amount' => $detail['amount'],
                        'cash' => $detail['modeOfPay'] == 'cash' ? 1 : 0,
                        'bank' => $detail['modeOfPay'] == 'bank' ? 1 : 0,
                        'adjustment' => $detail['modeOfPay'] == 'adjustment' ? 1 : 0,
                        'a' => $detail['a'] ? 1 : 0,
                        'b' => $detail['b'] ? 1 : 0,
                        'c' => $detail['c'] ? 1 : 0,
                        'd' => $detail['d'] ? 1 : 0,
                        'e' => $detail['e'] ? 1 : 0,
                        'f' => $detail['f'] ? 1 : 0,
                        'remark' => $detail['remark'],
                        // 'conclusion' => $detail['conclusion'],
                    ]);
                }
            } catch (Exception $e) {
                return $e;
            }
        });
        return Redirect::route('details')->with('success', 'Detail created');
    }

    public function destroy($account_id)
    {
        $details = Detail::where('company_id', session('company_id'))
            ->where('year_id', session('year_id'))
            ->where('account_id', $account_id)->get();

        foreach ($details as $detail) {
            $detail->delete();
        }
        return Redirect::back()->with('success', 'Details deleted.');
    }

    public function download_details($account_id)
    {

        $account = Account::where('id' , $account_id)
                    ->where('company_id' , session('company_id'))->first();
        if($account){
                $details = Detail::where('company_id' , session('company_id'))->where('account_id' , $account_id)->get();
                $detail_first = Detail::where('company_id' , session('company_id'))->first();
                if(count($details) > 0)
                {
                $this->generate_details($details,$account, $detail_first);
                    return response()->download(storage_path('app/public/' . session('company_id')  . '/' . session('year_id') . '/' .  'Test of Details.xlsx'));

                }else
                {
                return Redirect::route('details')->with('warning', 'Please create Test of Details first.');
                }

        }else{
            return Redirect::route('trial')->with('warning', 'Please create Account first.');
        }

    }

    public function import_details(Req $req)
    {

        $account_id = $req->account_id;
        $directory_id = $req->file_id['id'];
        $directoryParent = FileManager::find($directory_id);
        $account = Account::where('id' , $account_id)
            ->where('company_id' , session('company_id'))->first();
        if($account){
            $details = Detail::where('company_id' , session('company_id'))->where('account_id' , $account_id)->get();
            $detail_first = Detail::where('company_id' , session('company_id'))->first();
            if(count($details) > 0)
            {
                $this->generate_details($details,$account, $detail_first);
                // File::copy(public_path().'/temp/'. $name, storage_path('app/public/'.$path.'/'.$name));

                $file_exists = FileManager::
                    // where('company_id', session('company_id'))
                    // ->where('year_id', session('year_id'))
                    // ->where('is_folder', 1)
                    // ->where('parent_id', $directory_id)
                    // ->where('name', 'Test of Details.xlsx')
                    where('path', $directoryParent->path . '/Test of Details.xlsx')
                    ->first();
                // dd($file_exists);

                if(!$file_exists)
                {
                    $folderObj = FileManager::create([
                        'name' => 'Test of Details.xlsx',
                        'is_folder' => 1,
                        'parent_id' => $directory_id,
                        'path' => $directoryParent->path . '/' .  'Test of Details.xlsx',
                        'year_id' => session('year_id'),
                        'company_id' => session('company_id'),
                    ]);
                } else {
                    $folderObj = $file_exists;
                }
                // File::copy(storage_path('app/public/' . session('company_id')  . '/' . session('year_id') .'/'.'Test of Details.xlsx'),
                // storage_path('app/public/' . session('company_id')  . '/' . session('year_id') . '/' . $directory_id . '/' .  'Test of Details.xlsx'));
                File::copy(storage_path('app/public/' . session('company_id')  . '/' . session('year_id') .'/'.'Test of Details.xlsx'), storage_path('app/public/' . $folderObj->path));
                return redirect()->route('details')->with('success', 'File Import Successfully.');
                    // return response()->download(storage_path('app/public/' . session('company_id')  . '/' . session('year_id') . '/' . $directory_id . '/' .  'Test of Details.xlsx'));
            }else
            {
                return Redirect::route('details')->with('warning', 'Please create Test of Details first.');
            }

        }else{
            return Redirect::route('trial')->with('warning', 'Please create Account first.');
        }
    }

    public function generate_details($details,$account , $detail_first)
    {

        $debit = Trial::where('account_id', $account->id)->sum('cls_debit');
        $credit = Trial::where('account_id', $account->id)->sum('cls_credit');
         $res = $debit - $credit;

        //  dd(str_replace("-" ," ", $res));

        $spreadsheet = new Spreadsheet();


            // formating price cell
            $spreadsheet->getDefaultStyle()->getFont()->setSize(9);
            $FORMAT_ACCOUNTING = '_(* #,##0.00_);_(* \(#,##0.00\);_(* "-"??_);_(@_)';
            $spreadsheet->getActiveSheet()->getStyle('F:F')->getNumberFormat()->setFormatCode($FORMAT_ACCOUNTING);
            $spreadsheet->getDefaultStyle()->getAlignment()->setHorizontal('left');

            $Webdings = array(
                'font'  => array(
                    'bold' => true,
                    'name' => 'Webdings'
                ));

                $font = array(
                    'font'  => array(
                        'bold' => true,
                        'name' => 'Arial'
                    ));
            // page setup
            $spreadsheet->getActiveSheet()->getPageSetup()
                ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
            $spreadsheet->getActiveSheet()->getPageSetup()
                ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);

                // text Color
            $spreadsheet->getActiveSheet()->getStyle('A9:P9')
                ->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);

                // backroud cell
            $spreadsheet->getActiveSheet()->getStyle('A9:P9')
                ->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('000000');

            //merge cell
            for($i = 1 ;  $i <= 7  ;$i++ ){
                if($i <= 3){
                    $spreadsheet->getActiveSheet()->mergeCells('A'.$i . ':' .'C'.$i);
                }else{
                    $spreadsheet->getActiveSheet()->mergeCells('A'.$i . ':' .'B'.$i);
                }
            }

            $now = Carbon::now()->format("M d Y");
        $spreadsheet->getActiveSheet()->getStyle('A4:P7')->applyFromArray($font);
        $spreadsheet->getActiveSheet()->mergeCells('A1:C1');
        $spreadsheet->getActiveSheet()->mergeCells('A2:C2');
        $spreadsheet->getActiveSheet()->mergeCells('A3:C3');
        $spreadsheet->getActiveSheet()->fromArray(['Muniff Ziauddin & Co.'], NULL, 'A1');
        $spreadsheet->getActiveSheet()->getStyle('A1')->applyFromArray($font);
        $spreadsheet->getActiveSheet()->fromArray(['Chartered Accountants'], NULL, 'A2');
        $spreadsheet->getActiveSheet()->fromArray(['An Independent Member Firm of BKR International'], NULL, 'A3');
        $spreadsheet->getActiveSheet()->fromArray(['CLIENT:'], NULL, 'A4');
        $spreadsheet->getActiveSheet()->fromArray(['Prepared By:'], NULL, 'H4');
        $spreadsheet->getActiveSheet()->mergeCells('H4:I4');
        $spreadsheet->getActiveSheet()->fromArray([ucfirst(auth()->user()->name)], NULL, 'J4');
        $spreadsheet->getActiveSheet()->fromArray(['Date:'], NULL, 'M4');
        $spreadsheet->getActiveSheet()->fromArray([$now], NULL, 'N4');
        $spreadsheet->getActiveSheet()->fromArray(['PERIOD:'], NULL, 'A5');
        $spreadsheet->getActiveSheet()->fromArray(['Reviewed By:'], NULL, 'H5');
        $spreadsheet->getActiveSheet()->mergeCells('H5:I5');
        $spreadsheet->getActiveSheet()->fromArray(['Date:'], NULL, 'M5');
        $spreadsheet->getActiveSheet()->fromArray([$now], NULL, 'N5');
        $spreadsheet->getActiveSheet()->fromArray(['SUBJECT:'], NULL, 'A6');
        $spreadsheet->getActiveSheet()->fromArray(['Account Number:'], NULL, 'A7');
        $end = $detail_first->company->year->end ? new Carbon($detail_first->company->year->end) : null;
        $spreadsheet->getActiveSheet()->fromArray([$detail_first->company->name], NULL, 'C4');
        $spreadsheet->getActiveSheet()->fromArray([$end ? $end->format("M d Y") : null], NULL, 'C5');
        $spreadsheet->getActiveSheet()->fromArray(['Test Of Details - Vouching: '.$account->name], NULL, 'C6');
        $spreadsheet->getActiveSheet()->fromArray([$account->number], NULL, 'C7');

        $rowArray = ['SR#', 'Date', 'Description', 'Cheque', 'Voucher No#', 'Amount', 'Cash', 'Bank', 'Adj', 'A', 'B', 'C', 'D', 'E','F','Remarks'];
        $spreadsheet->getActiveSheet()->fromArray($rowArray, NULL, 'A9');
        $widthArray = ['5', '15', '40', '15', '15', '15', '10', '10', '10', '10', '10', '10', '10', '10', '10' , '30'];
        foreach (range('A', 'P') as $key => $col) {
            $spreadsheet->getActiveSheet()->getColumnDimension($col)->setWidth($widthArray[$key]);


            // $spreadsheet->getActiveSheet()->getStyle('C1:C8000')->getAlignment()->setWrapText(true);
        }


                $details = $details->map(
                    function ($det) {
                        return [
                            'id' => $det->id,
                            'date' => $det->date,
                            'description' => $det->description,
                            'cheque' => $det->cheque,
                            'voucher_no' => $det->voucher_no,
                            'amount' => $det->amount,
                            'cash' => $det->cash == 1 ? 'a' : 'r',
                            'bank' => $det->bank == 1 ?  'a' : 'r',
                            'adjustment' => $det->adjustment == 1 ?  'a' : 'r',
                            'A' => $det->a == 1 ? 'a' : 'r',
                            'B' => $det->b == 1 ? 'a' : 'r',
                            'C' => $det->c == 1 ? 'a' : 'r',
                            'D' => $det->d == 1 ? 'a' : 'r',
                            'E' => $det->e == 1 ? 'a' : 'r',
                            'F' => $det->f == 1 ? 'a' : 'r',
                            'remarks' => $det->remark,
                        ];
                    }
                )
                ->toArray();

                // dd(($details));
             $last_rec =   count($details) + 11;
               $styleArray = array(
                    'borders' => array(
                        'allBorders' => array(
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ),
                    ),
                );

                  $styleArray1 = array(
                    'borders' => array(
                        'allBorders' => array(
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                        ),
                    ),
                );


         $spreadsheet->getActiveSheet()->fromArray($details, NULL, 'A11');
         $spreadsheet->getActiveSheet()->getStyle('A10:P'.$last_rec )->applyFromArray($styleArray);
         $last_rec += 1;
         $total =   $last_rec;

         $spreadsheet->getActiveSheet()->getStyle('G10' . ':' .'O'. $last_rec)->applyFromArray($Webdings);
         $spreadsheet->getActiveSheet()->fromArray(['Sub-Total'], NULL, 'E'.$last_rec);
        //  $spreadsheet->getActiveSheet()->fromArray(['Sub-Total'], NULL, 'F'.$last_rec);
         $spreadsheet->getActiveSheet()->setCellValue('F'.$last_rec, '=sum(F11 : F'. $last_rec .')');
         $spreadsheet->getActiveSheet()->getStyle('A'.$last_rec . ':' . 'P'.$last_rec )->applyFromArray($styleArray);
         $spreadsheet->getActiveSheet()->getStyle('A'.$last_rec . ':' . 'P'.$last_rec )->applyFromArray($font);
        $last_rec += 1;
        $spreadsheet->getActiveSheet()->fromArray(['LEGENDS'], NULL, 'B'.$last_rec);
        $last_rec += 1;
        $spreadsheet->getActiveSheet()->fromArray(['A:'], NULL, 'B'.$last_rec);
        $spreadsheet->getActiveSheet()->fromArray(['Posting to ledger'], NULL, 'C'.$last_rec);
        $spreadsheet->getActiveSheet()->mergeCells('F'.$last_rec . ':' .'H'.$last_rec);
        $spreadsheet->getActiveSheet()->fromArray(['Amount Voucher:'], NULL, 'F'.$last_rec);
         $spreadsheet->getActiveSheet()->mergeCells('I'.$last_rec . ':' .'J'.$last_rec);
         $spreadsheet->getActiveSheet()->setCellValue('I'.$last_rec, '=(F'. $total .')');
         $spreadsheet->getActiveSheet()->getStyle('F'.$last_rec . ':' . 'J'.$last_rec )->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->fromArray(['Note:'], NULL, 'L'.$last_rec);
        $spreadsheet->getActiveSheet()->fromArray(['a'], NULL, 'M'.$last_rec);
        $spreadsheet->getActiveSheet()->getStyle('M'.$last_rec)->applyFromArray($Webdings);
         $spreadsheet->getActiveSheet()->getStyle('M'.$last_rec )->applyFromArray($styleArray);
         $spreadsheet->getActiveSheet()->mergeCells('N'.$last_rec . ':' .'O'.$last_rec);
        $spreadsheet->getActiveSheet()->fromArray(['Yes'], NULL, 'N'.$last_rec);


        $last_rec += 1;
        $spreadsheet->getActiveSheet()->fromArray(['B:'], NULL, 'B'.$last_rec);
        $spreadsheet->getActiveSheet()->fromArray(['Voucher Approved'], NULL, 'C'.$last_rec);
        $spreadsheet->getActiveSheet()->mergeCells('F'.$last_rec . ':' .'H'.$last_rec);
        $spreadsheet->getActiveSheet()->fromArray(['Ledger Balance:'], NULL, 'F'.$last_rec);
        $spreadsheet->getActiveSheet()->mergeCells('I'.$last_rec . ':' .'J'.$last_rec);
        $spreadsheet->getActiveSheet()->setCellValue('I'.$last_rec, str_replace("-" ," ", $res));
        $spreadsheet->getActiveSheet()->getStyle('F'.$last_rec . ':' . 'J'.$last_rec )->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->fromArray(['r'], NULL, 'M'.$last_rec);
            $spreadsheet->getActiveSheet()->getStyle('M'.$last_rec)->applyFromArray($Webdings);
         $spreadsheet->getActiveSheet()->getStyle('M'.$last_rec )->applyFromArray($styleArray);
         $spreadsheet->getActiveSheet()->mergeCells('N'.$last_rec . ':' .'O'.$last_rec);
        $spreadsheet->getActiveSheet()->fromArray(['No'], NULL, 'N'.$last_rec);


        $pre = $last_rec;
        $last_rec += 1;
        $spreadsheet->getActiveSheet()->fromArray(['C:'], NULL, 'B'.$last_rec);
        $spreadsheet->getActiveSheet()->fromArray(['Supporting Document'], NULL, 'C'.$last_rec);
         $spreadsheet->getActiveSheet()->mergeCells('F'.$last_rec . ':' .'H'.$last_rec);
        $spreadsheet->getActiveSheet()->fromArray(['% Vouched:'], NULL, 'F'.$last_rec);
        $spreadsheet->getActiveSheet()->mergeCells('I'.$last_rec . ':' .'J'.$last_rec);
         $spreadsheet->getActiveSheet()->setCellValue('I'.$last_rec, '=(F'. $total .'/'.'I'.$pre . '* 100 )');

         $spreadsheet->getActiveSheet()->getStyle('F'.$last_rec . ':' . 'J'.$last_rec )->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->fromArray(['N.A'], NULL, 'M'.$last_rec);
         $spreadsheet->getActiveSheet()->getStyle('M'.$last_rec )->applyFromArray($styleArray);
         $spreadsheet->getActiveSheet()->mergeCells('N'.$last_rec . ':' .'O'.$last_rec);
        $spreadsheet->getActiveSheet()->fromArray(['Not Applicable'], NULL, 'N'.$last_rec);

        $last_rec += 1;
        $spreadsheet->getActiveSheet()->fromArray(['D:'], NULL, 'B'.$last_rec);
        $spreadsheet->getActiveSheet()->fromArray(['Bank Statement'], NULL, 'C'.$last_rec);
        $last_rec += 1;
        $spreadsheet->getActiveSheet()->fromArray(['E:'], NULL, 'B'.$last_rec);
        $spreadsheet->getActiveSheet()->fromArray(['Column 1'], NULL, 'C'.$last_rec);
        $last_rec += 1;
        $spreadsheet->getActiveSheet()->fromArray(['F:'], NULL, 'B'.$last_rec);
        $spreadsheet->getActiveSheet()->fromArray(['Column 2'], NULL, 'C'.$last_rec);




        $writer = new Xlsx($spreadsheet);
        return   $writer->save(storage_path('app/public/' . session('company_id')  . '/' . session('year_id') . '/' .  'Test of Details.xlsx'));
    }


}
