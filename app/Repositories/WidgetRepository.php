<?php

namespace App\Repositories;

use App\Widget;
use App\User;

final class WidgetRepository
{

    /**
     * Undocumented function
     *
     * @param integer $userId
     * @return Widget
     */
    public static function empty(int $userId)
    {
        Widget::updateOrCreate(
            ['user_id' => $userId], 
            [
                'code' => uniqid(rand()),
                'font_family' => 'Arial, sans-serif', 
                'font_size' => '24',
                'text_color' => '#000000',
                'background_color' => '#00ff00',
                'transition_in' => 'none',
                'transition_out' => 'none',
            ]
        );
    }

    /**
     * Undocumented function
     *
     * @param integer $userId
     * @return Widget|null
     */
    public static function getByUserId(int $userId)
    {
        return Widget::whereUserId($userId)->first();
    }

    /**
     * Undocumented function
     *
     * @param string $code
     * @return Widget|null
     */
    public static function getByCode(string $code)
    {
        return Widget::whereCode($code)->first();
    }

    /**
     * Undocumented function
     *
     * @param integer $userId
     * @param string $backgroundColor
     * @param string $textColor
     * @param string $fontFamily
     * @param string $fontSize
     * @return void
     */
    public static function edit(
        int $userId,
        string $backgroundColor,
        string $textColor,
        string $fontFamily,
        string $fontSize,
        string $transitionIn,
        string $transitionOut
    ) {
        return Widget::whereUserId($userId)->update([
            'font_family' => $fontFamily, 
            'font_size' => $fontSize,
            'text_color' => $textColor,
            'background_color' => $backgroundColor,
            'transition_in' => $transitionIn,
            'transition_out' => $transitionOut,
        ]);
    }
}
