<?php

namespace App\Http\Controllers;

use App\Models\UserActivity;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
class UserActivityController extends Controller
{
    /**
     * Display a listing of the resource.
     */


    public function index()
    {

        return view('leaderboard.index');
    }
    public function activies_data(Request $request)
    {
        $filter = $request->filter;
        $search = $request->search;

        $query = User::select('users.id', 'users.name', DB::raw('SUM(user_activities.points) as total_points'))
            ->join('user_activities', 'users.id', '=', 'user_activities.user_id')
            ->groupBy('users.id', 'users.name');

        // Filter by day, month, year
        if ($filter === 'day') {
            $query->whereDate('user_activities.created_at', today());
        } elseif ($filter === 'month') {
            $query->whereMonth('user_activities.created_at', now()->month)
                ->whereYear('user_activities.created_at', now()->year);
        } elseif ($filter === 'year') {
            $query->whereYear('user_activities.created_at', now()->year);
        }

        // Search by User ID
        if ($search) {
            $query->orderByRaw("users.id = ? DESC", [$search]); // Show search result first
        }

        $users = $query->orderByDesc('total_points')->get();

        // Assign ranks
        $rankedUsers = $users->map(function ($user, $index) {
            $user->rank = $index + 1;
            return $user;
        });

        $userActivities = UserActivity::with('user')->get();
        $users = User::get();
        return response()->json([
            'rankedUsers' => $rankedUsers,
            'users' => $users,
            'userActivities' => $userActivities,
        ]);
    }

    public function recalculate()
    {
        $userPoints = DB::table('user_activities')
            ->select('user_id', DB::raw('SUM(points) as total_points'))
            ->groupBy('user_id')
            ->orderByDesc('total_points')
            ->get();

        $rank = 1;
        foreach ($userPoints as $user) {
            DB::table('user_activities')
                ->where('user_id', $user->user_id)
                ->update(['rank' => $rank]);

            $rank++;
        }

        return redirect()->route('leaderboard.index')->with('success', 'Leaderboard recalculated successfully!');
    }

}
