<?php

namespace App\Http\Controllers\Api\V1\Modules;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Score;

class MinigameController extends Controller
{
    public function index(string $id)
    {
        $scores = Score::where("app_id", $id)
            ->orderByDesc("amount")
            ->get();

        return [
            "leaderboard" => $scores->take(10)->all(),
            "count" => $scores->count()
        ];
    }

    public function store(string $id, Request $request)
    {
        $user_id = $request->input("user_id");
        $amount = $request->input("amount");

        if (!$user_id || !$amount) {
            return response()->json([
                "status" => 403,
                "message" => __("ro-bot.module_already_exists")
            ], 403);
        }
        
        $score = Score::firstOrNew([
            "user_id"   => $user_id,
            "app_id"    => $id 
        ]);

        $score->amount = $score->amount ? $score->amount + $amount : $amount;
        $score->push();
    }
}
