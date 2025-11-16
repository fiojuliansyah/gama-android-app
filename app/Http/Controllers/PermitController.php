<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Permit;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PermitController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $title = 'Cuti';
        $permits = Permit::where('user_id', $user->id)->get();
        return view('permits.index', compact('permits', 'title'));
    }

    public function create()
    {
        $user = Auth::user();
        return view('permits.create', compact('user'));
    }

    public function createPermit($slug)
    {
        $user = Auth::user();
        return view('permits.parts.permit', compact('user'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $imgUrl = null;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $storageOption = $request->input('storage_option', 'local');

            if ($storageOption === 's3') {
                $path = $image->store('permits', 's3');
                $imgUrl = Storage::disk('s3')->url($path);
            } else {
                $path = $image->store('permits', 'public');
                $imgUrl = asset("storage/{$path}");
            }
        }

        $attendance = Attendance::create([
            'date' => $request->start_date,
            'user_id' => $user->id,
            'site_id' => $user->site_id,
        ]);

        Permit::create([
            'user_id' => $user->id,
            'site_id' => $user->site_id,
            'title' => $request->title,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'image_url' => $imgUrl,
            'reason' => $request->reason,
            'contact' => $request->contact,
            'attendance_id' => $attendance->id,
        ]);

        return redirect()->route('permit.index')
            ->with('success', 'Pengajuan permohonan cuti berhasil diajukan');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'image' => 'nullable|image|max:2048',
        ]);

        $permit = Permit::findOrFail($id);

        $permit->update($request->only([
            'title',
            'start_date',
            'end_date',
            'reason',
            'contact'
        ]));

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $storageOption = $request->input('storage_option', 'local');

            if ($storageOption === 's3') {
                $path = $image->store('permits', 's3');
                $permit->image_url = Storage::disk('s3')->url($path);
            } else {
                $path = $image->store('permits', 'public');
                $permit->image_url = asset("storage/{$path}");
            }
        }

        $permit->save();

        return redirect()->route('home')
            ->with('success', 'Permit successfully updated.');
    }

    public function show($id)
    {
        $permit = Permit::find($id);
        return view('permits.show', compact('permit'));
    }
}
