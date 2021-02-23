<?php

namespace App\Http\Controllers;

use App\Timestamp;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Repositories\TimestampRepository;

class HomeController extends Controller
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
        $user = Auth::user();

        if ($user) {
            return redirect('login');
        }

        return redirect('home');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function dashboard()
    {
        $today = Carbon::now(getenv('TZ') ?: null);
        $lastEnteredString = 'Never';
        $lastExitedString = 'Never';

        $lastEntered = TimestampRepository::lastByUser(Auth::user(), true);
        $lastExited = TimestampRepository::lastByUser(Auth::user(), false);

        if ($lastEntered) {
            $lastEnteredString = $lastEntered->carbon->calendar();
        }

        if ($lastExited) {
            $lastExitedString = $lastExited->carbon->calendar();
        }

        return view('home', compact('today', 'lastEnteredString', 'lastExitedString'));
    }
}
