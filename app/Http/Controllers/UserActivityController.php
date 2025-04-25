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

        $query = User::select('users.id', 'users.name', 'users.total_points', 'users.rank')
            ->leftJoin('user_activities', 'users.id', '=', 'user_activities.user_id')
            ->groupBy('users.id', 'users.name', 'users.total_points', 'users.rank');

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
            // $user->rank = $index + 1;
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
        // Following query will return user_Id, total_points of each user
        // and order them by total_points in descending order
        // and update the rank of each user in the user_activities table
        $userPoints = DB::table('user_activities')
            ->select('user_id', DB::raw('SUM(points) as total_points'))
            ->groupBy('user_id')
            ->orderByDesc('total_points')
            ->get();

        $rank = 1;
        $lastPoints = null;
        // $index = 0;
        for($index = 0; $index < count($userPoints); $index++){
            if($index > 0){
                $lastPoints = $userPoints[$index - 1]->total_points;
            }

            //If points are same, then rank will be same

            if($lastPoints && $lastPoints != $userPoints[$index]->total_points) {
                $rank++;
            }
            DB::table('users')
                ->where('id', $userPoints[$index]->user_id)
                ->update([
                    'rank' => $rank,
                    'total_points' => $userPoints[$index]->total_points,
                ]);
            // DB::table('user_activities')
            //     ->where('user_id', $userPoints[$index]->user_id)
            //     ->update(['rank' => $rank]);

        }
        // foreach ($userPoints as $user) {
        //     if($index > 0){
        //         $lastPoints = $userPoints[$index - 1]->total_points;
        //     }
        //     //If points are same, then rank will be same
        //     if($lastPoints && $lastPoints != $user->total_points) {
        //         $rank++;
        //     }
        //     DB::table('user_activities')
        //         ->where('user_id', $user->user_id)
        //         ->update(['rank' => $rank]);

        //         $index++;
        // }

        return redirect()->route('leaderboard.index')->with('success', 'Leaderboard recalculated successfully!');
    }

}
