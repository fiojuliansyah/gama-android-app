<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Floor;
use App\Models\Jobdesk;
use App\Models\TaskPlanner;
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

        $session = PatrollSession::create([
            'user_id'       => $user->id,
            'site_id'       => $user->site_id,
            'patroll_code'  => 'PAT-' . strtoupper(uniqid()),
            'date'          => today(),
            'start_time'    => now(),
            'turn'          => 1
        ]);

        return redirect()->route('patroll.scan')
            ->with('success', 'Sesi patroli dimulai.');
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
        return view('security-patrolls.detail-floor', compact('floor', 'taskPlanners'));
    }

    public function update(Request $request, TaskPlanner $task)
    {
        $request->validate([
            'progress_description' => 'nullable|string',
            'image_before' => 'nullable|image|max:2048',
            'image_after' => 'nullable|image|max:2048',
            'status' => 'required|in:pending,in_progress,completed',
            'is_worked' => 'required|in:worked,not_worked',
        ]);

        $data = [
            'site_id' => $task->site_id,
            'status' => $request->status,
            'is_worked' => $request->is_worked,
            'progress_description' => $request->progress_description,
            'date' => now()->format('Y-m-d'),
            'start_time' => $request->start_time ?? now()->format('H:i:s'),
            'end_time' => $request->end_time ?? null,
        ];

        if ($request->hasFile('image_before')) {
            $storageOption = $request->input('storage_option_before', 'local');
            if ($storageOption === 's3') {
                $path = $request->file('image_before')->store('task_progress', 's3');
                $data['image_before_url'] = Storage::disk('s3')->url($path);
            } else {
                $path = $request->file('image_before')->store('task_progress', 'public');
                $data['image_before_url'] = asset("storage/{$path}");
            }
        }

        if ($request->hasFile('image_after')) {
            $storageOption = $request->input('storage_option_after', 'local');
            if ($storageOption === 's3') {
                $path = $request->file('image_after')->store('task_progress', 's3');
                $data['image_after_url'] = Storage::disk('s3')->url($path);
            } else {
                $path = $request->file('image_after')->store('task_progress', 'public');
                $data['image_after_url'] = asset("storage/{$path}");
            }
        }

        TaskProgress::updateOrCreate(
            [
                'task_planner_id' => $task->id,
                'user_id' => Auth::id(),
            ],
            $data
        );

        return back()->with('success', 'Task progress updated successfully.');
    }

}
