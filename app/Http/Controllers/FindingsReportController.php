<?php

namespace App\Http\Controllers;

use App\Models\FindingsReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FindingsReportController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $title = 'Findings Reports';
        $reports = FindingsReport::where('user_id', $userId)
                                    ->with('site')
                                    ->latest()
                                    ->get();
        return view('findings-reports.index', compact('reports', 'title'));
    }

    public function create()
    {
        return view('findings-reports.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'date' => 'required|date',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'direct_action' => 'required|string|max:255',
            'status' => 'required|in:pending,solved',
            'type' => 'required|in:low,medium,high',
            'image' => 'nullable|image|max:4096',
            'site_id' => 'nullable|exists:sites,id',
            'is_work_assignments' => 'nullable|boolean'
        ]);

        $imgUrl = null;

        if ($request->hasFile('image')) {
            $storageOption = $request->input('storage_option', 'local');

            if ($storageOption === 's3') {
                $path = $request->file('image')->store('findings-reports', 's3');
                $imgUrl = Storage::disk('s3')->url($path);
            } else {
                $path = $request->file('image')->store('findings-reports', 'public');
                $imgUrl = asset("storage/{$path}");
            }
        }

        FindingsReport::create([
            'user_id' => Auth::id(),
            'site_id' => Auth::user()->site_id,
            'title' => $request->title,
            'date' => $request->date,
            'description' => $request->description,
            'location' => $request->location,
            'direct_action' => $request->direct_action,
            'status' => $request->status,
            'type' => $request->type,
            'is_work_assignments' => $request->is_work_assignments ?? false,
            'image_url' => $imgUrl,
        ]);

        return redirect()->route('findings-reports.index')
                         ->with('success', 'Report created successfully.');
    }

    public function show($id)
    {
        $findingsReport = FindingsReport::with(['user', 'site'])->find($id);

        if(!$findingsReport) {
            abort(404, 'FindingsReport not found');
        }

        return view('findings-reports.show', compact('findingsReport'));
    }

    public function edit(FindingsReport $findingsReport)
    {
        return view('findings-reports.edit', compact('findingsReport'));
    }

    public function update(Request $request, FindingsReport $findingsReport)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'date' => 'required|date',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'direct_action' => 'required|string|max:255',
            'status' => 'required|in:pending,solved',
            'type' => 'required|in:low,medium,high',
            'image' => 'nullable|image|max:4096',
            'site_id' => 'nullable|exists:sites,id',
            'is_work_assignments' => 'nullable|boolean'
        ]);

        $data = $request->only([
            'title', 'date', 'description', 'location',
            'direct_action', 'status', 'type', 'is_work_assignments', 'site_id'
        ]);

        if ($request->hasFile('image')) {
            // hapus gambar lama
            if ($findingsReport->image_url) {
                $oldPath = str_replace(asset('storage/'), '', $findingsReport->image_url);
                Storage::disk('public')->delete($oldPath);
            }

            $storageOption = $request->input('storage_option', 'local');

            if ($storageOption === 's3') {
                $path = $request->file('image')->store('findings-reports', 's3');
                $data['image_url'] = Storage::disk('s3')->url($path);
            } else {
                $path = $request->file('image')->store('findings-reports', 'public');
                $data['image_url'] = asset("storage/{$path}");
            }
        }

        $findingsReport->update($data);

        return redirect()->route('findings-reports.index')
                         ->with('success', 'Report updated successfully.');
    }

    public function destroy(FindingsReport $findingsReport)
    {
        if ($findingsReport->image_url) {
            $oldPath = str_replace(asset('storage/'), '', $findingsReport->image_url);
            Storage::disk('public')->delete($oldPath);
        }

        $findingsReport->delete();

        return redirect()->route('findings-reports.index')
                         ->with('success', 'Report deleted successfully.');
    }
}
