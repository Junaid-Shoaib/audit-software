<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Template;
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
        $planings = [
            '0' => 'Acceptance Memo.docx',
            '1' => 'Accounting Estimates.docx',
            '2' => 'Classification of Company (Third Schedule).docx',
            '3' => 'Client Acceptance _ Continuance.docx',
            '4' => 'Client Meeting Minutes.docx',
            '5' => 'Client Overview.docx',
            '6' => 'Communications - Planning.docx',
            '7' => 'Consideration of Laws and Regulations.docx',
            '8' => 'Considerations Relating to Group Audit and Use of Work of Component Auditors.docx',
            '9' => 'Continuance Memo.docx',
            '10' => 'Details of Bankers and Professional Advisors.docx',
            '11' => 'Engagement Budgeting.xlsx',
            '12' => 'Engagement Team Roles and Responsibilities.docx',
            '13' => 'Entity_s Use of Management_s Expert.docx',
            '14' => 'Entity_s Use of Service Organization.docx',
            '15' => 'Fraud Inquiries.docx',
            '16' => 'Fraud Risk Assessment.docx',
            '17' => 'Fraud Risk Factors Checklist.docx',
            '18' => 'General Purpose CIS Template.docx',
            '19' => 'Going Concern.docx',
            '20' => 'Group Audit and Use of Work of Component Auditors.docx',
            '21' => 'ISA 315 (Revised) Template.docx',
            '22' => 'Kickoff Meeting Minutes.docx',
            '23' => 'List of Related Parties.docx',
            '24' => 'Materiality.docx',
            '25' => 'Materiality Calculation.xlsx',
            '26' => 'Minutes Review.docx',
            '27' => 'Other Specific Considerations.docx',
            '28' => 'Planning Memorandum.docx',
            '29' => 'Planning Process.docx',
            '30' => 'Points Forward From Previous Year.docx',
            '31' => 'Points Forward From Previous Year Second.docx',
            '32' => 'Professional Ethics and Independence.docx',
            '33' => 'Register of Non-Audit Services.docx',
            '34' => 'Register of Number of Years of Involvement.docx',
            '35' => 'Related Parties.docx',
            '36' => 'Risk Assessment.docx',
            '37' => 'Risk Assessment Analytical Procedures.docx',
            '38' => 'Risk Assessment Document.docx',
            '39' => 'Risk Assessment Analytics.xlsx',
            '40' => 'Terms of Engagement.docx',
            '41' => 'Threats to Independence.docx',
            '42' => 'Understand and Evaluate Internal Control.docx',
            '43' => 'Understanding Accounting Estimates.docx',
            '44' => 'Understanding the Entity and Its Environment.docx',
            '45' => 'Use of Work of Auditor_s Expert.docx',
            '46' => 'Use of Work of Internal Auditors.docx',
        ];

        foreach ($planings as $key => $value) {
          $planing = new Template();
          $planing->name = $value;
          $planing->path = 'planing/'.$value;
          $planing->type = 'planing';
          $planing->company_id = session('company_id');
          $planing->year_id = session('year_id');
          File::copy(public_path('/temp/'. $value), storage_path('app/public/planing/'.$value));
          $planing->save();
        }

        //
    }
}
