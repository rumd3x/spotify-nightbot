<?php

namespace App\Http\Controllers;

use App\Repositories\PlaybackSummaryRepository;
use App\Repositories\PreferenceRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use App\Repositories\UserRepository;
use App\Repositories\WidgetRepository;

class WidgetController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth')->except(['box', 'raw']);
    }

    public function index()
    {
        $fonts = [
            ['value' => "Arial, sans-serif", 'name' => "Arial"],
            ['value' => "Helvetica, sans-serif", 'name' => "Helvetica"],
            ['value' => "Gill Sans, sans-serif", 'name' => "Gill Sans"],
            ['value' => "Lucida, sans-serif", 'name' => "Lucida"],
            ['value' => "Helvetica, sans-serif", 'name' => "Helvetica Narrow"],
            ['value' => "Times, serif", 'name' => "Times"],
            ['value' => "Times New Roman, serif", 'name' => "Times New Roman"],
            ['value' => "Palatino, serif", 'name' => "Palatino"],
            ['value' => "Bookman, serif", 'name' => "Bookman"],
            ['value' => "New Century Schoolbook, serif", 'name' => "New Century Schoolbook"],
            ['value' => "Andale Mono, monospace", 'name' => "Andale Mono"],
            ['value' => "Courier New, monospace", 'name' => "Courier New"],
            ['value' => "Courier, monospace", 'name' => "Courier"],
            ['value' => "Lucidatypewriter, monospace", 'name' => "Lucida Typewriter"],
            ['value' => "Fixed, monospace", 'name' => "Fixed"],
            ['value' => "Comic Sans, Comic Sans MS, cursive", 'name' => "Comic Sans"],
            ['value' => "Zapf Chancery, cursive", 'name' => "Zapf Chancery"],
            ['value' => "Coronetscript, cursive", 'name' => "Coronetscript"],
            ['value' => "Florence, cursive", 'name' => "Florence"],
            ['value' => "Parkavenue, cursive", 'name' => "Parkavenue"],
            ['value' => "Impact, fantasy", 'name' => "Impact"],
            ['value' => "Arnoldboecklin, fantasy", 'name' => "Arnoldboecklin"],
            ['value' => "Oldtown, fantasy", 'name' => "Oldtown"],
            ['value' => "Blippo, fantasy", 'name' => "Blippo"],
            ['value' => "Brushstroke, fantasy", 'name' => "Brushstroke"],
        ];

        $inTransitions = ['none', 'backInDown','backInLeft','backInRight','backInUp','bounceIn','bounceInDown','bounceInLeft','bounceInRight','bounceInUp','fadeIn','fadeInDown','fadeInDownBig','fadeInLeft','fadeInLeftBig','fadeInRight','fadeInRightBig','fadeInUp','fadeInUpBig','fadeInTopLeft','fadeInTopRight','fadeInBottomLeft','fadeInBottomRight','flipInX','flipInY','lightSpeedInRight','lightSpeedInLeft','rotateIn','rotateInDownLeft','rotateInDownRight','rotateInUpLeft','rotateInUpRight','zoomIn','zoomInDown','zoomInLeft','zoomInRight','zoomInUp','slideInDown','slideInLeft','slideInRight','slideInUp','jackInTheBox','rollIn','hinge'];
        $outTransitions = ['none', 'backOutDown','backOutLeft','backOutRight','backOutUp','bounceOut','bounceOutDown','bounceOutLeft','bounceOutRight','bounceOutUp','fadeOut','fadeOutDown','fadeOutDownBig','fadeOutLeft','fadeOutLeftBig','fadeOutRight','fadeOutRightBig','fadeOutUp','fadeOutUpBig','fadeOutTopLeft','fadeOutTopRight','fadeOutBottomRight','fadeOutBottomLeft','flipOutX','flipOutY','lightSpeedOutRight','lightSpeedOutLeft','rotateOut','rotateOutDownLeft','rotateOutDownRight','rotateOutUpLeft','rotateOutUpRight','slideOutDown','slideOutLeft','slideOutRight','slideOutUp','zoomOut','zoomOutDown','zoomOutLeft','zoomOutRight','zoomOutUp','jackInTheBox','rollOut','hinge'];
        $sizes = [12,14,16,20,24,28,32,36,48,72];

        return view('widget', [
            'widget' => WidgetRepository::getByUserId(Auth::user()->id),
            'fonts' => $fonts,
            'sizes' => $sizes,
            'inTransitions' => $inTransitions,
            'outTransitions' => $outTransitions,
        ]);
    }

    public function edit(Request $request)
    {
        $request->validate([
            'backgroundColor' => ['required', 'regex:/^(#(?:[0-9a-f]{2}){2,4}|#[0-9a-f]{3}|(?:rgba?|hsla?)\((?:\d+%?(?:deg|rad|grad|turn)?(?:,|\s)+){2,3}[\s\/]*[\d\.]+%?\))$/i'],
            'textColor' => ['required', 'regex:/^(#(?:[0-9a-f]{2}){2,4}|#[0-9a-f]{3}|(?:rgba?|hsla?)\((?:\d+%?(?:deg|rad|grad|turn)?(?:,|\s)+){2,3}[\s\/]*[\d\.]+%?\))$/i'],
            'fontFamily' => 'required|string',
            'fontSize' => 'required|int|min:5|max:100',
            'inTransition' => 'required|string',
            'outTransition' => 'required|string',
        ]);

        $success = WidgetRepository::edit(
            Auth::user()->id,
            $request->input('backgroundColor') ?: '',
            $request->input('textColor'),
            $request->input('fontFamily'),
            $request->input('fontSize'),
            $request->input('inTransition'),
            $request->input('outTransition')
        );

        $success = true;

        if (!$success){
            return back()->with('info', 'Failed to edit widget customization.');
        }

        return back()->with('info', 'Widget customizations edited successfully.');
    }

    public function box(string $code) {
        $widget = WidgetRepository::getByCode($code);

        if (!$widget) {
            return abort(404);
        }

        $user = UserRepository::findUserByIdForWidgetBox($widget->user_id);

        $fontPieces = explode(',', $user->widget->font_family);
        foreach ($fontPieces as $k => $fontPart) {
            $fontPart = trim($fontPart);
            $fontPieces[$k] = (strpos($fontPart, ' ') !== false ? "\"{$fontPart}\"" : $fontPart);
        }
        $font = implode(', ', $fontPieces);

        return view('widget.box', ['font' => $font, 'widget' => $user->widget]);
    }

    public function raw(string $code)
    {
        $widget = WidgetRepository::getByCode($code);

        if (!$widget) {
            return abort(404);
        }

        $user = UserRepository::findUserByIdForWidgetBox($widget->user_id);
        if ($user->summary->playback_status !== "Playing") {
            return "";
        }

        $text = "{$user->summary->song} - {$user->summary->artist}";
        if ($user->preferences->artist_song_order === "artistNamePreceding") {
            $text = "{$user->summary->artist} - {$user->summary->song}";
        }
        
        if (!empty($user->preferences->preceding_label)) {
            $text = "{$user->preferences->preceding_label} {$text}";
        }      

        return $text;
    }
}
