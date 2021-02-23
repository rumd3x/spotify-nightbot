<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AppSetting extends Model
{
    const SPREADSHEET_CURRENT_TEMPLATE_FILENAME = 'spreadsheet_current_template_filename';
    const SPREADSHEET_INITIAL_COLUMN = 'spreadsheet_initial_column';
    const SPREADSHEET_INITIAL_ROW = 'spreadsheet_initial_row';
    const SPREADSHEET_GENERATION_EMAILS_REAL_RECIPIENTS = 'spreadsheet_generation_target_recipients';
    const SPREADSHEET_GENERATION_EMAILS_TARGET_RECIPIENTS = 'spreadsheet_generation_real_recipients';
    const SPREADSHEET_GENERATION_TARGET_HOURS = 'spreadsheet_generation_target_hours';
    const SPREADSHEET_HEADER_MONTH_CELL = 'spreadsheet_header_month_cell';
    const SPREADSHEET_HEADER_MONTH_FORMAT = 'spreadsheet_header_month_format';
    const SPREADSHEET_HEADER_PERSON_NAME = 'spreadsheet_person_name_header';
    const TARGET_HOURS_DAY = 'hours_day_target';
    const TARGET_LUNCH_TIME = 'minutes_lunch_target';

    protected $fillable = [
        'name',
        'value',
    ];
}
