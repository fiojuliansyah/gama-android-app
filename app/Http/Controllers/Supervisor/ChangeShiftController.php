<?php

namespace App\Http\Controllers\Supervisor;

use App\Models\User;
use App\Models\Shift;
use App\Models\Schedule;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ChangeShiftController extends Controller
{
    public function index(Request $request)
    {
        $currentUser = Auth::user();

        $sites = $currentUser->sites_leader;
        $siteIds = $sites->pluck('id')->toArray();

        $selectedSiteId = $request->site_id;

        $shifts = Shift::when($selectedSiteId, function($query) use ($selectedSiteId) {
                return $query->where('site_id', $selectedSiteId);
            })
            ->whereIn('site_id', $siteIds)
            ->get();

        return view('supervisor.change-shift.index', compact('shifts', 'sites', 'selectedSiteId'));
    }

    public function show(Request $request, Shift $shift)
    {
        $currentUser = Auth::user();

        if (! $currentUser->sites_leader->pluck('id')->contains($shift->site_id)) {
            abort(403, 'Anda tidak memiliki akses ke shift ini.');
        }

        $date = $request->date;
        $search = $request->search;

        $schedules = Schedule::with('user')
            ->where('shift_id', $shift->id)
            ->where('site_id', $shift->site_id)
            ->when($date, function ($query) use ($date) {
                return $query->where('date', $date);
            })
            ->when($search, function ($query) use ($search) {
                return $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%$search%");
                });
            })
            ->orderBy('date')
            ->get();

        return view('supervisor.change-shift.show', compact('shift', 'schedules', 'date', 'search'));
    }

    public function updateSchedule(Request $request)
    {
        $request->validate([
            'schedule_id' => 'required|exists:schedules,id',
            'shift_id' => 'required|exists:shifts,id'
        ]);

        $schedule = Schedule::findOrFail($request->schedule_id);
        $shift = Shift::findOrFail($request->shift_id);

        $schedule->update([
            'shift_id' => $shift->id,
            'clock_in' => $shift->clock_in,
            'clock_out' => $shift->clock_out,
            'type' => 'move-shift'
        ]);

        return back()->with('success', 'Schedule berhasil dipindah ke shift baru.');
    }
}
