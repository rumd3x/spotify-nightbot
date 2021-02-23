<?php

namespace App\Repositories;

use App\AppSetting;

final class AppSettingRepository
{
    /**
     * Retrieve App Setting by its name
     *
     * @param string $name
     * @return string|null
     */
    public static function get(string $name)
    {
        $setting = AppSetting::where('name', $name)->first();

        if (!$setting) {
            return null;
        }

        return (string) $setting->value;
    }

    /**
     * Sets the App Setting with the given key to the given value
     *
     * @param string $name
     * @param string $value
     * @return void
     */
    public static function set(string $name, string $value)
    {
        $current = self::get($name);

        if ($current === null) {
            AppSetting::create([
                'name' => $name,
                'value' => $value,
            ]);

            return;
        }

        if ($current === $value) {
            return;
        }

        AppSetting::where('name', $name)->update(['value' => $value]);
    }
}
