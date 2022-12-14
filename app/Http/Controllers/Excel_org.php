<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use App\Models\Salary;
use App\Models\AccountType;
use App\Models\AccountGroup;
use App\Models\Account;
use App\Models\Trial;
use Carbon\Carbon;
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
    public function __invoke(Request $request)
    {
        // return Inertia::render('TrialExcel/Index');
        $request->validate([
            'file'=> 'required|mimes:xlsx, xls'
        ]);


        $reader = ReaderEntityFactory::createXLSXReader();
        // $reader->open('trial.xlsx');
        $reader->open($request->file('file'));

        foreach ($reader->getSheetIterator() as $sheet) {
            // only read data from 1st sheet
            if ($sheet->getIndex() === 0) { // index is 0-based
                foreach ($sheet->getRowIterator() as $rowIndex => $row) {
                    if($rowIndex === 1) continue; // skip headers row
 dd(count($row->getCells()));
                    $col1 = $row->getCellAtIndex(0)->getValue();
                    $col2 = $row->getCellAtIndex(1)->getValue();
                    $col3 = $row->getCellAtIndex(2)->getValue();
                    $col4 = $row->getCellAtIndex(3)->getValue();
                    $col5 = $row->getCellAtIndex(4)->getValue();

                    if($col1 || $col2 || $col3 || $col4 || $col5)
                    {

                        //Account Type
                        $acc_type_name = $row->getCellAtIndex(0)->getValue();
                        // dd($acc_type_name);
                        if($acc_type_name){
                            $acc_type = AccountType::where('name', $acc_type_name)->first();
                        }


                        $fgn_grp_id;
                        //Account Group
                        $acc_grp_name = $row->getCellAtIndex(1)->getValue();
                        if($acc_grp_name)
                        {
                            $acc_grp_exist = AccountGroup::where('name', $acc_grp_name)->
                                where('company_id', session('company_id'))->
                                first();
                            if(!$acc_grp_exist)
                            {
                                $acc_grp = AccountGroup::create([
                                    'type_id' => $acc_type->id,
                                    'parent_id' => null,
                                    'name' => $acc_grp_name,
                                    'company_id' => session('company_id'),
                                ]);
                            } else {
                                $acc_grp = $acc_grp_exist;
                            }
                            $fgn_grp_id = $acc_grp->id;
                        }


                        //Account Sub Group
                        $acc_sub_grp_name = $row->getCellAtIndex(2)->getValue();
                        if($acc_sub_grp_name)
                        {
                            $acc_sub_grp_exist = AccountGroup::where('name', $acc_sub_grp_name)->
                                where('parent_id', $acc_grp->id)->
                                // where('company_id', session('company_id'))->
                                first();
                            if(!$acc_sub_grp_exist)
                            {
                                $acc_sub_grp = AccountGroup::create([
                                    'type_id' => $acc_type->id,
                                    'parent_id' => $acc_grp->id,
                                    'name' => $acc_sub_grp_name,
                                    'company_id' => session('company_id'),
                                ]);
                            } else {
                                $acc_sub_grp = $acc_sub_grp_exist;
                            }
                            $fgn_grp_id = $acc_sub_grp->id;
                        }

                        //Account Sub-sub Group
                        $acc_sub_sub_grp_name = $row->getCellAtIndex(3)->getValue();
                        if($acc_sub_sub_grp_name)
                        {
                            $acc_sub_sub_grp_exist = AccountGroup::where('name', $acc_sub_sub_grp_name)->
                                where('parent_id', $acc_grp->id)->
                                // where('company_id', session('company_id'))->
                                first();
                            if(!$acc_sub_sub_grp_exist)
                            {
                                $acc_sub_sub_grp = AccountGroup::create([
                                    'type_id' => $acc_type->id,
                                    'parent_id' => $acc_sub_grp->id,
                                    'name' => $acc_sub_sub_grp_name,
                                    'company_id' => session('company_id'),
                                ]);
                            } else {
                                $acc_sub_sub_grp = $acc_sub_sub_grp_exist;
                            }
                            $fgn_grp_id = $acc_sub_sub_grp->id;
                        }


                        //Accounts
                        $acc_name = $row->getCellAtIndex(4)->getValue();
                        if($acc_name)
                        {
                            $acc_exist = Account::where('name', $acc_name)->
                                where('group_id', $fgn_grp_id)->
                                where('company_id', session('company_id'))->
                                first();
                            if(!$acc_exist)
                            {
                                $acc = Account::create([
                                    'name' => $acc_name,
                                    'group_id' => $fgn_grp_id,
                                    'company_id' => session('company_id'),
                                ]);
                                $accountGroupforFolder = AccountGroup::find($fgn_grp_id);
                                Storage::makeDirectory('/public/' . session('company_id') .
                                     '/' . session('year_id') . '/execution/' . $accountGroupforFolder->name);
                            } else {
                                $acc = $acc_exist;
                            }

                            //For Trial table ----------------------------------------- START ---------------------------------
                            $opn_debit = $row->getCellAtIndex(5)->getValue() ? $row->getCellAtIndex(5)->getValue() : 0;
                            $opn_credit = $row->getCellAtIndex(6)->getValue() ? $row->getCellAtIndex(6)->getValue() : 0;

                            $remain_debit = $row->getCellAtIndex(7)->getValue() ? $row->getCellAtIndex(7)->getValue() :  0;
                            $remain_credit = $row->getCellAtIndex(8)->getValue() ? $row->getCellAtIndex(8)->getValue() : 0;

                            $cls_debit = $row->getCellAtIndex(9)->getValue() ? $row->getCellAtIndex(9)->getValue() : 0;
                            $cls_credit = $row->getCellAtIndex(10)->getValue() ? $row->getCellAtIndex(10)->getValue() : 0;

                            $trial_exists = Trial::where('company_id', session('company_id'))
                                ->where('account_id', $acc->id)->first();

                            if($trial_exists)
                            {
                                $trial_exists->opn_debit = $opn_debit;
                                $trial_exists->opn_credit = $opn_credit;

                                $trial_exists->remain_debit = $remain_debit;
                                $trial_exists->remain_credit = $remain_credit;

                                $trial_exists->cls_debit = $cls_debit;
                                $trial_exists->cls_credit = $cls_credit;

                                $trial_exists->account_id = $acc->id;
                                $trial_exists->company_id = session('company_id');

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
                        //  else {
                        //     break;
                        // }
                    }






                    // $col1 = $row->getCellAtIndex(0); // id
                    // $col2 = $row->getCellAtIndex(1)->getValue(); // joining date - using getValue() method of Cell object to get actual date value
                    // $col3 = $row->getCellAtIndex(2); // name
                    // $col4 = $row->getCellAtIndex(3); // department
                    // $col5 = $row->getCellAtIndex(4); // designation
                    // $col6 = $row->getCellAtIndex(5); // gross salary
                    // $col7 = $row->getCellAtIndex(6); // medical
                    // $col8 = $row->getCellAtIndex(7); // conveyance
                    // Salary::create([
                    //     // no need to insert $col1 as the id auto-generates
                    //     'joining_date' => $col2,
                    //     'name' => $col3,
                    //     'department' => $col4,
                    //     'designation' => $col5,
                    //     'gross_salary' => $col6,
                    //     'medical' => $col7,
                    //     'conveyance' => $col8,
                    // ]);
                }
                break; // no need to read more sheets
            }
            $reader->close();
        }

        return Redirect::route('accounts');
    }


}
