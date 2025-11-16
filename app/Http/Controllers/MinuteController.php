<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MinuteController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $title = 'Berita Acara';
        $minutes = Attendance::where('type', 'minutes')
                                ->where('user_id', $userId)
                                ->get();
        return view('minutes.index', compact('minutes', 'title'));
    }

    public function create()
    {
        return view('minutes.create');
    }

    public function minute(Request $request)
    {
        $user = Auth::user();
        $imgUrl = null;

        if ($request->hasFile('image')) {
            $storageOption = $request->input('storage_option', 'local');

            if ($storageOption === 's3') {
                $path = $request->file('image')->store('minutes_images', 's3');
                $imgUrl = Storage::disk('s3')->url($path);
            } else {
                $path = $request->file('image')->store('minutes_images', 'public');
                $imgUrl = asset("storage/{$path}");
            }
        }

        $lastAttendance = Attendance::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->first();

        if ($request->type === 'clockin') {
            $attendance = new Attendance;
            $attendance->date = $request->date;
            $attendance->latlong = $request->latlong;
            $attendance->user_id = $user->id;
            $attendance->site_id = $user->site_id;
            $attendance->clock_in = $request->clock;
            $attendance->type = 'minutes';
            $attendance->face_image_url_clockin = $imgUrl;
            $attendance->remark = $request->remark;
            $attendance->save();
        } elseif ($request->type === 'clockout') {
            if ($lastAttendance) {
                $lastAttendance->clock_out = $request->clock;
                $lastAttendance->face_image_url_clockout = $imgUrl;
                $lastAttendance->remark = $request->remark;
                $lastAttendance->save();
            } else {
                return redirect()->route('minute.index')
                                 ->with('error', 'No clock-in record found. Please clock in first.');
            }
        }

        return redirect()->route('minute.index')
                         ->with('success', 'Minute recorded successfully.');
    }

    public function show($id)
    {
        $minute = Attendance::find($id);
        return view('minutes.show', compact('minute'));
    }
}
