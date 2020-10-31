<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Application;
use App\Models\Log;

use App\Http\Requests\LogRequest;

class LogController extends Controller {
    public function index(string $id, Request $request)
    {
        $application = resolve("application");

        return Log::filter($request->all())
            ->where("app_id", $id)
            ->get();
    }

    public function store(string $id, LogRequest $request)
    {
        if($request->user("api")) return;

        $header = $request->header("Authorization");

        if(!isset($header)) {
            return response()->json([
                "status" => 401,
                "message" => __("error.401.message")
            ], 401);
        }
        
        $application = Application::where("bot_token", $header)->where("_id", $id)->first();
        if(!isset($application)) {
            return response()->json([
                "status" => 404,
                "message" => __("error.404.message")
            ], 404);
        }

        return Log::create($request->validated());
    }
}