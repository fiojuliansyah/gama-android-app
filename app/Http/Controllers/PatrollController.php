<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Floor;
use App\Models\Jobdesk;
use App\Models\TaskPlanner;
use App\Models\TaskProgress;
use Illuminate\Http\Request;
use App\Models\PatrollSession;
use App\Models\SecurityPatroll;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PatrollController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $siteId = $user->site_id;

        $floors = Floor::where('site_id', $siteId)->get();

        $sessions = PatrollSession::where('user_id', $user->id)
            ->orderBy('id', 'desc')
            ->get();

        $session = null;

        // Jika pilih session dari dropdown
        if ($request->session_id) {
            $session = PatrollSession::where('id', $request->session_id)
                ->where('user_id', $user->id)
                ->first();
        } else {
            // Default ambil session terakhir
            $session = $sessions->first();
        }

        // Cek apakah session hari ini
        $sessionToday = false;
        if ($session) {
            $sessionToday = Carbon::parse($session->date)->isToday();
        }

        // List lantai yang sudah discan
        $patrolledFloors = [];
        if ($session && $sessionToday) {
            $patrolledFloors = SecurityPatroll::where('patroll_session_id', $session->id)
                ->pluck('floor_id')
                ->toArray();
        }

        return view('security-patrolls.index', compact(
            'floors',
            'session',
            'sessions',
            'patrolledFloors',
            'sessionToday'
        ));
    }

    public function scan()
    {
        $user = Auth::user();

        $sessionToday = PatrollSession::where('user_id', $user->id)
            ->whereDate('date', today())
            ->whereNull('end_time')
            ->orderBy('id', 'desc')
            ->first();

        return view('security-patrolls.scan', compact('sessionToday'));
    }

    public function startSession()
    {
        $user = Auth::user();

        $lastSession = PatrollSession::where('user_id', $user->id)
                        ->whereDate('date', today())
                        ->latest('turn')
                        ->first();

        $turn = $lastSession ? $lastSession->turn + 1 : 1;

        $session = PatrollSession::create([
            'user_id'       => $user->id,
            'site_id'       => $user->site_id,
            'patroll_code'  => 'PAT-' . strtoupper(uniqid()),
            'date'          => today(),
            'start_time'    => now(),
            'turn'          => $turn
        ]);

        return redirect()->route('patroll.scan')
            ->with('success', 'Sesi patroli dimulai. Turn ke-' . $turn);
    }

    public function endSession($id)
    {
        $session = PatrollSession::findOrFail($id);

        $session->end_time = now();
        $session->save();

        return redirect()->back()->with('info', 'Sesi patroli telah diakhiri.');
    }

    public function detailFloor($id)
    {
        $floor = Floor::findOrFail($id);
        $taskPlanners = TaskPlanner::where('floor_id', $id)->get();

        $currentSession = auth()->user()->patrollSessions()
                                ->whereNull('end_time')
                                ->latest()
                                ->first();

        return view('security-patrolls.detail-floor', compact('floor', 'taskPlanners', 'currentSession'));
    }

    public function taskUpdate(Request $request, TaskPlanner $task)
    {
        $request->validate([
            'progress_description' => 'nullable|string',
            'image_base64' => 'nullable|string',
            'patroll_session_id' => 'required|exists:patroll_sessions,id'
        ]);

        $data = [
            'user_id' => Auth::id(),
            'floor_id' => $task->floor_id,
            'site_id' => $task->site_id,
            'task_planner_id' => $task->id,
            'name' => $task->name,
            'description' => $request->progress_description,
            'status' => 'reported',
            'patroll_session_id' => $request->patroll_session_id,
        ];

        if ($request->image_base64) {
            $image = $request->image_base64;
            $image = str_replace('data:image/jpeg;base64,', '', $image);
            $image = str_replace(' ', '+', $image);

            $imageName = 'patrol_' . time() . '.jpg';
            Storage::disk('public')->put('security_patroll/' . $imageName, base64_decode($image));

            $data['image_url'] = asset('storage/security_patroll/' . $imageName);
        }

        SecurityPatroll::updateOrCreate(
            [
                'task_planner_id' => $task->id,
                'user_id' => Auth::id(),
                'patroll_session_id' => $request->patroll_session_id,
            ],
            $data
        );

        return back()->with('success', 'Task progress updated successfully.');
    }

}
