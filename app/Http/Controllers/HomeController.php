<?php

namespace App\Http\Controllers;

use App\Jobs\UpdateAllUsersSongsJob;
use App\Repositories\NotificationRepository;
use App\Repositories\SongHistoryRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use SpotifyWebAPI\Session as SpotifySession;
use SpotifyWebAPI\SpotifyWebAPI;

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
        return view('dashboard');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function history()
    {
        return view('history', ['history' => SongHistoryRepository::getUserLast50(Auth::user()->id)]);
    }

    public function clearNotifications()
    {
        $notificationIds = NotificationRepository::unreadsByUserId(Auth::user()->id)->pluck('id');
        NotificationRepository::markAsRead($notificationIds->toArray());
        return back()->with('info', 'Notifications cleared!');
    }
}
