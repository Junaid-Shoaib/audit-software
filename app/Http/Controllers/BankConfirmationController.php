<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request as Req;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use App\Models\BankBranch;
use App\Models\Year;
use App\Models\Company;
use App\Models\BankConfirmation;
use Inertia\Inertia;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Exists;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Writer\Word2007;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class BankConfirmationController extends Controller
{
    //Index
    public function index()
    {
        //Company Change
        $active_co = Setting::where('user_id', Auth::user()->id)->where('key', 'active_company')->first();
        $coch_hold = Company::where('id', $active_co->value)->first();

        //Condition Create Button
        $branches = BankBranch::all()
            ->filter(
                function ($branch) {
                    foreach ($branch->bankAccounts as $account) {
                        if ($account->company_id == session('company_id')) {
                            if ($account->bankBranch->bankConfirmations()
                                ->where('year_id', session('year_id'))->first('confirm_create')
                            ) {
                                return false;
                            } else {
                                return true;
                            }
                        }
                    }
                }
            )->first();


        return Inertia::render('Confirmations/Index', [
            'create' => $branches,
            'balances' => BankConfirmation::where('company_id', session('company_id'))->where('year_id', session('year_id'))->paginate(10)->withQueryString()
                ->through(
                    fn ($confirmation) =>
                    [
                        $sent = $confirmation->sent ? new Carbon($confirmation->sent) : null,
                        $reminder = $confirmation->reminder ? new Carbon($confirmation->reminder) : null,
                        $confirm_create = $confirmation->confirm_create ? new Carbon($confirmation->confirm_create) : null,
                        $received = $confirmation->received ? new Carbon($confirmation->received) : null,
                        'id' => $confirmation->id,
                        'sent' => $sent ? $sent->format("M d Y") : null,
                        'reminder' => $reminder ? $reminder->format("M d Y") : null,
                        'confirm_create' => $confirm_create ?  $confirm_create->format("M d Y") : null,
                        'received' => $received ? $received->format("M d Y") : null,
                        'path' => $confirmation->path  ? $confirmation->path : null,
                        'branch' => $confirmation->bankBranch->bank->name . " - " . $confirmation->bankBranch->address,
                        'company' => $confirmation->company->name,
                        'year' => $confirmation->year->begin . " - " . $confirmation->year->end,
                    ]
                ),


            'cochange' => $coch_hold,
            'companies' => Company::all()
                ->map(function ($company) {
                    return [
                        'id' => $company->id,
                        'name' => $company->name,
                    ];
                }),

            'years' => Year::where('company_id', session('company_id'))->get()
                ->map(function ($year) {
                    $end = new Carbon($year->end);
                    $begin = new Carbon($year->begin);
                    return [
                        'id' => $year->id,
                        'begin' => $begin->format("F j Y"),
                        'end' => $end->format("F j Y"),
                    ];
                }),
        ]);
    }
    //Create
    public  function create()
    {

        $branches = BankBranch::all()
            ->filter(
                function ($branch) {
                    foreach ($branch->bankAccounts as $account) {
                        if ($account->company_id == session('company_id')) {
                            if ($account->bankBranch->bankConfirmations()
                                ->where('year_id', session('year_id'))->first('confirm_create')
                            ) {
                                return false;
                            } else {
                                return true;
                            }
                        }
                    }
                }
            )

            ->map(function ($branch) {
                $confirm_create = Carbon::now();

                BankConfirmation::create([
                    'confirm_create' => $confirm_create->format('Y-m-d'),
                    'company_id' => session('company_id'),
                    'year_id' => session('year_id'),
                    'branch_id' => $branch->id,
                ]);
            });

        return back()->withInput();
        // }
    }
    //Show
    public function show($id)
    {
        //
    }
    //Edit
    public function edit()
    {
        // $data = BankConfirmation::where('company_id', session('company_id'))->where('year_id', session('year_id'))
        //         ->get()
        //         ->map(function ($confirmation) {
        //             return [
        //                 'id' => $confirmation->id,
        //                 'name' => $confirmation->bankBranch->bank->name . "-" . $confirmation->bankBranch->address,
        //                 'confirm_create' => $confirmation->confirm_create,
        //                 'sent' => $confirmation->sent,
        //                 'reminder' => $confirmation->reminder,
        //                 'received' => $confirmation->received,
        //                 'path' =>  $confirmation->path,

        //             ];
        //         });
        //         dd($data);
        return Inertia::render('Confirmations/Edit', [
            'data' => BankConfirmation::where('company_id', session('company_id'))->where('year_id', session('year_id'))
                ->get()
                ->map(function ($confirmation) {
                    return [
                        'id' => $confirmation->id,
                        'name' => $confirmation->bankBranch->bank->name . "-" . $confirmation->bankBranch->address,
                        'confirm_create' => $confirmation->confirm_create,
                        'sent' => $confirmation->sent,
                        'reminder' => $confirmation->reminder,
                        'received' => $confirmation->received,
                        'path' =>  $confirmation->path,
                    ];
                }),
            // return response()->download(storage_path('app/public/' . $year->company->id . '/' . $year->id . '/' .  'Remaining_pages.docx'));
            'branches' => BankBranch::all()
                ->filter(function ($branch) {
                    foreach ($branch->bankAccounts as $account) {
                        if ($account->company_id == session('company_id'))
                            return true;
                    }
                })

                ->map(function ($branch) {
                    return [
                        'id' => $branch->id,
                        'name' => $branch->bank->name . " - " . $branch->address,
                    ];
                }),
            'year' => Year::where('id', session('year_id'))->first(),
        ]);
    }
    //Update

    public function updated(Req $request, $id)
    {

        // dd($id);
        $validated = $request->validate([
            'file' => 'required|mimes:pdf|max:2048',
        ]);

        $confirm = BankConfirmation::find($id);
        if ($confirm) {
            $fileName = "B" . $request->id . '.' . $validated['file']->getClientOriginalExtension();
            $path  =  Request::file('file')->storeAs(session('company_id') . '/' . session('year_id'), $fileName, 'public');
            $validated['file']->move(storage_path('app/public/' . session('company_id') . '/' . session('year_id') . '/'), $fileName);
            $confirm->path = $path;
            $confirm->received = Carbon::now();
            $confirm->save();
            return back()->with('success', 'File Uploaded');
        } else {
            return back()->with('success', 'Only Pdf File Upload');
        }
    }


    public function update(Req $request, BankConfirmation $balance)
    {
        Request::validate([
            'balances.*.confirm_create' => ['required'],
        ]);
        foreach ($request->balances as $balance) {
            $bal = BankConfirmation::find($balance['id']);
            $bal->update([
                'sent' => $balance['sent'],
                'reminder' =>  $balance['reminder'],
                'received' => $balance['received'],
                'path' => $balance['path'],


            ]);
        }
        return Redirect::route('confirmations')->with('success', 'Bank Confirmation updated.');
    }

    //Delete
    public function destroy(BankConfirmation $confirmation)
    {
        $confirmation->delete();
        return Redirect::back()->with('success', 'Bank Confirmation deleted.');
    }

    //Template Sheet

    public function  bankconfirmUpload($id)
    {
        $confirm = \App\Models\BankConfirmation::find($id);
        if ($confirm) {
            return response()->download(storage_path('app/public/' . $confirm->path));
            // Storage::disk('public')->exists($confirm->path);
        } else {
            return Redirect::route('confirmations')->with('success', 'File Not Found.');
        }
    }
    public function bankConfig()
    {
        //template
        $account = \App\Models\BankAccount::where('company_id', session('company_id'))->first();
        if ($account) {
            $year = Year::where('company_id', session('company_id'))
                ->where('id', session('year_id'))->first();
            $end = $year->end ? new Carbon($year->end) : null;

            $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('templatebr.docx');
            $names = str_replace(["&"], "&amp;", $year->company->name);
            $templateProcessor->setValue('client', $names);
            $templateProcessor->setValue('end', $end->format("F j Y"));
            $templateProcessor->saveAs(storage_path('app/public/' . $year->company->id . '/' . $year->id . '/' .  'Remaining_pages.docx'));
            return response()->download(storage_path('app/public/' . $year->company->id . '/' . $year->id . '/' .  'Remaining_pages.docx'));
        } else {
            return Redirect::route('balances.create')->with('success', 'Create Balance first.');
        }
    }

    //Branches Pdf
    public function branchespdf(Request $request)
    {
        $company = BankConfirmation::where('company_id', session('company_id'))->first();
        if ($company) {

            $year = Year::where('company_id', session('company_id'))
                ->where('id', session('year_id'))->first();
            $end = $year->end ? new Carbon($year->end) : null;

            $names = str_replace(["&"], "&amp;", $year->company->name);
            $endDate = $end->format("F j Y");
            //   $a = "hello world";

            $confirmation = BankConfirmation::where('company_id', session('company_id'))->get()
                ->map(function ($confirm) {
                    return [
                        'id' => $confirm->id,
                        'branch' => $confirm->bankBranch->bank->name . " - " . $confirm->bankBranch->address,
                    ];
                });
            // dd($confirmation);


            $pdf = app('dompdf.wrapper');
            $pdf->getDomPDF()->set_option("enable_php", true);
            $pdf->loadView('branchespdf', compact('names', 'endDate', 'confirmation'));
            return $pdf->stream($names . " " . 'branches.pdf');
        } else {
            return Redirect::route('bank_accounts.create')->with('success', 'Create Account first.');
        }
    }


    // excel file Generator
    public function ex()
    {
        $spreadsheet = new Spreadsheet();

        $colArray = ['H:H', 'I:I', 'J:J', 'K:K'];
        foreach ($colArray as $key => $col) {

            $FORMAT_ACCOUNTING = '_(* #,##0.00_);_(* \(#,##0.00\);_(* "-"??_);_(@_)';
            $spreadsheet->getActiveSheet()->getStyle($col)->getNumberFormat()->setFormatCode($FORMAT_ACCOUNTING);
        }


        $spreadsheet->getActiveSheet()->getstyle('D3')
            ->getBorders()->getbottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $spreadsheet->getActiveSheet()->getstyle('D4')
            ->getBorders()->getbottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $spreadsheet->getActiveSheet()->getstyle('D5:E5')
            ->getBorders()->getbottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        $spreadsheet->getActiveSheet()->getstyle('L3')
            ->getBorders()->getbottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $spreadsheet->getActiveSheet()->getstyle('L4')
            ->getBorders()->getbottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        $spreadsheet->getActiveSheet()->getstyle('O3')
            ->getBorders()->getbottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $spreadsheet->getActiveSheet()->getstyle('O4')
            ->getBorders()->getbottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        $spreadsheet->getActiveSheet()->setMergeCells(['D5:E5']);
        $spreadsheet->getActiveSheet()->getStyle('C3:C5')->applyFromArray(
            array(
                'font'  => array(
                    'bold'  =>  true,
                ),
                'alignment' => array(
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ),
            )
        );

        $spreadsheet->getActiveSheet()->getStyle('D3:D5')->applyFromArray(
            array(
                'alignment' => array(
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ),
            )
        );
        $company = \App\Models\BankBalance::where('company_id', session('company_id'))
            ->where('year_id', session('year_id'))->first();
        if ($company) {
            $end = $company->year->end ? new Carbon($company->year->end) : null;
        } else {
            return Redirect::route('balances.create')->with('success', 'Create Account first.');
        }
        $spreadsheet->getActiveSheet()->fromArray(['CLIENT:'], NULL, 'C3');
        $spreadsheet->getActiveSheet()->fromArray(['PERIOD:'], NULL, 'C4');
        $spreadsheet->getActiveSheet()->fromArray(['SUBJECT:'], NULL, 'C5');
        $spreadsheet->getActiveSheet()->fromArray([$company->company->name], NULL, 'D3');
        $spreadsheet->getActiveSheet()->fromArray([$end ? $end->format("M d Y") : null], NULL, 'D4');
        $spreadsheet->getActiveSheet()->fromArray(['Bank Confirmation Control Sheet'], NULL, 'D5');

        $spreadsheet->getActiveSheet()->fromArray(['Prepared By:'], NULL, 'K3');
        $spreadsheet->getActiveSheet()->fromArray(['Reviewed By:'], NULL, 'K4');
        $spreadsheet->getActiveSheet()->fromArray(['Date:'], NULL, 'N3');
        $spreadsheet->getActiveSheet()->fromArray(['Date:'], NULL, 'N4');



        $spreadsheet->getActiveSheet()->getStyle('B7:O7')->applyFromArray(
            array(
                'fill' => array(
                    'fillType' => Fill::FILL_SOLID,
                    'color' => array('rgb' => '484848')
                ),
                'font'  => array(
                    'bold'  =>  true,
                    'color' => array('rgb' => 'FFFFFF')
                ),
                'alignment' => array(
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ),
            )
        );

        $rowArray = ['SR#', 'BANK', 'ACCOUNT#', 'ACCOUNT TYPE', 'CURRENCY', 'ADDRESS', 'AS PER LEDGER', 'AS PER BANK STATEMENT', 'AS PER CONFIRMATION', 'DIFFERENCE', 'CREATED', 'SENT', 'REMINDER', 'RECEIVED'];
        $spreadsheet->getActiveSheet()->fromArray($rowArray, NULL, 'B7');
        $widthArray = ['10', '5', '20', '20', '20', '15', '25', '17', '17', '17', '20', '20', '20', '20', '20'];
        foreach (range('A', 'O') as $key => $col) {
            $spreadsheet->getActiveSheet()->getColumnDimension($col)->setWidth($widthArray[$key]);
        }

        $dataa = \App\Models\BankConfirmation::where('company_id', session('company_id'))->where('year_id', session('year_id'))->first();
        if ($dataa) {
            $data = \App\Models\BankBalance::where('company_id', session('company_id'))->where('year_id', session('year_id'))->get()
                ->map(
                    function ($bal) {
                        return [
                            'id' => $bal->id,
                            'bank' => $bal->bankAccount->bankBranch->bank->name,
                            'number' => $bal->bankAccount->name,
                            'type' => $bal->bankAccount->type,
                            'currency' => $bal->bankAccount->currency,
                            'branch' => $bal->bankAccount->bankBranch->address,
                            'ledger' => $bal->ledger,
                            'statement' => $bal->statement,
                            'confirmation' => $bal->confirmation,
                            'difference' => $bal->statement - $bal->confirmation ? $bal->statement - $bal->confirmation : '0',
                            'confirm_create' => $bal->bankAccount->bankBranch->bankConfirmations()->where('company_id', session('company_id'))->where('year_id', session('year_id'))->get()->first()->confirm_create,
                            'sent' => $bal->bankAccount->bankBranch->bankConfirmations
                                ->filter(function ($confirmation) {
                                    return ($confirmation->company_id == session('company_id') && $confirmation->year_id == session('year_id'));
                                })->first()->sent,
                            'reminder' => $bal->bankAccount->bankBranch->bankConfirmations()->where('company_id', session('company_id'))->where('year_id', session('year_id'))->get()->first()->reminder,
                            'received' => $bal->bankAccount->bankBranch->bankConfirmations()->where('company_id', session('company_id'))->where('year_id', session('year_id'))->get()->first()->received,
                        ];
                    }
                )
                ->toArray();
        } else {
            return Redirect::route('confirmations')->with('success', 'Please Create Confirmation.');
        }

        $cnt = count($data);
        for ($i = 0; $i < $cnt; $i++) {
            // dd($data[$i]);
            $data[$i]['confirm_create'] = $data[$i]['confirm_create'] ? new Carbon($data[$i]['confirm_create']) : null;
            $data[$i]['confirm_create'] = $data[$i]['confirm_create'] ? $data[$i]['confirm_create']->format('F j, Y') : null;
            $data[$i]['sent'] = $data[$i]['sent'] ? new Carbon($data[$i]['sent']) : null;
            $data[$i]['sent'] = $data[$i]['sent'] ? $data[$i]['sent']->format('F j, Y') : null;
            $data[$i]['reminder'] = $data[$i]['reminder'] ? new Carbon($data[$i]['reminder']) : null;
            $data[$i]['reminder'] = $data[$i]['reminder'] ? $data[$i]['reminder']->format('F j, Y') : null;
            $data[$i]['received'] = $data[$i]['received'] ? new Carbon($data[$i]['received']) : null;
            $data[$i]['received'] = $data[$i]['received'] ? $data[$i]['received']->format('F j, Y') : null;
        }


        // dd($cnt);
        $spreadsheet->getActiveSheet()->fromArray($data, NULL, 'B9');




        $total = 0;
        for ($i = 0; $i < $cnt; $i++) {
            $total = $total + $data[$i]['ledger'];
        }

        // dd($total);
        $tstr = $cnt + 9;
        $tcell = "H" . strval($tstr);
        $spreadsheet->getActiveSheet()->setCellValue($tcell, $total);

        $styleArray = [
            'borders' => [
                'outline' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                    'color' => [
                        'rgb' => '484848',
                    ],


                ],
            ],
        ];
        $spreadsheet->getActiveSheet()->getStyle($tcell)->applyFromArray($styleArray);

        $writer = new Xlsx($spreadsheet);
        $writer->save(storage_path('app/public/' . $company->company->id  . '/' . $company->year->id . '/' .  'Control Sheet.xlsx'));
        return response()->download(storage_path('app/public/' . $company->company->id . '/' . $company->year->id . '/' .  'Control Sheet.xlsx'));
    }

    //word file Generator
    public function word()

    {
        $phpWord = new PhpWord();
        $i = 0;
        $phpWord->addParagraphStyle('p1Style', array('align' => 'both', 'spaceAfter' => 0, 'spaceBefore' => 0));
        $phpWord->addParagraphStyle('p2Style', array('align' => 'both'));
        $phpWord->addParagraphStyle('p3Style', array('align' => 'right', 'spaceAfter' => 0, 'spaceBefore' => 0));
        $phpWord->addFontStyle('f1Style', array('name' => 'Calibri', 'size' => 12));
        $phpWord->addFontStyle('f2Style', array('name' => 'Calibri', 'bold' => true, 'size' => 12));
        $company = \App\Models\Company::where('id', session('company_id'))->first();

        if ($company->bankAccounts()->first()) {
            $branch = $company->bankAccounts()->get();

            // foreach ($branch as $b) {
            //     $branches[$i] = $b->bankBranch;
            //     $i++;
            // }


            $branches = null;
            foreach ($branch as $b) {

                $check = true;
                if ($branches) {
                    foreach ($branches as $bran) {
                        if ($bran->id == $b->bankBranch->id) {
                            $check = false;
                            break;
                        }
                    }
                }
                if ($check) {
                    $branches[$i] = $b->bankBranch;
                    $i++;
                }
            }
        } else {
            return Redirect::route('bank_accounts.create')->with('success', 'Create Account First');
        }

        $period = Year::where('id', session('year_id'))->first();
        $begin = new Carbon($period->begin);
        $end = new Carbon($period->end);
        $year = $end->year;
        $str = "first Monday of July {$year}";
        $date = new Carbon($str);
        $name = str_replace(["(", ")"], "", $company->name);
        $words = preg_split("/[\s,_-]+/", $name);
        $acronym = "";
        $i = 0;
        $count = 1;

        foreach ($words as $w) {
            $acronym .= $w[0];
        }


        foreach ($branches  as $branch) {
            // dd($branch);
            $section = $phpWord->addSection();
            $textrun = $section->addTextRun();
            $section->addTextBreak(2);
            $acronyms = str_replace(["&"], "&amp;", $acronym);
            $ref = "MZ-BCONF/" . $acronyms . "/" . $year . "/" . $count++;
            $section->addText($ref, 'f2Style', 'p1Style');
            $textrun = $section->addTextRun();
            $section->addTextBreak(1);
            $section->addText($date->format('F j, Y'), 'f2Style', 'p1Style');
            $textrun = $section->addTextRun();
            $section->addTextBreak(0);


            $section->addText('The Manager,', 'f1Style', 'p1Style');
            $bankname = str_replace(["&"], "&amp;", $branch->bank->name);
            $section->addText($bankname . ",", 'f1Style', 'p1Style');
            $branchname = str_replace(["&"], "&amp;", $branch->address);
            $branch = explode("\n", $branchname);

            foreach ($branch as $branchadd) {
                $section->addText($branchadd . ",", 'f1Style', 'p1Style');
                $branchadd++;
            }

            $textrun = $section->addTextRun();
            $section->addTextBreak(0);
            $section->addText('Dear Sir,', 'f1Style', 'p2Style');

            $textrun = $section->addTextRun();

            $textrun = $section->addTextRun('p2Style');
            $textrun->addText('Subject: ', 'f1Style');
            $textrun->addText('Bank Report for Audit Purpose of ', 'f2Style');
            $companyname = str_replace(["&"], "&amp;", $company->name);
            $textrun->addText($companyname, 'f2Style');

            $textrun = $section->addTextRun();
            $section->addTextBreak(0);
            $section->addTextBreak(0);
            // dd($section);
            $textrun = $section->addTextRun('p2Style');
            $textrun->addText(
                "In accordance with your above named customer’s instructions given hereon, please send DIRECT to us at the below address, as auditors of your customer, the following information relating to their affairs at your branch as at the close of business on ",
                'f1Style',
            );
            $textrun->addText($end->format('F j, Y'), 'f2Style');
            $textrun->addText(
                " and, in the case of items 2, 4 and 9, during the period since ",
                'f1Style',
            );
            $textrun->addText($begin->format('F j, Y'), 'f2Style');

            $textrun = $section->addTextRun();
            $section->addTextBreak(0);

            $textrun = $section->addTextRun();
            $textrun->addText(
                "Please state against each item any factors which may limit the completeness of your reply; if there is nothing to report, state ‘NONE’.",
                'f1Style',
                'p2Style'
            );

            $textrun = $section->addTextRun();
            $section->addTextBreak(0);

            $textrun = $section->addTextRun();
            $textrun->addText(
                "It is understood that any replies given are in strict confidence, for the purposes of audit.",
                'f1Style',
                'p2Style'
            );

            $textrun = $section->addTextRun();
            $section->addTextBreak(0);

            $textrun = $section->addTextRun();
            $textrun->addText(
                "Yours truly,",
                'f1Style',
                'p2Style'
            );

            $section->addText(
                "Disclosure  Authorized",
                'f2Style',
                'p3Style'
            );

            $section->addText(
                "For  and  on  behalf  of",
                'f2Style',
                'p3Style'
            );

            $textrun = $section->addTextRun();
            $section->addTextBreak(1);

            $textrun = $section->addTextRun();
            $textrun->addText(
                "Chartered Accountants                                                                                  ___________________",
                'f2Style',
                'p2Style'
            );

            $textrun = $section->addTextRun();
            $section->addTextBreak(0);

            $textrun = $section->addTextRun();
            $textrun->addText(
                "Enclosures:",
                'f1Style',
                'p2Style'
            );
        }
        $writer = new Word2007($phpWord);
        $writer->save(storage_path('app/public/' . $company->id . '/' . $period->id . '/' .  'Bank Letters.docx'));

        //Template FIle Generated.
        $end = $period->end ? new Carbon($period->end) : null;
        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('templatebr.docx');
        $templateProcessor->setValue('client', $company->name);
        $templateProcessor->setValue('end', $end->format("F j Y"));
        $templateProcessor->saveAs(storage_path('app/public/' . $company->id . '/' . $period->id . '/' .  'Remaining_pages.docx'));
        return response()->download(storage_path('app/public/' . $company->id . '/' . $period->id . '/' .  'Bank Letters.docx'));
    }
}
