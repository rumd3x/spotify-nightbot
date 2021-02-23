<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\AppSetting;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Repositories\AppSettingRepository;

class AppSettingsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $inputs = [
            [
                'display' => AppSettingRepository::get(AppSetting::SPREADSHEET_CURRENT_TEMPLATE_FILENAME),
                'description' => 'Upload Spreasheet Template (csv, xls)',
                'type' => 'file',
                'name' => AppSetting::SPREADSHEET_CURRENT_TEMPLATE_FILENAME,
            ],
            [
                'display' => 'Cell with the Person Name (Column + Row)',
                'type' => 'text',
                'name' => AppSetting::SPREADSHEET_HEADER_PERSON_NAME,
                'value' => AppSettingRepository::get(AppSetting::SPREADSHEET_HEADER_PERSON_NAME),
            ],
            [
                'display' => 'Cell with header for respective month (Column + Row)',
                'type' => 'text',
                'name' => AppSetting::SPREADSHEET_HEADER_MONTH_CELL,
                'value' => AppSettingRepository::get(AppSetting::SPREADSHEET_HEADER_MONTH_CELL),
            ],
            [
                'display' => 'The format of the outputted date string on the header cell',
                'type' => 'text',
                'name' => AppSetting::SPREADSHEET_HEADER_MONTH_FORMAT,
                'value' => AppSettingRepository::get(AppSetting::SPREADSHEET_HEADER_MONTH_FORMAT),
            ],
            [
                'display' => 'Initial Column',
                'type' => 'text',
                'name' => AppSetting::SPREADSHEET_INITIAL_COLUMN,
                'value' => AppSettingRepository::get(AppSetting::SPREADSHEET_INITIAL_COLUMN),
            ],
            [
                'display' => 'Initial Row',
                'type' => 'text',
                'name' => AppSetting::SPREADSHEET_INITIAL_ROW,
                'value' => AppSettingRepository::get(AppSetting::SPREADSHEET_INITIAL_ROW),
            ],
            [
                'display' => 'Spreadsheet Recipients (Emails)',
                'type' => 'text',
                'name' => AppSetting::SPREADSHEET_GENERATION_EMAILS_REAL_RECIPIENTS,
                'value' => AppSettingRepository::get(AppSetting::SPREADSHEET_GENERATION_EMAILS_REAL_RECIPIENTS),
            ],
            [
                'display' => 'Target Hours for Spreadsheet Generation',
                'type' => 'text',
                'name' => AppSetting::SPREADSHEET_GENERATION_TARGET_HOURS,
                'value' => AppSettingRepository::get(AppSetting::SPREADSHEET_GENERATION_TARGET_HOURS),
            ],
            [
                'display' => 'Target Hours Spreadsheet Recipients (Emails)',
                'type' => 'text',
                'name' => AppSetting::SPREADSHEET_GENERATION_EMAILS_TARGET_RECIPIENTS,
                'value' => AppSettingRepository::get(AppSetting::SPREADSHEET_GENERATION_EMAILS_TARGET_RECIPIENTS),
            ],
            [
                'display' => 'Target Number of Hours to Work per Day',
                'type' => 'text',
                'name' => AppSetting::TARGET_HOURS_DAY,
                'value' => AppSettingRepository::get(AppSetting::TARGET_HOURS_DAY),
            ],
            [
                'display' => 'Expand Lunch Time to Minimium',
                'type' => 'text',
                'name' => AppSetting::TARGET_LUNCH_TIME,
                'value' => AppSettingRepository::get(AppSetting::TARGET_LUNCH_TIME),
            ],
        ];

        return view('settings', compact('inputs'));
    }

    public function save(Request $request)
    {
        $request->validate([
            AppSetting::SPREADSHEET_CURRENT_TEMPLATE_FILENAME => 'nullable|file|mimes:csv,xls,xlsx',
            AppSetting::SPREADSHEET_INITIAL_ROW => 'required|min:1|max:99999|integer',
            AppSetting::SPREADSHEET_INITIAL_COLUMN => 'required|min:1|max:2|alpha',
            AppSetting::SPREADSHEET_HEADER_MONTH_CELL => 'required|min:2|max:3|alpha_num',
            AppSetting::SPREADSHEET_GENERATION_EMAILS_REAL_RECIPIENTS => 'present|email_list',
            AppSetting::SPREADSHEET_GENERATION_EMAILS_TARGET_RECIPIENTS => 'present|email_list',
            AppSetting::SPREADSHEET_GENERATION_TARGET_HOURS => 'present|nullable|integer',
            AppSetting::SPREADSHEET_HEADER_MONTH_FORMAT => 'present|nullable',
            AppSetting::SPREADSHEET_HEADER_PERSON_NAME => 'required|min:2|max:3|alpha_num',
            AppSetting::TARGET_HOURS_DAY => 'present|nullable|min:0|max:24|integer',
            AppSetting::TARGET_LUNCH_TIME => 'present|nullable|min:0|max:240|integer',
        ]);

        if ($request->hasFile(AppSetting::SPREADSHEET_CURRENT_TEMPLATE_FILENAME)) {
            $file = $request->file(AppSetting::SPREADSHEET_CURRENT_TEMPLATE_FILENAME);
            if ($file->getError() !== UPLOAD_ERR_OK) {
                return Redirect::back()->withErrors([$file->getErrorMessage()]);
            }

            $fileSetting = AppSettingRepository::get(AppSetting::SPREADSHEET_CURRENT_TEMPLATE_FILENAME);
            if ($fileSetting && Storage::disk('local')->exists('template.old')) {
                Storage::disk('local')->delete('template.old');
            }
            if ($fileSetting && Storage::disk('local')->exists($fileSetting)) {
                Storage::disk('local')->move($fileSetting, 'template.old');
            }
            AppSettingRepository::set(AppSetting::SPREADSHEET_CURRENT_TEMPLATE_FILENAME, $file->getClientOriginalName());
            $file->storeAs('/', $file->getClientOriginalName());
        }

        foreach ($request->only([
            AppSetting::SPREADSHEET_INITIAL_ROW,
            AppSetting::SPREADSHEET_INITIAL_COLUMN,
            AppSetting::SPREADSHEET_GENERATION_EMAILS_REAL_RECIPIENTS,
            AppSetting::SPREADSHEET_GENERATION_EMAILS_TARGET_RECIPIENTS,
            AppSetting::SPREADSHEET_GENERATION_TARGET_HOURS,
            AppSetting::SPREADSHEET_HEADER_MONTH_CELL,
            AppSetting::SPREADSHEET_HEADER_MONTH_FORMAT,
            AppSetting::SPREADSHEET_HEADER_PERSON_NAME,
            AppSetting::TARGET_HOURS_DAY,
            AppSetting::TARGET_LUNCH_TIME,
        ]) as $name => $value) {
            AppSettingRepository::set($name, $value);
        }

        return Redirect::back();
    }
}
