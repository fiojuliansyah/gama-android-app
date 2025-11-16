<?php

namespace App\Http\Controllers;

use App\Models\Floor;
use Illuminate\Http\Request;
use App\Models\PatrollSession;
use App\Models\SecurityPatroll;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

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

        $sessionToday = PatrollSession::where('user_id', $user->id)
            ->whereDate('date', today())
            ->first();

        if ($sessionToday) {
            return redirect()->route('patroll.scan')
                ->with('info', 'Sesi patroli sudah dimulai hari ini.');
        }

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


    public function store(Request $request)
    {
        $user = Auth::user();

        // Ambil session terbaru
        $session = PatrollSession::where('user_id', $user->id)
            ->orderBy('id', 'desc')
            ->first();

        if (!$session) {
            return redirect()->route('patroll.scan')->with('error', 'Session tidak ditemukan.');
        }

        // Validasi QR Code
        if ($request->code !== "FLOOR-" . $request->floor_id) {
            return redirect()->back()->with('error', 'QR Code tidak valid!');
        }

        // Cek apakah lantai sudah discan
        $already = SecurityPatroll::where('patroll_session_id', $session->id)
            ->where('floor_id', $request->floor_id)
            ->first();

        if ($already) {
            return redirect()->route('patroll.scan')
                ->with('warning', 'Lantai ini sudah dipatroli.');
        }

        // Simpan patroli lantai
        SecurityPatroll::create([
            'patroll_session_id' => $session->id,
            'floor_id'           => $request->floor_id,
            'user_id'            => $user->id,
            'time'               => now(),
            'qr_code'            => $request->code
        ]);

        return redirect()->route('patroll.scan')->with('success', 'Patroli lantai berhasil dicatat!');
    }

    public function endSession($id)
    {
        $session = PatrollSession::findOrFail($id);

        $session->end_time = now();
        $session->save();

        return redirect()->back()->with('info', 'Sesi patroli telah diakhiri.');
    }

}
