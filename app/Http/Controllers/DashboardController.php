<?php

namespace App\Http\Controllers;

use App\User;
use App\Vote;
use App\Campaign;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index() {
        $users = User::orderBy('created_at', 'desc')->get()->take(10);
        $users_count = count(User::all());
        $admins_count = count(User::whereHas('roles', function($query) {
            return $query->where('name', 'admin');
        })->get());
        $campaigns_count = count(Campaign::all());
        $vote_count = count(Vote::all());
        return response()->json([
            "users" => $users,
            "users_count" => $users_count,
            "campaigns_count" => $campaigns_count,
            "admins_count" => $admins_count,
            "vote_count" => $vote_count,
        ]);
    }
}
