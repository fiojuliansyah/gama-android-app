<?php

namespace App\Http\Controllers\Supervisor;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class TeamController extends Controller
{
    public function index(Request $request)
    {
        $currentUser = Auth::user();
        $sites = $currentUser->sites_leader;

        $siteIdsLeadByCurrentUser = $sites->pluck('id')->toArray();
        $query = User::whereIn('site_id', $siteIdsLeadByCurrentUser);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                ->orWhere('email', 'like', "%$search%")
                ->orWhere('phone', 'like', "%$search%");
            });
        }

        if ($request->filled('site_id')) {
            $query->where('site_id', $request->site_id);
        }

        $teams = $query->get();

        return view('supervisor.teams.index', [
            'teams' => $teams,
            'sites' => $sites,
            'currentUser' => $currentUser,
        ]);
    }

    public function show($id)
    {
        $currentUser = Auth::user();
        $sites = $currentUser->sites_leader;
        $siteIds = $sites->pluck('id')->toArray();

        $user = User::where('id', $id)
            ->whereIn('site_id', $siteIds)
            ->firstOrFail();

        return view('supervisor.teams.show', [
            'user' => $user,
            'currentUser' => $currentUser
        ]);
    }

    public function resign(Request $request, User $user)
    {
        $request->validate([
            'resign_date' => 'required|date',
        ]);

        if (!$user->profile) {
            return back()->with('error', 'Profile user tidak ditemukan.');
        }

        $user->profile->update([
            'resign_date' => $request->resign_date
        ]);

        return redirect()->route('supervisor.teams.show', $user->id)
            ->with('success', 'Tanggal resign berhasil diperbarui.');
    }
}
