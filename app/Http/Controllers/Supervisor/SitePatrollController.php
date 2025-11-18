<?php

namespace App\Http\Controllers\Supervisor;

use Carbon\Carbon;
use App\Models\Site;
use App\Models\Floor;
use Illuminate\Http\Request;
use App\Models\PatrollSession;
use App\Models\SecurityPatroll;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class SitePatrollController extends Controller
{
    public function index()
    {
        $currentUser = Auth::user();
        $sites = $currentUser->sites_leader;
        
        return view('supervisor.site-patroll.index', compact('sites'));
    }

    public function show($siteId, Request $request)
    {
        $user = Auth::user();
        $site = Site::findOrFail($siteId);
        $floors = Floor::where('site_id', $siteId)->get();

        $filterDate = $request->date ? Carbon::parse($request->date) : Carbon::today();

        $sessions = PatrollSession::where('user_id', $user->id)
            ->whereDate('date', $filterDate)
            ->where('site_id', $siteId)
            ->orderBy('id', 'desc')
            ->get();

        if ($request->session_id) {
            $session = PatrollSession::where('id', $request->session_id)
                ->whereDate('date', $filterDate)
                ->where('site_id', $siteId)
                ->where('user_id', $user->id)
                ->first();
        } else {
            $session = $sessions->first();
        }

        $sessionToday = false;
        if ($session) {
            $sessionToday = Carbon::parse($session->date)->toDateString() === $filterDate->toDateString();
        }

        $patrolledFloors = [];
        if ($session) {
            $patrolledFloors = SecurityPatroll::where('patroll_session_id', $session->id)
                ->pluck('floor_id')
                ->toArray();
        }

        return view('supervisor.site-patroll.show', compact(
            'floors',
            'site',
            'session',
            'sessions',
            'patrolledFloors',
            'sessionToday',
            'filterDate'
        ));
    }
}
