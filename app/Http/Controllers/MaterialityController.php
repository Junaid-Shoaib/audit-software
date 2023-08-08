<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Company;
use App\Models\Risk;
use App\Models\Setting;
use App\Models\Year;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class MaterialityController extends Controller
{
    public function index()
    {
        return Inertia::render('Materiality/Index', []);
    }



    public function materiality(Request $request)
    {
        $request->validate([
            'preTax' => 'required|numeric|between:0.1,99.99',
            'tAsset' => 'required|numeric|between:0.1,99.99',
            'equity' => 'required|numeric|between:0.1,99.99',
            'netRevenue' => 'required|numeric|between:0.1,99.99',
            'adpt' => 'required|numeric|between:0.1,5',
            'preTaxSel' => 'required_without_all:preTaxSel,tAssetSel,equitySel,netRevenueSel',
            'tAssetSel' => 'required_without_all:preTaxSel,tAssetSel,equitySel,netRevenueSel',
            'equitySel' => 'required_without_all:preTaxSel,tAssetSel,equitySel,netRevenueSel',
            'netRevenueSel' => 'required_without_all:preTaxSel,tAssetSel,equitySel,netRevenueSel',
            "answer1" => 'required',
            "answer2" => 'required',
            "answer3" => 'required',
            "answer4" => 'required',
            "answer5" => 'required',
            "answer6" => 'required',
            "answer7" => 'required',
        ]);


        $accounts = Account::where('company_id', session('company_id'))->first();
        if ($accounts) {
            $cntSel = 0;
            $preTax = $request->preTax;
            $tAsset = $request->tAsset;
            $equity = $request->equity;
            $netRevenue = $request->netRevenue;
            // dd($request);
            $accounts = Account::where('company_id', session('company_id'))->with('accountGroup', 'accountGroup.accountType', 'trials')->get();
            $capital = $assets = $revenue = $expense = 0;
            foreach ($accounts as $account) {
                if ($account->trials->year_id == session('year_id')) {
                    if ($account->accountGroup->accountType->name == 'Assets') {
                        $temp = abs($account->trials->cls_debit - $account->trials->cls_credit);
                        $assets += $temp;
                    } else if ($account->accountGroup->accountType->name == 'Capital') {
                        $temp = abs($account->trials->cls_debit - $account->trials->cls_credit);
                        $capital += $temp;
                    } else if ($account->accountGroup->accountType->name == 'Revenue') {
                        $temp = abs($account->trials->cls_debit - $account->trials->cls_credit);
                        $revenue += $temp;
                    } else if ($account->accountGroup->accountType->name == 'Expenses') {
                        $temp = abs($account->trials->cls_debit - $account->trials->cls_credit);
                        $expense += $temp;
                    }
                }
            }
            $preTaxIncome = abs($revenue - $expense);

            // dd('Assets '.$assets, 'Capital '.$capital, 'Revenue '.$revenue, 'Expenses '.$expense);
            $year = Year::where('company_id', session('company_id'))
                ->where('id', session('year_id'))->first();
            if ($year) {
                $partner = $year->users()->role('partner')->first();
                $manager = $year->users()->role('manager')->first();
                $staff = $year->users()->role('staff')->first();
                //   dd($partner->name , $manager->name , $staff->name);


                if ($partner != null && $manager != null && $staff != null) {
                    // $start = $year->begin ? new Carbon($year->begin) : null;
                    $now = Carbon::now();
                    $end = $year->end ? new Carbon($year->end) : null;
                    $names = str_replace(["&"], "&amp;", $year->company->name);


                    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load(public_path('materiality/materiality.xlsx'));
                    $worksheet = $spreadsheet->getActiveSheet();
                    $worksheet->getCell('D7')->setValue($names);
                    $worksheet->getCell('D9')->setValue("FOR THE YEAR ENDED " . $end->format("M j Y"));
                    $worksheet->getCell('A77')->setValue(ucwords($staff->name));
                    $worksheet->getCell('A81')->setValue(ucwords($manager->name));
                    $worksheet->getCell('A85')->setValue(ucwords($partner->name));
                    $worksheet->getCell('I77')->setValue($now->format("M j Y"));
                    // $worksheet->getCell('G7')->setValue('Date: ' . $now->format("M j Y"));
                    // $worksheet->getCell('C7')->setValue(ucwords($partner->name));

                    $I18 = 0;
                    $I19 = 0;
                    $I20 = 0;
                    $I21 = 0;

                    // Single Rule
                    if (isset($request->preTaxSel)) {
                        $preTaxName = $preTax . '% of Pre Tax Income';
                        $worksheet->getCell('A18')->setValue($preTaxName);
                        $worksheet->getCell('I18')->setValue($preTaxIncome);
                        $I18 = $preTaxIncome * $preTax / 100;
                        $cntSel++;
                    }
                    if (isset($request->tAssetSel)) {
                        $TAssetName = $tAsset . '% of Total Assets';
                        $worksheet->getCell('A19')->setValue($TAssetName);
                        $worksheet->getCell('I19')->setValue($assets);
                        $cntSel++;
                        $I19 = $assets * $tAsset / 100;
                    }

                    if (isset($request->equitySel)) {
                        $equityName =  $equity . '% of Equity';
                        $worksheet->getCell('A20')->setValue($equityName);
                        $worksheet->getCell('I20')->setValue($equity);
                        $I20 = $capital * $equity / 100;
                        $cntSel++;
                    }
                    if (isset($request->netRevenueSel)) {
                        $netRevenueName = $netRevenue . '% of Total Net Revenues';
                        $worksheet->getCell('A21')->setValue($netRevenueName);
                        $worksheet->getCell('I21')->setValue($revenue);
                        $I21 = $revenue * $netRevenue / 100;
                        $cntSel++;
                    }
                    $materiality_val = ($I18 + $I19 + $I20 + $I21) / $cntSel;
                    $worksheet->getCell('I24')->setValue($materiality_val);
                    $worksheet->getCell('I65')->setValue($request->adpt . '%');
                    $worksheet->getCell('A16')->setValue($request->answer1);

                    if ($request->answer2 == 'yes') {
                        $worksheet->getCell('A28')->setValue('X');
                    } else if ($request->answer2 == 'no') {
                        $worksheet->getCell('A30')->setValue('X');
                    } else {
                        $worksheet->getCell('A32')->setValue('X');
                    }

                    $worksheet->getCell('A36')->setValue($request->answer3);

                    if ($request->answer4 == 'yes') {
                        $worksheet->getCell('A40')->setValue('X');
                    } else if ($request->answer4 == 'no') {
                        $worksheet->getCell('A42')->setValue('X');
                    } else {
                        $worksheet->getCell('A44')->setValue('X');
                    }


                    $worksheet->getCell('A48')->setValue($request->answer5);

                    if ($request->answer6 == 'yes') {
                        $worksheet->getCell('A54')->setValue('X');
                    } else if ($request->answer6 == 'no') {
                        $worksheet->getCell('A56')->setValue('X');
                    } else {
                        $worksheet->getCell('A58')->setValue('X');
                    }

                    if ($request->answer7 == 'yes') {
                        $worksheet->getCell('A71')->setValue('X');
                    } else {
                        $worksheet->getCell('A73')->setValue('X');
                    }

                    if ($cntSel > 1) {
                        $worksheet->getCell('A24')->setValue('Aggregate Planing Materiality');
                    } else {
                        if (isset($preTaxName)) {
                            $worksheet->getCell('A24')->setValue($preTaxName);
                        } else if (isset($TAssetName)) {
                            $worksheet->getCell('A24')->setValue($TAssetName);
                        } else if (isset($equityName)) {
                            $worksheet->getCell('A24')->setValue($equityName);
                        } else {
                            $worksheet->getCell('A24')->setValue($netRevenueName);;
                        }
                    }


                    // $materiality_val = ($E14 + $E15 + $E16 + $E17) / 2;

                    // //Planning Materiality (PiM as mentioned in Single Rule)
                    // $worksheet->getCell('E14')->setValue('=(' . $preTax . '%*I18)');
                    // $worksheet->getCell('E15')->setValue('=(' . $tAsset . '%*I19)');
                    // $worksheet->getCell('E16')->setValue('=(' . $equity . '%*I20)');
                    // $worksheet->getCell('E17')->setValue('=(' . $netRevenue . '%*I21)');

                    // ----- To calculate performance materiality as in EXCEL SHEET FORMULAS -----------------------------
                    // $F14 = $E14 * 10 / 100;
                    // $F15 = $E15 * 10 / 100;
                    // $F16 = $E16 * 10 / 100;
                    // $F17 = $E17 * 10 / 100;
                    $perf_materiality_val = 100;
                    // $perf_materiality_val = ($F14 + $F15 + $F16 + $F17) / 2;
                    // dd($perf_materiality_val, $materiality_val, $E14, $E15,$E16, $E17, $I18, $I19,$D16, $I21);I20
                    $materiality_exists = Setting::where('company_id', session('company_id'))
                        ->where('year_id', session('year_id'))
                        ->where('user_id', Auth::user()->id)
                        ->where('key', 'materiality')
                        ->first();

                    if ($materiality_exists) {
                        $materiality_exists->value = $materiality_val;
                        $materiality_exists->save();
                    } else {
                        Setting::create([
                            'key' => 'materiality',
                            'value' => $materiality_val,
                            'user_id' => Auth::user()->id,
                            'company_id' => session('company_id'),
                            'year_id' => session('year_id'),
                        ]);
                    }
                    // $perf_materiality_exists = Setting::where('company_id', session('company_id'))
                    //     ->where('year_id', session('year_id'))
                    //     ->where('user_id', Auth::user()->id)
                    //     ->where('key', 'perf_materiality')
                    //     ->first();
                    // if ($perf_materiality_exists) {
                    //     $perf_materiality_exists->value = $perf_materiality_val;
                    //     $perf_materiality_exists->save();
                    // } else {
                    //     Setting::create([
                    //         'key' => 'perf_materiality',
                    //         'value' => $perf_materiality_val,
                    //         'user_id' => Auth::user()->id,
                    //         'company_id' => session('company_id'),
                    //         'year_id' => session('year_id'),
                    //     ]);
                    // }

                    // ----------------------------------

                    // $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xls');
                    $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
                    $writer->save(storage_path('app/public/materiality.xlsx'));
                    return response()->download(storage_path('app/public/materiality.xlsx'));
                }
            } else {
                return back()->with('warning', 'Please Create Team First');
            }
        } else {
            $comp = Company::find(session('company_id'));
            if ($comp) {
                return redirect()->back()->with('warning', 'Account not found in ' . $comp->name . ' company, Upload trial to generate accounts');
            } else {
                return redirect()->back()->with('warning', 'Account not found in selected company, Upload trial to generate accounts');
            }
        }
    }



    public function risk_level()
    {
        return Inertia::render('Materiality/RiskLevel', []);
    }

    public function risk_level_download(Request $request)
    {
        $request->validate([
            "answer1" => "required",
            "answer2" => "required",
            "answer3" => "required",
            "answer4" => "required",
            "answer5" => "required",
            "answer6" => "required",
            "answer7" => "required",
            "answer8" => "required",
            "answer9" => "required",
            "answer10" => "required",
            "answer11" => "required",
            "answer12" => "required",
            "answer13" => "required",
            "answer14" => "required",
            "answer15" => "required",
            "answer16" => "required",
            "answer17" => "required",
            "answer18" => "required",
            "answer19" => "required",
            "answer20" => "required",
            "answer21" => "required",
            "answer22" => "required",
            "answer23" => "required",
            "answer24" => "required",
            "answer25" => "required",
            "answer26" => "required",
            "answer27" => "required",
            "answer28" => "required",
            "answer29" => "required",
        ]);
        $year = Year::where('company_id', session('company_id'))->where('id', session('year_id'))->first();
        if ($year) {
            $partner = $year->users()->role('partner')->first();
            $manager = $year->users()->role('manager')->first();
            $staff = $year->users()->role('staff')->first();
            //   dd($partner->name , $manager->name , $staff->name);


            if ($partner != null && $manager != null && $staff != null) {
                // $start = $year->begin ? new Carbon($year->begin) : null;
                $now = Carbon::now();
                $end = $year->end ? new Carbon($year->end) : null;
                $names = str_replace(["&"], "&amp;", $year->company->name);


                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load(public_path('materiality/Risk-FS-level.xlsx'));
                $worksheet = $spreadsheet->getActiveSheet();
                $worksheet->getCell('A5')->setValue("Client: " . $names);
                $worksheet->getCell('A6')->setValue("Period: " . $end->format("M j Y"));
                $worksheet->getCell('D4')->setValue(ucwords($staff->name));
                $worksheet->getCell('D5')->setValue($now->format("M j Y"));
                $worksheet->getCell('D6')->setValue(ucwords($manager->name));
                $worksheet->getCell('D8')->setValue(ucwords($partner->name));



                //answer1
                if ($request->answer1 == 'Low') {
                    $worksheet->getCell('B11')->setValue('Low');
                } else if ($request->answer1 == 'Medium') {
                    $worksheet->getCell('C11')->setValue('Medium');
                } else {
                    $worksheet->getCell('D11')->setValue('High');
                }

                //answer2
                if ($request->answer2 == 'Director/Management') {
                    $worksheet->getCell('B15')->setValue('Director / Management');
                } else if ($request->answer2 == 'Small-Minority') {
                    $worksheet->getCell('C15')->setValue('Small Minority');
                } else {
                    $worksheet->getCell('D15')->setValue('External Interest');
                }

                //answer3
                if ($request->answer3 == 'No') {
                    $worksheet->getCell('B16')->setValue('No');
                } else if ($request->answer3 == 'Possibly') {
                    $worksheet->getCell('C16')->setValue('Possibly');
                } else {
                    $worksheet->getCell('D16')->setValue('Yes');
                }


                //answer4
                if ($request->answer4 == 'No') {
                    $worksheet->getCell('B17')->setValue('No');
                } else if ($request->answer4 == 'Possibly') {
                    $worksheet->getCell('C17')->setValue('Possibly');
                } else {
                    $worksheet->getCell('D17')->setValue('Yes');
                }


                //answer5
                if ($request->answer5 == 'Yes') {
                    $worksheet->getCell('B21')->setValue('Yes');
                } else if ($request->answer5 == 'Sometimes') {
                    $worksheet->getCell('C21')->setValue('Sometimes');
                } else {
                    $worksheet->getCell('D21')->setValue('No');
                }


                //answer6
                if ($request->answer6 == 'Good') {
                    $worksheet->getCell('B22')->setValue('Good');
                } else if ($request->answer6 == 'Adequate') {
                    $worksheet->getCell('C22')->setValue('Adequate');
                } else {
                    $worksheet->getCell('D22')->setValue('Poor');
                }


                //answer7
                if ($request->answer7 == 'None') {
                    $worksheet->getCell('B23')->setValue('None');
                } else if ($request->answer7 == 'Some') {
                    $worksheet->getCell('C23')->setValue('Some');
                } else {
                    $worksheet->getCell('D23')->setValue('Many');
                }


                //answer8
                if ($request->answer8 == 'Yes') {
                    $worksheet->getCell('B24')->setValue('Yes');
                } else if ($request->answer8 == 'Sometimes') {
                    $worksheet->getCell('C24')->setValue('Sometimes');
                } else {
                    $worksheet->getCell('D24')->setValue('No');
                }

                //answer9
                if ($request->answer9 == 'Yes') {
                    $worksheet->getCell('B25')->setValue('Yes');
                } else if ($request->answer9 == 'Not-Applicable') {
                    $worksheet->getCell('C25')->setValue('Not Applicable');
                } else {
                    $worksheet->getCell('D25')->setValue('No');
                }


                //answer10
                if ($request->answer10 == 'Low-Risk') {
                    $worksheet->getCell('B29')->setValue('Low Risk');
                } else if ($request->answer10 == 'Medium-Risk') {
                    $worksheet->getCell('C29')->setValue('Medium Risk');
                } else {
                    $worksheet->getCell('D29')->setValue('High Risk');
                }


                //answer11
                if ($request->answer11 == 'Yes') {
                    $worksheet->getCell('B30')->setValue('Yes');
                } else if ($request->answer11 == 'Not-Applicable') {
                    $worksheet->getCell('C30')->setValue('Not Applicable');
                } else {
                    $worksheet->getCell('D30')->setValue('No');
                }


                //answer12
                if ($request->answer12 == 'Insignificant') {
                    $worksheet->getCell('B31')->setValue('Insignificant');
                } else if ($request->answer12 == 'Moderate') {
                    $worksheet->getCell('C31')->setValue('Moderate');
                } else {
                    $worksheet->getCell('D31')->setValue('Major');
                }

                //answer13
                if ($request->answer13 == 'No') {
                    $worksheet->getCell('B32')->setValue('No');
                } else if ($request->answer13 == 'Not-Applicable') {
                    $worksheet->getCell('C32')->setValue('Not Applicable');
                } else {
                    $worksheet->getCell('D32')->setValue('Yes');
                }

                //answer14
                if ($request->answer14 == 'No') {
                    $worksheet->getCell('B33')->setValue('No');
                } else if ($request->answer14 == 'Possibly') {
                    $worksheet->getCell('C33')->setValue('Possibly');
                } else {
                    $worksheet->getCell('D33')->setValue('Yes');
                }


                //answer15
                if ($request->answer15 == 'No') {
                    $worksheet->getCell('B34')->setValue('No');
                } else if ($request->answer15 == 'Some') {
                    $worksheet->getCell('C34')->setValue('Some');
                } else {
                    $worksheet->getCell('D34')->setValue('Yes, a lot');
                }

                //answer16
                if ($request->answer16 == 'No') {
                    $worksheet->getCell('B35')->setValue('No');
                } else if ($request->answer16 == 'Not-Applicable') {
                    $worksheet->getCell('C35')->setValue('Not Applicable');
                } else {
                    $worksheet->getCell('D35')->setValue('Yes');
                }

                //answer17
                if ($request->answer17 == 'Always') {
                    $worksheet->getCell('B39')->setValue('Always');
                } else if ($request->answer17 == 'Sometimes') {
                    $worksheet->getCell('C39')->setValue('Sometimes');
                } else {
                    $worksheet->getCell('D39')->setValue('Rarely');
                }

                //answer18
                if ($request->answer18 == 'No') {
                    $worksheet->getCell('B40')->setValue('No');
                } else if ($request->answer18 == 'Not-Applicable') {
                    $worksheet->getCell('C40')->setValue('Not Applicable');
                } else {
                    $worksheet->getCell('D40')->setValue('Yes');
                }

                //answer19
                if ($request->answer19 == 'No') {
                    $worksheet->getCell('B41')->setValue('No');
                } else if ($request->answer19 == 'Sometimes') {
                    $worksheet->getCell('C41')->setValue('Sometimes');
                } else {
                    $worksheet->getCell('D41')->setValue('Often');
                }

                //answer20
                if ($request->answer20 == 'No') {
                    $worksheet->getCell('B42')->setValue('No');
                } else if ($request->answer20 == 'Not-Applicable') {
                    $worksheet->getCell('C42')->setValue('Not Applicable');
                } else {
                    $worksheet->getCell('D42')->setValue('Yes');
                }

                //answer21
                if ($request->answer21 == 'No') {
                    $worksheet->getCell('B43')->setValue('No');
                } else if ($request->answer21 == 'Not-Applicable') {
                    $worksheet->getCell('C43')->setValue('Not Applicable');
                } else {
                    $worksheet->getCell('D43')->setValue('Yes');
                }

                //answer22
                if ($request->answer22 == 'No') {
                    $worksheet->getCell('B44')->setValue('No');
                } else if ($request->answer22 == 'Not-Applicable') {
                    $worksheet->getCell('C44')->setValue('Not Applicable');
                } else {
                    $worksheet->getCell('D44')->setValue('Yes');
                }

                //answer23
                if ($request->answer23 == 'No') {
                    $worksheet->getCell('B45')->setValue('No');
                } else if ($request->answer23 == 'Not-Applicable') {
                    $worksheet->getCell('C45')->setValue('Not Applicable');
                } else {
                    $worksheet->getCell('D45')->setValue('Yes');
                }

                //answer24
                if ($request->answer24 == 'No') {
                    $worksheet->getCell('B49')->setValue('No');
                } else if ($request->answer24 == 'Not-Applicable') {
                    $worksheet->getCell('C49')->setValue('Not Applicable');
                } else {
                    $worksheet->getCell('D49')->setValue('Yes');
                }

                //answer25
                if ($request->answer25 == 'No') {
                    $worksheet->getCell('B50')->setValue('No');
                } else if ($request->answer25 == 'Not-Applicable') {
                    $worksheet->getCell('C50')->setValue('Not Applicable');
                } else {
                    $worksheet->getCell('D50')->setValue('Yes');
                }

                //answer26
                if ($request->answer26 == 'No') {
                    $worksheet->getCell('B51')->setValue('No');
                } else if ($request->answer26 == 'Not-Applicable') {
                    $worksheet->getCell('C51')->setValue('Not Applicable');
                } else {
                    $worksheet->getCell('D51')->setValue('Yes');
                }


                //answer27
                if ($request->answer27 == 'No') {
                    $worksheet->getCell('B52')->setValue('No');
                } else if ($request->answer27 == 'Not-Applicable') {
                    $worksheet->getCell('C52')->setValue('Not Applicable');
                } else {
                    $worksheet->getCell('D52')->setValue('Yes');
                }

                //answer28
                if ($request->answer28 == 'No') {
                    $worksheet->getCell('B53')->setValue('No');
                } else if ($request->answer28 == 'Not-Applicable') {
                    $worksheet->getCell('C53')->setValue('Not Applicable');
                } else {
                    $worksheet->getCell('D53')->setValue('Yes');
                }

                //answer29
                if ($request->answer29 == 'No') {
                    $worksheet->getCell('B54')->setValue('No');
                } else if ($request->answer29 == 'Not-Applicable') {
                    $worksheet->getCell('C54')->setValue('Not Applicable');
                } else {
                    $worksheet->getCell('D54')->setValue('Yes');
                }

                $perf_materiality_val = $request->answer1;
                $perf_materiality_exists = Setting::where('company_id', session('company_id'))
                    ->where('year_id', session('year_id'))
                    ->where('user_id', Auth::user()->id)
                    ->where('key', 'perf_materiality')
                    ->first();
                if ($perf_materiality_exists) {
                    $perf_materiality_exists->value = $perf_materiality_val;
                    $perf_materiality_exists->save();
                } else {
                    Setting::create([
                        'key' => 'perf_materiality',
                        'value' => $perf_materiality_val,
                        'user_id' => Auth::user()->id,
                        'company_id' => session('company_id'),
                        'year_id' => session('year_id'),
                    ]);
                }

                // ----------------------------------

                // $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xls');
                $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
                $writer->save(storage_path('app/public/Risk-FS-level.xlsx'));
                return response()->download(storage_path('app/public/Risk-FS-level.xlsx'));
            } else {
                return back()->with('warning', 'Please Create Team First');
            }
        } else {
            $comp = Company::find(session('company_id'));
            if ($comp) {
                return redirect()->back()->with('warning', 'Year Not Found' . $comp->name . '');
            } else {
                return redirect()->back()->with('warning', 'Company not found');
            }
        }
    }

    public function rsc()
    {
        $accounts = Account::where('company_id', session('company_id'))->orderBy('name', 'asc')->get();
        return Inertia::render('Materiality/Rsc', [
            'accounts' => $accounts,
        ]);
    }



    public function rsc_download(Request $request)
    {
        // dd($request->all());
        $request->validate([
            "account_id" => "required",
            "classification" => "required",
            "completeness" => "required",
            "accuracy" => "required",
            "cut_off" => "required",
            "pre_dis" => "required",
            "answer1" => "required",
            "answer2" => "required",
            "answer3" => "required",
            "answer4" => "required",
            "answer5" => "required",
            "answer6" => "required",
            "answer7" => "required",
            "answer8" => "required",
        ]);
        $avg_mat = Setting::where('company_id', session('company_id'))->where('year_id', session('year_id'))->where('key', 'materiality')->first();
        if ($avg_mat) {
            $avg_mat_val = $avg_mat->value;
            $avg_perf_mat = Setting::where('company_id', session('company_id'))->where('year_id', session('year_id'))->where('key', 'perf_materiality')->first();
            if ($avg_perf_mat) {
                $year = Year::where('company_id', session('company_id'))->where('id', session('year_id'))->first();
                if ($year) {
                    $partner = $year->users()->role('partner')->first();
                    $manager = $year->users()->role('manager')->first();
                    $staff = $year->users()->role('staff')->first();
                    //   dd($partner->name , $manager->name , $staff->name);


                    if ($partner != null && $manager != null && $staff != null) {
                        $now = Carbon::now();
                        $end = $year->end ? new Carbon($year->end) : null;
                        $names = str_replace(["&"], "&amp;", $year->company->name);

                        $account = Account::where('company_id', session('company_id'))->where('id', $request->account_id)->first();
                        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load(public_path('materiality/Risk-RSC.xlsx'));
                        $worksheet = $spreadsheet->getActiveSheet();
                        $worksheet->getCell('B5')->setValue($names);
                        $worksheet->getCell('B6')->setValue($end->format("M j Y"));
                        $worksheet->getCell('B7')->setValue($account->name);
                        $worksheet->getCell('G5')->setValue(ucwords($staff->name));
                        $worksheet->getCell('G6')->setValue($now->format("M j Y"));
                        $worksheet->getCell('G7')->setValue(ucwords($manager->name));
                        $worksheet->getCell('G9')->setValue(ucwords($partner->name));
                        $worksheet->getCell('D12')->setValue($avg_perf_mat->value);
                        $worksheet->getCell('D13')->setValue($avg_mat->value);
                        // dd($request->classification, $request->completeness, $request->accuracy, $request->cut_off, $request->pre_dis);

                        $worksheet->getCell('C23')->setValue($request->classification);
                        $worksheet->getCell('D23')->setValue($request->completeness);
                        $worksheet->getCell('E23')->setValue($request->accuracy);
                        $worksheet->getCell('F23')->setValue($request->cut_off);
                        $worksheet->getCell('G23')->setValue($request->pre_dis);



                        if ($request->answer1 == 'Yes') {
                            $worksheet->getCell('E44')->setValue('⌐');
                        } else if ($request->answer1 == 'Not-Applicable') {
                            $worksheet->getCell('F44')->setValue('⌐');
                        } else {
                            $worksheet->getCell('G44')->setValue('⌐');
                        }

                        if ($request->answer2 == 'Yes') {
                            $worksheet->getCell('E45')->setValue('⌐');
                        } else if ($request->answer2 == 'Not-Applicable') {
                            $worksheet->getCell('F45')->setValue('⌐');
                        } else {
                            $worksheet->getCell('G45')->setValue('⌐');
                        }

                        if ($request->answer3 == 'Yes') {
                            $worksheet->getCell('E46')->setValue('⌐');
                        } else if ($request->answer3 == 'Not-Applicable') {
                            $worksheet->getCell('F46')->setValue('⌐');
                        } else {
                            $worksheet->getCell('G46')->setValue('⌐');
                        }

                        if ($request->answer4 == 'Yes') {
                            $worksheet->getCell('E47')->setValue('⌐');
                        } else if ($request->answer4 == 'Not-Applicable') {
                            $worksheet->getCell('F47')->setValue('⌐');
                        } else {
                            $worksheet->getCell('G47')->setValue('⌐');
                        }

                        if ($request->answer5 == 'Yes') {
                            $worksheet->getCell('E48')->setValue('⌐');
                        } else if ($request->answer5 == 'Not-Applicable') {
                            $worksheet->getCell('F48')->setValue('⌐');
                        } else {
                            $worksheet->getCell('G48')->setValue('⌐');
                        }
                        if ($request->answer6 == 'Yes') {
                            $worksheet->getCell('E49')->setValue('⌐');
                        } else if ($request->answer6 == 'Not-Applicable') {
                            $worksheet->getCell('F49')->setValue('⌐');
                        } else {
                            $worksheet->getCell('G49')->setValue('⌐');
                        }
                        if ($request->answer7 == 'Yes') {
                            $worksheet->getCell('E50')->setValue('⌐');
                        } else if ($request->answer7 == 'Not-Applicable') {
                            $worksheet->getCell('F50')->setValue('⌐');
                        } else {
                            $worksheet->getCell('G50')->setValue('⌐');
                        }
                        if ($request->answer8 == 'Yes') {
                            $worksheet->getCell('E51')->setValue('⌐');
                        } else if ($request->answer8 == 'Not-Applicable') {
                            $worksheet->getCell('F51')->setValue('⌐');
                        } else {
                            $worksheet->getCell('G51')->setValue('⌐');
                        }


                        $classification = 0;
                        $completeness = 0;
                        $accuracy = 0;
                        $cut_off = 0;

                        $classification_val = 0;
                        $completeness_val = 0;
                        $accuracy_val = 0;
                        $cut_off_val = 0;
                        $pre_Disclosure_val = 0;
                        if ($avg_perf_mat->value == 'Low') {
                            $Low_low_mat = 1.2;
                            $Low_med_mat = 1.4;
                            $Low_high_mat = 1.6;

                            //Classigfication
                            if ($request->classification == 'Low') {
                                $classification = $avg_mat_val / $Low_low_mat;
                                $classification_val = $Low_low_mat;
                            } else if ($request->classification == 'Medium') {
                                $classification = $avg_mat_val / $Low_med_mat;
                                $classification_val = $Low_med_mat;
                            } else {
                                $classification = $avg_mat_val / $Low_high_mat;
                                $classification_val = $Low_high_mat;
                            }

                            //Completness
                            if ($request->completeness == 'Low') {
                                $completeness = $avg_mat_val / $Low_low_mat;
                                $completeness_val = $Low_low_mat;
                            } else if ($request->completeness == 'Medium') {
                                $completeness = $avg_mat_val / $Low_med_mat;
                                $completeness_val = $Low_med_mat;
                            } else {
                                $completeness = $avg_mat_val / $Low_high_mat;
                                $completeness_val = $Low_high_mat;
                            }


                            //accuracy
                            if ($request->accuracy == 'Low') {
                                $accuracy = $avg_mat_val / $Low_low_mat;
                                $accuracy_val = $Low_low_mat;
                            } else if ($request->completeness == 'Medium') {
                                $accuracy = $avg_mat_val / $Low_med_mat;
                                $accuracy_val = $Low_med_mat;
                            } else {
                                $accuracy = $avg_mat_val / $Low_high_mat;
                                $accuracy_val = $Low_high_mat;
                            }


                            //Cut Off
                            if ($request->cut_off == 'Low') {
                                $cut_off = $avg_mat_val / $Low_low_mat;
                                $cut_off_val = $Low_low_mat;
                            } else if ($request->cut_off == 'Medium') {
                                $cut_off = $avg_mat_val / $Low_med_mat;
                                $cut_off_val = $Low_med_mat;
                            } else {
                                $cut_off = $avg_mat_val / $Low_high_mat;
                                $cut_off_val = $Low_high_mat;
                            }

                            // pre_Disclosure

                            if ($request->pre_dis == 'Low') {
                                $pre_Disclosure = $avg_mat_val / $Low_low_mat;
                                $pre_Disclosure_val = $Low_low_mat;
                            } else if ($request->pre_dis == 'Medium') {
                                $pre_Disclosure = $avg_mat_val / $Low_med_mat;
                                $pre_Disclosure_val = $Low_med_mat;
                            } else {
                                $pre_Disclosure = $avg_mat_val / $Low_high_mat;
                                $pre_Disclosure_val = $Low_high_mat;
                            }
                        } else if ($avg_perf_mat->value == 'Medium') {
                            $Med_low_mat = 1.4;
                            $Med_med_mat = 1.8;
                            $Med_high_mat = 2.1;


                            //Classigfication
                            if ($request->classification == 'Low') {
                                $classification = $avg_mat_val / $Med_low_mat;
                                $classification_val = $Med_low_mat;
                            } else if ($request->classification == 'Medium') {
                                $classification = $avg_mat_val / $Med_med_mat;
                                $classification_val = $Med_med_mat;
                            } else {
                                $classification = $avg_mat_val / $Med_high_mat;
                                $classification_val = $Med_high_mat;
                            }

                            //Completness
                            if ($request->completeness == 'Low') {
                                $completeness = $avg_mat_val / $Med_low_mat;
                                $completeness_val = $Med_low_mat;
                            } else if ($request->completeness == 'Medium') {
                                $completeness = $avg_mat_val / $Med_med_mat;
                                $completeness_val = $Med_med_mat;
                            } else {
                                $completeness = $avg_mat_val / $Med_high_mat;
                                $completeness_val = $Med_high_mat;
                            }


                            //accuracy
                            if ($request->accuracy == 'Low') {
                                $accuracy = $avg_mat_val / $Med_low_mat;
                                $accuracy_val = $Med_low_mat;
                            } else if ($request->completeness == 'Medium') {
                                $accuracy = $avg_mat_val / $Med_med_mat;
                                $accuracy_val = $Med_med_mat;
                            } else {
                                $accuracy = $avg_mat_val / $Med_high_mat;
                                $accuracy_val = $Med_high_mat;
                            }


                            //Cut Off
                            if ($request->cut_off == 'Low') {
                                $cut_off = $avg_mat_val / $Med_low_mat;
                                $cut_off_val = $Med_low_mat;
                            } else if ($request->cut_off == 'Medium') {
                                $cut_off = $avg_mat_val / $Med_med_mat;
                                $cut_off_val = $Med_med_mat;
                            } else {
                                $cut_off = $avg_mat_val / $Med_high_mat;
                                $cut_off_val = $Med_high_mat;
                            }

                            //pre_Disclosure

                            if ($request->pre_dis == 'Low') {
                                $pre_Disclosure = $avg_mat_val / $Med_low_mat;
                                $pre_Disclosure_val = $Med_low_mat;
                            } else if ($request->pre_dis == 'Medium') {
                                $pre_Disclosure = $avg_mat_val / $Med_med_mat;
                                $pre_Disclosure_val = $Med_med_mat;
                            } else {
                                $pre_Disclosure = $avg_mat_val / $Med_high_mat;
                                $pre_Disclosure_val = $Med_high_mat;
                            }
                        } else {
                            $High_low_mat = 1.6;
                            $High_med_mat = 2.1;
                            $High_high_mat = 2.5;

                            //Classigfication
                            if ($request->classification == 'Low') {
                                $classification = $avg_mat_val / $High_low_mat;
                                $classification_val = $High_low_mat;
                            } else if ($request->classification == 'Medium') {
                                $classification = $avg_mat_val / $High_med_mat;
                                $classification_val = $High_med_mat;
                            } else {
                                $classification = $avg_mat_val / $High_high_mat;
                                $classification_val = $High_high_mat;
                            }

                            //Completness
                            if ($request->completeness == 'Low') {
                                $completeness = $avg_mat_val / $High_low_mat;
                                $completeness_val = $High_low_mat;
                            } else if ($request->completeness == 'Medium') {
                                $completeness = $avg_mat_val / $High_med_mat;
                                $completeness_val = $High_med_mat;
                            } else {
                                $completeness = $avg_mat_val / $High_high_mat;
                                $completeness_val = $High_high_mat;
                            }


                            //accuracy
                            if ($request->accuracy == 'Low') {
                                $accuracy = $avg_mat_val / $High_low_mat;
                                $accuracy_val = $High_low_mat;
                            } else if ($request->completeness == 'Medium') {
                                $accuracy = $avg_mat_val / $High_med_mat;
                                $accuracy_val = $High_med_mat;
                            } else {
                                $accuracy = $avg_mat_val / $High_high_mat;
                                $accuracy_val = $High_high_mat;
                            }


                            //Cut Off
                            if ($request->cut_off == 'Low') {
                                $cut_off = $avg_mat_val / $High_low_mat;
                                $cut_off_val = $High_low_mat;
                            } else if ($request->cut_off == 'Medium') {
                                $cut_off = $avg_mat_val / $High_med_mat;
                                $cut_off_val = $High_med_mat;
                            } else {
                                $cut_off = $avg_mat_val / $High_high_mat;
                                $cut_off_val = $High_high_mat;
                            }

                            // pre_Disclosure

                            if ($request->pre_dis == 'Low') {
                                $pre_Disclosure = $avg_mat_val / $High_low_mat;
                                $pre_Disclosure_val = $High_low_mat;
                            } else if ($request->pre_dis == 'Medium') {
                                $pre_Disclosure = $avg_mat_val / $High_med_mat;
                                $pre_Disclosure_val = $High_med_mat;
                            } else {
                                $pre_Disclosure = $avg_mat_val / $High_high_mat;
                                $pre_Disclosure_val = $High_high_mat;
                            }
                        }
                        // dd($classification_val, $completeness_val, $accuracy_val,  $cut_off_val,  $pre_Disclosure_val);
                        $sum = $classification_val + $completeness_val + $accuracy_val +  $cut_off_val +  $pre_Disclosure_val;
                        $pm = round((5 * $avg_mat_val) / $sum);
                        $risk_exists = Risk::where('company_id', session('company_id'))
                            ->where('year_id', session('year_id'))
                            ->where('account_id', $request->account_id)
                            ->first();
                        if ($risk_exists) {
                            $risk_exists->classification = round($classification);
                            $risk_exists->completeness = round($completeness);
                            $risk_exists->accuracy = round($accuracy);
                            $risk_exists->cut_off = round($cut_off);
                            $risk_exists->presentation_disclosure = round($pre_Disclosure);
                            $risk_exists->overall = 0;
                            $risk_exists->perform_materiality = $pm;
                            $risk_exists->company_id = session('company_id');
                            $risk_exists->year_id = session('year_id');
                            $risk_exists->account_id = $request->account_id;                // $risk_exists->value = $perf_materiality_val;
                            $risk_exists->save();
                        } else {
                            Risk::create([
                                'classification' => $classification,
                                'completeness' => $completeness,
                                'accuracy' => $accuracy,
                                'cut_off' => $cut_off,
                                'presentation_disclosure' => $pre_Disclosure,
                                'overall' => 0,
                                'perform_materiality' => $pm,
                                'company_id' => session('company_id'),
                                'year_id' => session('year_id'),
                                'account_id' => $request->account_id,
                            ]);
                        }

                        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
                        $writer->save(storage_path('app/public/Risk-RSC.xlsx'));
                        return response()->download(storage_path('app/public/Risk-RSC.xlsx'));
                    } else {
                        return back()->with('warning', 'Please Create Team First');
                    }
                } else {
                    $comp = Company::find(session('company_id'));
                    if ($comp) {
                        return redirect()->back()->with('warning', 'Year Not Found' . $comp->name . '');
                    } else {
                        return redirect()->back()->with('warning', 'Company not found');
                    }
                }
            } else {
                return redirect()->route('risk_level')->with('warning', 'Download Overall Financial Statement Risk level Fisrt');
            }
        } else {
            return redirect()->route('materialities')->with('warning', 'Download Materiality Schedule Fisrt');
        }
    }
}
