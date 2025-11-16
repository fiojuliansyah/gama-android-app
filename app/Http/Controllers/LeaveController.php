<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Leave;
use App\Models\TypeLeave;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LeaveController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $title = 'Cuti';
        $leaves = Leave::where('user_id', $user->id)->get();
        return view('leaves.index', compact('leaves', 'title'));
    }

    public function create()
    {
        $user = Auth::user();
        $types = TypeLeave::where('site_id', $user->site_id)->get();
        return view('leaves.create', compact('types'));
    }

    public function createLeave($slug)
    {
        $user = Auth::user();
        $typeLeave = TypeLeave::where('slug', $slug)->firstOrFail();
        return view('leaves.parts.leave', compact('typeLeave', 'user'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $imgUrl = null;

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $storageOption = $request->input('storage_option', 'local');

            if ($storageOption === 's3') {
                $path = Storage::disk('s3')->putFile('leaves_images', $file);
                $imgUrl = Storage::disk('s3')->url($path);
            } else {
                $path = Storage::disk('public')->putFile('leaves_images', $file);
                $imgUrl = asset("storage/{$path}");
            }
        }

        $attendance = Attendance::create([
            'date' => $request->start_date,
            'user_id' => $user->id,
            'site_id' => $user->site_id,
            'type' => 'leave',
        ]);

        Leave::create([
            'user_id' => $user->id,
            'site_id' => $user->site_id,
            'type_id' => $request->type_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'image_url' => $imgUrl,
            'reason' => $request->reason,
            'contact' => $request->contact,
            'attendance_id' => $attendance->id,
        ]);

        return redirect()->route('leave.index')->with('success', 'Pengajuan permohonan cuti berhasil diajukan');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'image' => 'nullable|image|max:2048',
        ]);

        $leave = Leave::findOrFail($id);
        $leave->update($request->only([
            'type_id',
            'start_date',
            'end_date',
            'reason',
            'contact'
        ]));

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $storageOption = $request->input('storage_option', 'local');

            if ($storageOption === 's3') {
                $path = Storage::disk('s3')->putFile('leaves_images', $file);
                $leave->image_url = Storage::disk('s3')->url($path);
            } else {
                $path = Storage::disk('public')->putFile('leaves_images', $file);
                $leave->image_url = asset("storage/{$path}");
            }

            $leave->save();
        }

        return redirect()->route('home')->with('success', 'Leave successfully updated.');
    }

    public function show($id)
    {
        $leave = Leave::find($id);
        return view('leaves.show', compact('leave'));
    }
}
