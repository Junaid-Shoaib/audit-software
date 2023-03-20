<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Template;
use Illuminate\Support\Facades\Storage;
use File;


class TemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {


        Storage::makeDirectory('/public/' . 'planing');
        Storage::makeDirectory('/public/' . 'execution');
        Storage::makeDirectory('/public/' . 'completion');
        Storage::makeDirectory('/public/' . 'report');
        $planings = [
            '0' => 'Acceptance Memo.docx',
            '1' => 'Accounting Estimates - Planning.docx',
            '2' => 'Classification of Company (Third Schedule).docx',
            '3' => 'Client Acceptance _ Continuance.docx',
            '4' => 'Client Meeting Minutes.docx',
            '5' => 'Client Overview.docx',
            '6' => 'Communications - Planning.docx',
            '7' => 'Consideration of Laws and Regulations.docx',
            '8' => 'Considerations Relating to Group Audit and Use of Work of Component Auditors.docx',
            '9' => 'Continuance Memo.docx',
            '10' => 'Details of Bankers and Professional Advisors.docx',
            '11' => 'Engagement Budgeting - Overview.xlsx',
            '12' => 'Engagement Budgeting - Task Plan and Budget.xlsx',
            '13' => 'Engagement Team Roles and Responsibilities.docx',
            '14' => 'Entity_s Use of Management_s Expert.docx',
            '15' => 'Entity_s Use of Service Organization.docx',
            '16' => 'Fraud Discussion Minutes.docx',
            '17' => 'Fraud Inquiries.docx',
            '18' => 'Fraud Risk Assessment.docx',
            '19' => 'Fraud Risk Factors Checklist.docx',
            '20' => 'General Purpose CIS Template.docx',
            '21' => 'Going Concern - Planning.docx',
            '22' => 'Going Concern Assessment.docx',
            '23' => 'Group Audit and Use of Work of Component Auditors.docx',
            '24' => 'Internal Control Template.docx',
            '25' => 'ISA 315 (Revised) Template.docx',
            '26' => 'Kickoff Meeting Minutes.docx',
            '27' => 'List of Related Parties.docx',
            '28' => 'Materiality.docx',
            '29' => 'Materiality Calculation - Calculation.xlsx',
            '30' => 'Materiality Calculation - Guidance.xlsx',
            '31' => 'Minutes and Contracts.docx',
            '32' => 'Minutes Review.docx',
            '33' => 'Other Specific Considerations.docx',
            '34' => 'Planning Memorandum.docx',
            '35' => 'Planning Process.docx',
            '36' => 'Points Forward From Previous Year.docx',
            '37' => 'Points Forward From Previous Year Second.docx',
            '38' => 'Professional Ethics and Independence.docx',
            '39' => 'Register of Non-Audit Services.docx',
            '40' => 'Register of Number of Years of Involvement.docx',
            '41' => 'Related Parties - Planning.docx',
            '42' => 'Risk Assessment.docx',
            '43' => 'Risk Assessment Analytical Procedures.docx',
            '44' => 'Risk Assessment Analytics - Guidance.xlsx',
            '45' => 'Risk Assessment Analytics - Ratio Analysis.xlsx',
            '36' => 'Risk Assessment Analytics - Trent Analysis.xlsx',
            '47' => 'Risk Assessment Document.docx',
            '48' => 'Terms of Engagement.docx',
            '49' => 'Threats to Independence.docx',
            '50' => 'Understand and Evaluate Internal Control.docx',
            '51' => 'Understanding Accounting Estimates.docx',
            '52' => 'Understanding the Entity and Its Environment.docx',
            '53' => 'Use of Work of Auditor_s Expert.docx',
            '54' => 'Use of Work of Internal Auditors.docx',
        ];

        foreach ($planings as $key => $value) {
            $planing = new Template();
            $planing->name = $value;
            $planing->path = 'planing/' . $value;
            $planing->type = 'planing';
            $planing->company_id = session('company_id');
            $planing->year_id = session('year_id');
            File::copy(public_path('/temp/' . $value), storage_path('app/public/planing/' . $value));
            $planing->save();
        }

        $completion = [
            '01' => 'Accounting Estimates - Completion.docx',
            '02' => 'Audit Report.docx',
            '03' => 'Communications - Completion.docx',
            '04' => 'Completion Checklists.docx',
            '05' => 'Completion Checklists 1.docx',
            '06' => 'Concluding on Preliminary Assessments.docx',
            '07' => 'Final Review of Financial Statements.docx',
            '08' => 'Fraud.docx',
            '09' => 'Going Concern - Completion.docx',
            '10' => 'Justification for the Audit Report.docx',
            '11' => 'Laws and Regulations.docx',
            '12' => 'Overall Conclusion Analytical Procedures.docx',
            '13' => 'Overall Conclusion Analytics - Guidance.xlsx',
            '14' => 'Overall Conclusion Analytics - Ratio Analysis.xlsx',
            '15' => 'Overall Conclusion Analytics - Trend Analysis.xlsx',
            '16' => 'Points Forward to Next Year.docx',
            '17' => 'Points Forward to Next Year 1.docx',
            '18' => 'Register of Laws and Regulations.docx',
            '19' => 'Related Parties - Completion.docx',
            '20' => 'Significant Matters.docx',
            '21' => 'Significant Matters 1.docx',
            '22' => 'Subsequent Events.docx',
            '23' => 'Subsequent Events Checklist.docx',
            '24' => 'Summary of Misstatements.docx',
            '25' => 'Summary of Misstatements 1.docx',
            '26' => 'Summary Review Memorandum for Partner.docx',
            '27' => 'Summary Review Memorandum for Partner 1.docx',
            '28' => 'Written Representations.docx',
        ];

