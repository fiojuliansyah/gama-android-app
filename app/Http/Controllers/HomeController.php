<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Leave;
use App\Models\Permit;
use App\Models\Overtime;
use App\Models\Schedule;
use App\Models\Attendance;
use App\Models\TaskPlanner;
use Illuminate\Http\Request;
use App\Models\GeneratePayroll;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
    
        $currentDate = Carbon::now()->toDateString();
        $yesterdayDate = Carbon::yesterday()->toDateString();
        
        $currentMonthStart = Carbon::now()->startOfMonth()->toDateString();
        $currentMonthEnd = Carbon::now()->endOfMonth()->toDateString();
    
        $attendanceCount = Attendance::where('user_id', $userId)
                        ->whereDate('date', '>=', $currentMonthStart)
                        ->whereDate('date', '<=', $currentMonthEnd)
                        ->count();

        $overtimeCount = Attendance::where('type', 'overtime')
                        ->where('user_id', $userId)
                        ->whereDate('date', '>=', $currentMonthStart)
                        ->whereDate('date', '<=', $currentMonthEnd)
                        ->count();
    
        $lateCount = Attendance::where('type', 'late')
                    ->where('user_id', $userId)
                    ->whereDate('date', '>=', $currentMonthStart)
                    ->whereDate('date', '<=', $currentMonthEnd)
                    ->count();
    
        $alphaCount = Attendance::where('type', 'alpha')
                    ->where('user_id', $userId)
                    ->whereDate('date', '>=', $currentMonthStart)
                    ->whereDate('date', '<=', $currentMonthEnd)
                    ->count();

        $permitCount = Permit::where('user_id', $userId)
                    ->whereDate('created_at', '>=', $currentMonthStart)
                    ->whereDate('created_at', '<=', $currentMonthEnd)
                    ->count();

        $leaveCount = Leave::where('user_id', $userId)
                    ->whereDate('created_at', '>=', $currentMonthStart)
                    ->whereDate('created_at', '<=', $currentMonthEnd)
                    ->count();
        
        $schedule = Schedule::where('user_id', $userId)
                            ->where('date', $currentDate)
                            ->first();
    
        $latestClockIn = Attendance::where('user_id', $userId)
                        ->whereDate('date', $currentDate)
                        ->whereNotNull('clock_in')
                        ->exists();
    
        $latestAttendance = Attendance::where('user_id', $userId)
                        ->where(function ($query) use ($currentDate) {
                            $query->whereDate('date', $currentDate)
                                  ->orWhereNull('clock_out');
                        })
                        ->latest()
                        ->first();
    
        $yesterdayAttendance = Attendance::where('user_id', $userId)
                        ->whereDate('date', $yesterdayDate)
                        ->latest()
                        ->first();
    
        $currentMonthName = Carbon::now()->format('F Y');

        $tasks = TaskPlanner::whereDate('date', Carbon::now()->toDateString())->get();
    
        return view('home', compact(
            'attendanceCount', 
            'overtimeCount', 
            'lateCount', 
            'alphaCount',
            'permitCount',
            'leaveCount', 
            'schedule', 
            'latestClockIn', 
            'latestAttendance', 
            'yesterdayAttendance',
            'currentMonthName',
            'tasks'
        ));
    }

    public function setting()
    {
        return view('setting');
    }

    public function getStarted()
    {
        return view('walkthrough');
    }

    public function download()
    {
        return view('download');
    }

    public function logs(Request $request)
    {
        $userId = Auth::id();

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = Attendance::where('user_id', $userId)->whereNotNull('clock_in')->orderBy('created_at', 'DESC');

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        $logs = $query->paginate(5);

        return view('attendances.logs', compact('logs'));
    }

    public function payslip()
    {
        $payroll = GeneratePayroll::where('user_id', Auth::user()->id)
                                ->latest()
                                ->first();
        
        return view('payslip.index', compact('payroll'));
    }
}
