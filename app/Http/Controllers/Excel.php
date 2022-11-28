<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use App\Models\Salary;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Models\AccountType;
use App\Models\AccountGroup;
use App\Models\Account;
use App\Models\Company;
use App\Models\FileManager;
use App\Models\Trial;
use App\Models\Year;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class Excel extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        if (Company::first()) {
            return Inertia::render('TrialExcel/Index');
        } else {
            return Redirect::route('companies')->with('warning', 'Create Company first');
        }
    }

    // Trial Upload & Read function
    public function __invoke(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx, xls'
        ]);

        DB::beginTransaction();
        // try {
        // DB::transaction(function () use ($request) {

        $colms = [
            0 => 'Account Type',
            1 => 'Account Group',
            // 2 => 'Sub-group',
            // 3 => 'Sub-sub-group',
            // 4 => 'Sub-sub-sub-group',
            // 5 => 'Account',
            // 6 => 'Account Number',
            // 7 => 'opening debt',
            // 8 => 'opening credit',
            // 9 => 'current movement',
            // 10 => 'debt',
            // 11 => 'current',
            // 12 => 'movement',
            // 13 => 'credit',
            // 14 => 'closing',
            // 15 => 'debt',
            // 16 => 'closing',
            // 17 => 'credit',
        ];

        $accArray = [];
        $accInc = 0;
        $fgn_grp_id = null;
        $reader = ReaderEntityFactory::createXLSXReader();
        $reader->open($request->file('file'));

        foreach ($reader->getSheetIterator() as $key => $sheet) {
            // only read data from 1st sheet
            if ($sheet->getIndex() === 0) { // index is 0-based
                // DB::transaction(function () use (
                //     $sheet,
                //     $colms,
                //     $accInc,
                //     $accArray
                // ) {
                // try {


                foreach ($sheet->getRowIterator() as $rowIndex => $row) {

                    if ($rowIndex === 1) {
                        if ($colms[0] !=  $row->getCellAtIndex(0)->getValue() && $colms[1] !=  $row->getCellAtIndex(1)->getValue()) {
                            DB::rollBack();
                            return redirect()->back()->with('error', 'Please Do Not Match Sheet Columns');
                        }
                    }
                    if ($rowIndex === 1)
                        continue; // skip headers row
                    $total_col = count($row->getCells());
                    for ($i = 0; $i <= $total_col - 7; $i++) {
                        $cols[$i] = $row->getCellAtIndex($i)->getValue();
                    }
                    // dd($cols[1] != ""));
                    $check_cols = false;
                    // foreach($cols as $col){
                    if ($cols[0] != "") {
                        if ($cols[1] != "") {
                            if ($cols[2] != "") {
                                if ($cols[$total_col - 7] != "") {
                                    $check_cols = true;
                                } else {
                                    DB::rollBack();
                                    return redirect()->back()->with('error', 'Account Name is Missing at Row Number ' . $rowIndex);
                                }
                            } else {
                                DB::rollBack();
                                return redirect()->back()->with('error', 'Account Group is Missing at Row Number ' . $rowIndex);
                            }
                        } else {
                            DB::rollBack();
                            return redirect()->back()->with('error', 'Account Number is Missing at Row Number ' . $rowIndex);
                        }
                    } else {
                        DB::rollBack();
                        return redirect()->back()->with('error', 'Account Type is Missing at Row Number ' . $rowIndex);
                    }
                    // }
                    if ($check_cols) {

                        //Account Type
                        $acc_type_name = $row->getCellAtIndex(0)->getValue();
                        $acc_type = AccountType::where('name', $acc_type_name)->first();
                        if (!$acc_type) {
                            DB::rollBack();
                            return redirect()->back()->with('error', 'Account Type Not Exist Row Number ' . $rowIndex);
                        }

                        //Account Group
                        $acc_grp_name = $row->getCellAtIndex(2)->getValue();
                        if ($acc_grp_name) {
                            $acc_grp_exist = AccountGroup::where('name', $acc_grp_name)->where('company_id', session('company_id'))->first();
                            if (!$acc_grp_exist) {
                                $acc_grp = AccountGroup::create([
                                    'type_id' => $acc_type->id,
                                    'parent_id' => null,
                                    'name' => $acc_grp_name,
                                    'company_id' => session('company_id'),
                                ]);
                            } else {
                                $acc_grp = $acc_grp_exist;
                            }
                        }

                        // Recurrsion Sub Group
                        $parent = $acc_grp->id;
                        for ($j = 3; $j <= $total_col - 8; $j++) {
                            $acc_sub_grp_name = null;
                            $acc_sub_grp_name = $row->getCellAtIndex($j)->getValue();

                            if ($acc_sub_grp_name) {
                                $acc_sub_grp_exist = AccountGroup::where('name', $acc_sub_grp_name)->where('parent_id', $parent)->where('company_id', session('company_id'))->first();
                                if (!$acc_sub_grp_exist) {
                                    $acc_sub_grp = AccountGroup::create([
                                        'type_id' => $acc_type->id,
                                        'parent_id' => $parent,
                                        'name' => $acc_sub_grp_name,
                                        'company_id' => session('company_id'),
                                    ]);
                                } else {
                                    $acc_sub_grp = $acc_sub_grp_exist;
                                }
                                $parent = $acc_sub_grp->id;
                            }
                        }


                        //Accounts
                        $acc_name = $row->getCellAtIndex($total_col - 7)->getValue();
                        $acc_num = $row->getCellAtIndex(1)->getValue();


                        if ($acc_name && $acc_num) {

                            $accArray[$accInc] = $acc_name;
                            $accInc++;

                            $acc_exist = Account::where('group_id', $parent)->where('company_id', session('company_id'))->where(function ($query) use ($acc_name, $acc_num) {
                                $query->where('name', $acc_name)->orWhere('number', $acc_num);
                            })->first();
                            if (!$acc_exist) {
                                $acc = Account::create([
                                    'name' => $acc_name,
                                    'number' => $acc_num,
                                    'group_id' => $parent,
                                    'company_id' => session('company_id'),
                                ]);
                                $accountGroupforFolder = AccountGroup::find($parent);
                                Storage::makeDirectory('/public/' . session('company_id') .
                                    '/' . session('year_id') . '/execution/' . $accountGroupforFolder->name);
                            } else {
                                $acc = $acc_exist;
                            }

                            //For Trial table ----------------------------------------- START ---------------------------------
                            $opn_debit = $row->getCellAtIndex($total_col - 6)->getValue() ? $row->getCellAtIndex($total_col - 6)->getValue() : 0;
                            $opn_credit = $row->getCellAtIndex($total_col - 5)->getValue() ? $row->getCellAtIndex($total_col - 5)->getValue() : 0;

                            $remain_debit = $row->getCellAtIndex($total_col - 4)->getValue() ? $row->getCellAtIndex($total_col - 4)->getValue() :  0;
                            $remain_credit = $row->getCellAtIndex($total_col - 3)->getValue() ? $row->getCellAtIndex($total_col - 3)->getValue() : 0;

                            $cls_debit = $row->getCellAtIndex($total_col - 2)->getValue() ? $row->getCellAtIndex($total_col - 2)->getValue() : 0;
                            $cls_credit = $row->getCellAtIndex($total_col - 1)->getValue() ? $row->getCellAtIndex($total_col - 1)->getValue() : 0;

                            $trial_exists = Trial::where('company_id', session('company_id'))
                                ->where('account_id', $acc->id)->first();

                            if ($trial_exists) {
                                $trial_exists->opn_debit = $opn_debit;
                                $trial_exists->opn_credit = $opn_credit;

                                $trial_exists->remain_debit = $remain_debit;
                                $trial_exists->remain_credit = $remain_credit;

                                $trial_exists->cls_debit = $cls_debit;
                                $trial_exists->cls_credit = $cls_credit;

                                $trial_exists->account_id = $acc->id;
                                $trial_exists->company_id = session('company_id');
                                $trial_exists->save();
                            } else {
                                Trial::create([
                                    'opn_debit' => $opn_debit,
                                    'opn_credit' => $opn_credit,

                                    'remain_debit' => $remain_debit,
                                    'remain_credit' => $remain_credit,

                                    'cls_debit' => $cls_debit,
                                    'cls_credit' => $cls_credit,

                                    'account_id' => $acc->id,
                                    'company_id' => session('company_id'),
                                ]);
                            }
                            //For Trial table ----------------------------------------- START ---------------------------------
                        }
                    }
                }
                // } catch (\Exception $e) {
                //     return $e;
                //     // something went wrong
                // }

                break;
                // no need to read more sheets
            }

            $reader->close();
        }
        $accResets = Account::whereNotIn('name', $accArray)->get();
        if ($accResets) {
            foreach ($accResets as $accReset) {
                $trial_exists1 = Trial::where('company_id', session('company_id'))
                    ->where('account_id', $accReset->id)->first();
                if ($trial_exists1) {

                    $trial_exists1->opn_debit = 0;
                    $trial_exists1->opn_credit = 0;

                    $trial_exists1->remain_debit = 0;
                    $trial_exists1->remain_credit = 0;

                    $trial_exists1->cls_debit = 0;
                    $trial_exists1->cls_credit = 0;
                    $trial_exists1->save();
                }
            }
        }
        // });
        DB::commit();
        // } catch (\Exception $e) {
        //     DB::rollBack();
        //     return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        //     return redirect()->back()->with('error', $e);
        //     // something went wrong
        // }

        return Redirect::route('accounts');
    }

    // Lead download Function
    public function lead()
    {
        $accounts = Account::where('company_id', session('company_id'))->first();
        if ($accounts) {
            $acc_grps =  AccountGroup::with('accounts', 'accounts.trials')->where('company_id', session('company_id'))
                ->tree()->get()->toTree()->toArray();
            $spreadsheet = new Spreadsheet();
            foreach ($acc_grps as $key => $acc_grp) {
                $this->excel1($acc_grp, $key, $spreadsheet);
            }

            $writer = new Xlsx($spreadsheet);
            $writer->save(storage_path('app/public/' . 'lead.xlsx'));
            return response()->download(storage_path('app/public/' . 'lead.xlsx'));
        } else {
            $comp = Company::find(session('company_id'));
            return redirect()->back()->with('warning', 'Account not found in ' . $comp->name . ' company, Upload trial to generate accounts');
        }
        //-----------------------------------------------------------------
    }

    // Lead Genreate Global function --start

    public $opn = 0;
    public $cls = 0;
    public $dif = 0;
    public $opn_credt = 0;
    public $cls_credt = 0;
    public $opn_debt = 0;
    public $cls_debt = 0;

    function RemoveSpecialChar($str)
    {

        // Using str_replace() function
        // to replace the word
        $res = str_replace(array(
            '\/', '/', '\'', '"',
            ',', ';', '<', '>'
        ), ' ', $str);

        // Returning the result
        return $res;
    }

    public function excel1($acc_grp, $key, $spreadsheet)
    {
        $worksheet1 = $spreadsheet->createSheet($key);
        // $title = $this->RemoveSpecialChar($acc_grp['name']);
        // dd(strlen($title));
        // if (strlen($title)  > 31) {
        //     return  redirect()->route('trial.index')->with('error', 'Maximum 31 characters allowed in sheet title.');
        //     // throw new PHPExcel_Exception('Maximum 31 characters allowed in sheet title.');
        // }
        // echo $this->RemoveSpecialChar($acc_grp['name']);
        // $worksheet1->setTitle($title);

        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setName('Logo');

        $drawing->setPath('images/logo.png'); /* put your path and image here */
        $drawing->setCoordinates('D1');
        $drawing->setOffsetX(70);
        $drawing->setHeight(200);
        $drawing->setWorksheet($spreadsheet->getSheet($key));
        foreach (range('A', 'G') as $k => $col) {
            $spreadsheet->getSheet($key)->getColumnDimension($col)->setAutoSize(true);
        }

        $spreadsheet->getSheet($key)->getStyle('A11:G30')->getAlignment()->setHorizontal('center');
        $spreadsheet->getSheet($key)->getStyle('B11:B30')->getAlignment()->setHorizontal('left');
        $spreadsheet->getSheet($key)->getStyle('A11:G30')->getAlignment()->setVertical('center');


        $spreadsheet->getSheet($key)->fromArray(['CLIENT:'], NULL, 'A3');
        $spreadsheet->getSheet($key)->fromArray(['MZK CORPORATION'], NULL, 'B3');
        $spreadsheet->getSheet($key)->fromArray(['SUBJECT:'], NULL, 'A4');
        $spreadsheet->getSheet($key)->fromArray([$acc_grp['name']], NULL, 'B4');
        $spreadsheet->getSheet($key)->fromArray(['PERIOD:'], NULL, 'A5');
        $spreadsheet->getSheet($key)->fromArray(['23-8-2022'], NULL, 'B5');

        $year = Year::find(session('year_id'));
        $partner = $year->users()->role('partner')->first();
        $manager = $year->users()->role('manager')->first();
        $staff = $year->users()->role('staff')->first();

        $spreadsheet->getSheet($key)->fromArray(['PREPARED BY:'], NULL, 'A6');
        $spreadsheet->getSheet($key)->fromArray([$staff->name], NULL, 'B6');
        $spreadsheet->getSheet($key)->fromArray(['REVIEWED BY:'], NULL, 'A7');
        $spreadsheet->getSheet($key)->fromArray([$manager->name], NULL, 'B7');
        $spreadsheet->getSheet($key)->fromArray(['REVIEWED BY:'], NULL, 'A8');
        $spreadsheet->getSheet($key)->fromArray([$partner->name], NULL, 'B8');
        $spreadsheet->getSheet($key)->fromArray(['LEAD SCHEDULE'], NULL, 'B9');
        $spreadsheet->getSheet($key)->fromArray([''], NULL, 'B10');
        $spreadsheet->getSheet($key)->fromArray(['S.NO'], NULL, 'A11');
        $spreadsheet->getSheet($key)->fromArray(['PARTICULARS'], NULL, 'B11');
        $spreadsheet->getSheet($key)->fromArray(['REFS'], NULL, 'C11');
        $spreadsheet->getSheet($key)->fromArray(['ENDING YEAR'], NULL, 'D11');
        $spreadsheet->getSheet($key)->fromArray(['BEG YEAR'], NULL, 'E11');
        $spreadsheet->getSheet($key)->fromArray(['DIFFERENCE'], NULL, 'F11');
        $spreadsheet->getSheet($key)->fromArray(['%'], NULL, 'G11');
        $spreadsheet->getSheet($key)->getStyle('A11:G11')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_DOUBLE);
        $spreadsheet->getSheet($key)->getStyle('A12:G12')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_DOUBLE);
        $j = 13;
        $open = $clos = $diff = 0;
        foreach ($acc_grp['children'] as $k => $children) {
            $spreadsheet->getSheet($key)->getStyle('A' . $j . ':G' . $j)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_DOUBLE);
            $spreadsheet->getSheet($key)->fromArray([$k + 1], NULL, 'A' . $j);
            $spreadsheet->getSheet($key)->fromArray([$children['name']], NULL, 'B' . $j);
            $spreadsheet->getSheet($key)->fromArray([$children['path']], NULL, 'C' . $j);
            $this->opn = 0;
            $this->cls = 0;
            $this->dif = 0;

            $this->opn_credt = 0;
            $this->cls_credt = 0;
            $this->opn_debt = 0;
            $this->cls_debt = 0;

            $this->acc_sum($children);
            $closing = abs($this->cls_credt - $this->cls_debt);
            $opening = abs($this->opn_credt - $this->opn_debt);
            $spreadsheet->getSheet($key)->fromArray([$closing], NULL, 'D' . $j);
            $spreadsheet->getSheet($key)->fromArray([$opening], NULL, 'E' . $j);
            $spreadsheet->getSheet($key)->fromArray([$closing - $opening], NULL, 'F' . $j);
            $div = $this->opn == 0 ? 1 : $this->opn;
            $res = ($this->cls / $div) * 100;
            $spreadsheet->getSheet($key)->fromArray([round($res, 2) . '%'], NULL, 'G' . $j);
            $open += $opening;
            $clos += $closing;
            $diff += $closing - $opening;

            $j++;
        }
        $spreadsheet->getSheet($key)->getStyle('A' . $j . ':G' . $j)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_DOUBLE);

        // $acc_cls = $acc_opn = 0;
        foreach ($acc_grp['accounts'] as $k => $acc) {
            $spreadsheet->getSheet($key)->getStyle('A' . $j . ':G' . $j)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_DOUBLE);
            $spreadsheet->getSheet($key)->fromArray([$k + 1], NULL, 'A' . $j);
            $spreadsheet->getSheet($key)->fromArray([$acc['name']], NULL, 'B' . $j);
            $spreadsheet->getSheet($key)->fromArray([$acc['number']], NULL, 'C' . $j);
            $acc_closing = abs($acc['trials']['cls_credit'] - $acc['trials']['cls_debit']);
            $acc_opening = abs($acc['trials']['opn_credit'] - $acc['trials']['opn_debit']);
            $spreadsheet->getSheet($key)->fromArray([$acc_closing], NULL, 'D' . $j);
            $spreadsheet->getSheet($key)->fromArray([$acc_opening], NULL, 'E' . $j);
            $spreadsheet->getSheet($key)->fromArray([$acc_closing - $acc_opening], NULL, 'F' . $j);
            $j++;
            $clos += abs($acc['trials']['cls_credit'] - $acc['trials']['cls_debit']);
            $open += abs($acc['trials']['opn_credit'] - $acc['trials']['opn_debit']);
        }
        $spreadsheet->getSheet($key)->getStyle('A' . $j . ':G' . $j)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_DOUBLE);

        $j++;
        $spreadsheet->getSheet($key)->getStyle('A' . $j . ':G' . $j)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_DOUBLE);

        $spreadsheet->getSheet($key)->fromArray(['TOTAL'], NULL, 'B' . $j);
        $spreadsheet->getSheet($key)->fromArray([$clos], NULL, 'D' . $j);
        $spreadsheet->getSheet($key)->fromArray([$open], NULL, 'E' . $j);
        $spreadsheet->getSheet($key)->fromArray([$clos - $open], NULL, 'F' . $j);
        $divi = $open == 0 ? 1 : $open;
        $resu = ($clos / $divi) * 100;
        $spreadsheet->getSheet($key)->fromArray([round($resu, 2) . '%'], NULL, 'G' . $j);

        foreach ($acc_grp['children'] as $k => $children) {
            $this->excel1($children, $k, $spreadsheet);
        }
    }

    // Lead Genreate Global function --End

    // #Account Sum Function

    public function acc_sum($acc_grp)
    {
        if (count($acc_grp['accounts']) >> 0) {
            foreach ($acc_grp['accounts'] as $acc) {
                $this->opn_credt += $acc['trials']['opn_credit'];
                $this->opn_debt += $acc['trials']['opn_debit'];
                $this->cls_credt += $acc['trials']['cls_credit'];
                $this->cls_debt += $acc['trials']['cls_debit'];
            }
        }
        if (count($acc_grp) >> 0) {
            foreach ($acc_grp['children'] as $k => $children) {
                $this->acc_sum($children);
            }
        }
        return;
    }







    public function materiality()
    {
        // $template = Template::where('name', $templates[0])->first();
        // if ($template) {
        // $extension =   explode(".", ($template->name));
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
            // if (strtolower($extension[1]) == 'docx' || strtolower($extension[1]) == 'docs') {
            //     $templateProcessor = new  \PhpOffice\PhpWord\TemplateProcessor(public_path('materiality/materiality.xlsx'));
            //     $templateProcessor->setValue('client', $names);
            //     $templateProcessor->setValue('partner', ucwords($partner->name));
            //     $templateProcessor->setValue('manager', ucwords($manager->name));
            //     $templateProcessor->setValue('user', ucwords($staff->name));
            //     $templateProcessor->setValue('start', $start->format("F j Y"));
            //     $templateProcessor->setValue('end', $end->format("F j Y"));
            //     $templateProcessor->saveAs(storage_path('app/public/' . $template->path));
            //     return $template;
            // } else {

            // $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load(public_path('materiality/materiality.xlsx'));
            // $worksheet = $spreadsheet->getActiveSheet();
            // $worksheet->getCell('C2')->setValue($name);
            // $worksheet->getCell('C3')->setValue($start->format("F j Y") . ' - ' . $end->format("F j Y"));
            // $worksheet->getCell('C5')->setValue(ucwords($staff->name));
            // $worksheet->getCell('C6')->setValue(ucwords($manager->name));
            // $worksheet->getCell('C7')->setValue(ucwords($partner->name));
            // $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xls');
            // $writer->save(storage_path('app/public/' . $template->path));
            // return response()->download(storage_path('app/public/' . $template->path));

        }
        // } else {
        //     return back()->with('warning', 'Please Create Team First');
        // }
        // } else {
        //     return back()->with('warning', 'tempalate Not Fount');
        // };
    }
}
