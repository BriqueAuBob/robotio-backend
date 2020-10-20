<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Auth;

use App\Models\Application;
use App\Models\Log;

use App\Http\Requests\LogRequest;

class LogController extends Controller {
    public function index(string $id, Request $request)
    {
        $user = Auth::user();

        $application = Application::where("_id", $id)->first();
        if(!isset($application) || $application["owner_id"] != $user["_id"]) return;

        return Log::filter($request->all())->get();
    }

    public function store(string $id, LogRequest $request)
    {
        if($request->user("api")) return;

        $header = $request->header("Authorization");

        if(!isset($header)) {
            return response()->json([
                "status" => 401,
                "message" => "Vous devez vous connecter pour effectuer cette requête!"
            ], 401);
        }
        
        $application = Application::where("bot_token", $header)->where("_id", $id)->first();
        if(!isset($application)) {
            return response()->json([
                "status" => 404,
                "message" => "Cette application n'existe pas!"
            ], 404);
        }

        return Log::create($request->validated());
    }
}