        foreach ($completion as $key => $value) {
            $completion = new Template();
            $completion->name = $value;
            $completion->path = 'completion/' . $value;
            $completion->type = 'completion';
            $completion->company_id = session('company_id');
            $completion->year_id = session('year_id');
            File::copy(public_path('/temp/' . $value), storage_path('app/public/completion/' . $value));
            $completion->save();
        }


        $execution = [
            '01' => 'Accrued Expenses.docx',
            '02' => 'Accrued Expenses.xlsx',
            '03' => 'Bank Confirmation Format.docx',
            '04' => 'Cash and Bank Balances.docx',
            '05' => 'Cash and Bank Balances.xlsx',
            '06' => 'Contingencies and Commitments.docx',
            '07' => 'Contingencies and Commitments.xlsx',
            '08' => 'Cost of Sales.docx',
            '09' => 'Cost of Sales.xlsx',
            '10' => 'Debtors Creditors Confirmation Format.docx',
            '11' => 'Deferred Liabilities.docx',
            '12' => 'Deferred Liabilities.xlsx',
            '13' => 'Deposits and Prepayments.docx',
            '14' => 'Deposits and Prepayments.xlsx',
            '15' => 'Direct Taxation.docx',
            '16' => 'Direct Taxation.xlsx',
            '17' => 'Dividend Payable.docx',
            '18' => 'Dividend Payable.xlsx',
            '19' => 'Equity.docx',
            '20' => 'Equity.xlsx',
            '21' => 'Expenses.docx',
            '22' => 'Expenses.xlsx',
            '23' => 'External Confirmations.docx',
            '24' => 'Financial Charges.docx',
            '25' => 'Financial Charges.xlsx',
            '26' => 'Fixed Assets.xlsx',
            '27' => 'Fixed Assets (Tangible, Intangible and CWIP).docx',
            '28' => 'Inventory Count Program.docx',
            '29' => 'Investment Properties.docx',
            '30' => 'Investment Properties.xlsx',
            '31' => 'Investments.docx',
            '32' => 'Investments.xlsx',
            '33' => 'Journal Entries Testing.docx',
            '34' => 'Journal Entries Testing.xlsx',
            '35' => 'Lawyer Confirmation Format.docx',
            '36' => 'Lease Confirmation Format.docx',
            '37' => 'Liabilities Against Assets.docx',
            '38' => 'Liabilities Against Assets.xlsx',
            '39' => 'Loan Confirmation Format.docx',
            '40' => 'Loans and Advances.docx',
            '41' => 'Loans and Advances.xlsx',
            '42' => 'Long Term Debt.xlsx',
            '43' => 'Long Term Deposits.docx',
            '44' => 'Long Term Deposits.xlsx',
            '45' => 'Means of Selecting Items for Testing.docx',
            '46' => 'Means of Selecting Items for Testing Forms.xlsx',
            '47' => 'Other Income.docx',
            '48' => 'Other Income.xlsx',
            '49' => 'Payroll.docx',
            '50' => 'Payroll.xlsx',
            '51' => 'Sales.docx',
            '52' => 'Sales.xlsx',
            '53' => 'Short Term and Long Term Debt.docx',
            '54' => 'Short Term Debt.xlsx',
            '55' => 'Stores, Spares and Stock in Trade.docx',
            '56' => 'Stores, Spares and Stock in Trade.xlsx',
            '57' => 'Surplus on Revaluation.docx',
            '58' => 'Surplus on Revaluation.xlsx',
            '59' => 'Tax Adviser Confirmation Format.docx',
            '60' => 'Trade Payables.docx',
            '61' => 'Trade Payables.xlsx',
            '62' => 'Trade Receivables.docx',
            '63' => 'Trade Receivables.xlsx',
        ];

        foreach ($execution as $key => $value) {
            $execution = new Template();
            $execution->name = $value;
            $execution->path = 'execution/' . $value;
            $execution->type = 'execution';
            $execution->company_id = session('company_id');
            $execution->year_id = session('year_id');
            File::copy(public_path('/temp/' . $value), storage_path('app/public/execution/' . $value));
            $execution->save();
        }


        $reports = [
            '01' => 'Auditors Report to the Trustees-Board of Governors-Management Committee.docx',
            '02' => 'Cost Auditors Report.docx',
            '03' => 'Independent Reasonable Assurance Report on Statement of Free Float of Shares.docx',
            '04' => 'Review Report on the Statement of Compliance contained in Listed Companies.docx',
            '05' => 'Report on Review of Interim Financial Statements.docx',
            '06' => 'Report on the Audit of the Financial Statements of.docx',
            '07' => 'Auditor‘s Report on Consolidated Financial Statements of Holding Company.docx',
            '08' => 'Review Report on Statement of Net Capital Balance.docx',
        ];

        foreach ($reports as $key => $value) {
            $report = new Template();
            $report->name = $value;
            $report->path = 'report/' . $value;
            $report->type = 'report';
            $report->company_id = session('company_id');
            $report->year_id = session('year_id');
            File::copy(public_path('/temp/' . $value), storage_path('app/public/report/' . $value));
            $report->save();
        }
    }
}
