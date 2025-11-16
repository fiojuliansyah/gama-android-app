<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Schedule;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AttendanceController extends Controller
{
    public function index()
    {
        $title = 'Presensi';
        $userId = Auth::id();
        $currentDate = Carbon::now()->toDateString();

        $schedule = Schedule::where('user_id', $userId)->where('date', $currentDate)->first();

        $latestAttendance = Attendance::where('user_id', $userId)
            ->where(function ($query) use ($currentDate) {
                $query->whereDate('date', $currentDate)->orWhereNull('clock_out');
            })
            ->latest()
            ->first();

        $latestClockIn = $latestAttendance && $latestAttendance->clock_in;
        $latestClockOut = $latestAttendance && $latestAttendance->clock_out;

        $logs = Attendance::where('user_id', $userId)->orderBy('date', 'desc')->paginate(1);

        return view('attendances.index', compact('latestClockIn', 'latestClockOut', 'latestAttendance', 'logs', 'title', 'schedule'));
    }

    public function clockinPage()
    {
        $user = Auth::user();
        $today = Carbon::now()->toDateString();

        $existingAttendance = Attendance::where('user_id', $user->id)->whereDate('date', $today)->first();

        if ($existingAttendance && $existingAttendance->clock_in) {
            return redirect()->route('home')->with('warning', 'Anda sudah melakukan clock in hari ini.');
        }

        return view('attendances.clock');
    }

    public function clockoutPage()
    {
        $user = Auth::user();

        $attendance = Attendance::where('user_id', $user->id)->whereNotNull('clock_in')->whereNull('clock_out')->first();

        if (!$attendance) {
            return redirect()->route('home')->with('warning', 'Anda belum melakukan clock in atau sudah melakukan clock out hari ini.');
        }

        return view('attendances.clock');
    }

    public function clockinStore(Request $request)
    {
        $user = Auth::user();
        $dateNow = Carbon::now()->toDateString();
        $timeNow = Carbon::now()->toTimeString();

        $existingAttendance = Attendance::where('user_id', $user->id)->whereDate('date', $dateNow)->first();

        if ($existingAttendance && $existingAttendance->clock_in) {
            return redirect()->route('home')->with('warning', 'Anda sudah melakukan clock in hari ini.');
        }

        $schedule = Schedule::where('user_id', $user->id)->where('date', $dateNow)->first();

        if (!$existingAttendance) {
            $attendance = new Attendance();
            $attendance->date = $dateNow;
            $attendance->latlong = $request->latlong;
            $attendance->user_id = $user->id;
            $attendance->site_id = $user->site_id;
            $attendance->clock_in = $timeNow;
        } else {
            $attendance = $existingAttendance;
            $attendance->clock_in = $timeNow;
        }

        if ($schedule && $schedule->clock_in) {
            $scheduledTime = Carbon::parse($schedule->clock_in);
            $actualTime = Carbon::parse($timeNow);
            if ($actualTime->gt($scheduledTime)) {
                $attendance->type = 'late';
                $attendance->late_duration = $actualTime->diffInMinutes($scheduledTime);
            }
        }

        if ($request->image) {
            try {
                $imageData = $request->input('image');

                if (strpos($imageData, 'data:image') === 0) {
                    $imageData = preg_replace('/^data:image\/\w+;base64,/', '', $imageData);
                    $imageData = base64_decode($imageData);

                    $tempFile = tempnam(sys_get_temp_dir(), 'clockin_');
                    file_put_contents($tempFile, $imageData);

                    $storageOption = $request->input('storage_option', 'local');

                    if ($storageOption === 's3') {
                        $path = Storage::disk('s3')->putFile('attendances_images', $tempFile);
                        $attendance->face_image_url_clockin = Storage::disk('s3')->url($path);
                    } else {
                        $path = Storage::disk('public')->putFile('attendances_images', $tempFile);
                        $attendance->face_image_url_clockin = asset("storage/{$path}");
                    }

                    unlink($tempFile);
                } else {
                    throw new \Exception('Invalid image format received');
                }
            } catch (\Exception $e) {
                Log::error('Clock-in image upload error: ' . $e->getMessage());
                return redirect()->route('home')->with('error', 'Gagal mengunggah gambar. Error: ' . $e->getMessage());
            }
        }

        $attendance->save();

        $successMessage = 'Clock in berhasil pada ' . Carbon::parse($timeNow)->format('H:i');
        if ($attendance->type == 'late') {
            $successMessage .= '. Anda terlambat ' . $attendance->late_duration . ' menit';
        }

        return redirect()->route('home')->with('success', $successMessage);
    }

    public function clockoutStore(Request $request)
    {
        $user = Auth::user();
        $timeNow = Carbon::now()->toTimeString();

        $lastAttendance = Attendance::where('user_id', $user->id)->whereNotNull('clock_in')->whereNull('clock_out')->first();

        if (!$lastAttendance) {
            return redirect()->route('home')->with('warning', 'Anda belum melakukan clock in atau sudah melakukan clock out hari ini.');
        }

        $lastAttendance->clock_out = $timeNow;

        if ($request->image) {
            try {
                $imageData = $request->input('image');

                if (strpos($imageData, 'data:image') === 0) {
                    $imageData = preg_replace('/^data:image\/\w+;base64,/', '', $imageData);
                    $imageData = base64_decode($imageData);

                    $tempFile = tempnam(sys_get_temp_dir(), 'clockout_');
                    file_put_contents($tempFile, $imageData);

                    $storageOption = $request->input('storage_option', 'local');

                    if ($storageOption === 's3') {
                        $path = Storage::disk('s3')->putFile('attendances_images', $tempFile);
                        $lastAttendance->face_image_url_clockout = Storage::disk('s3')->url($path);
                    } else {
                        $path = Storage::disk('public')->putFile('attendances_images', $tempFile);
                        $lastAttendance->face_image_url_clockout = asset("storage/{$path}");
                    }

                    unlink($tempFile);
                } else {
                    throw new \Exception('Invalid image format received');
                }
            } catch (\Exception $e) {
                Log::error('Clock-out image upload error: ' . $e->getMessage());
                return redirect()->route('home')->with('error', 'Gagal mengunggah gambar. Error: ' . $e->getMessage());
            }
        }

        $lastAttendance->save();

        $clockInTime = Carbon::parse($lastAttendance->clock_in);
        $clockOutTime = Carbon::parse($lastAttendance->clock_out);
        $duration = $clockOutTime->diff($clockInTime)->format('%H jam %I menit');

        return redirect()->route('home')->with('success', "Clock out berhasil pada {$clockOutTime->format('H:i')}. Durasi kerja: {$duration}");
    }

    public function getTodayStatus()
    {
        $user = Auth::user();
        $today = Carbon::now()->toDateString();

        $attendance = Attendance::where('user_id', $user->id)->whereDate('date', $today)->first();

        if (!$attendance) {
            return response()->json([
                'status' => 'no_attendance',
                'message' => 'Belum melakukan absensi',
            ]);
        }

        if ($attendance->clock_in && !$attendance->clock_out) {
            $clockInTime = Carbon::parse($attendance->clock_in)->format('H:i');
            return response()->json([
                'status' => 'clocked_in',
                'message' => "Sudah clock in pada {$clockInTime}",
                'clock_in' => $clockInTime,
            ]);
        }

        if ($attendance->clock_in && $attendance->clock_out) {
            $clockInTime = Carbon::parse($attendance->clock_in)->format('H:i');
            $clockOutTime = Carbon::parse($attendance->clock_out)->format('H:i');
            return response()->json([
                'status' => 'completed',
                'message' => 'Absensi selesai',
                'clock_in' => $clockInTime,
                'clock_out' => $clockOutTime,
            ]);
        }

        return response()->json([
            'status' => 'unknown',
            'message' => 'Status tidak diketahui',
        ]);
    }

    public function timeOff(Request $request)
    {
        $user = Auth::user();
        $dateNow = Carbon::now()->toDateString();
        $timeNow = Carbon::now()->toTimeString();

        $attendance = new Attendance();
        $attendance->date = $dateNow;
        $attendance->user_id = $user->id;
        $attendance->site_id = $user->site_id;
        $attendance->clock_in = $timeNow;
        $attendance->clock_out = $timeNow;
        $attendance->type = 'shift_off';
        $attendance->save();

        return redirect()->route('home')->with('success', 'Attendance recorded successfully.');
    }
}
