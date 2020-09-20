<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use GuzzleHttp\Command\Exception\CommandClientException;

use App\Models\User;
use App\Models\Application;
use App\Models\Module;
use Illuminate\Support\Facades\Auth;

use App\Helpers\Discord;
use RestCord\DiscordClient;

use App\Http\Resources\Applications\ApplicationCollection;
use App\Http\Resources\Applications\ApplicationResource;

use App\Http\Requests\ModuleRequest;

class ModuleController extends Controller
{
    public function index()
    {
        $application = Application::where($id)->get();

        return $application;
    }

    public function get(string $id, string $type, Request $request)
    {
        if(!$request->user("api")) {
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

            $mod = collect($application->modules)->where("type", $type)->first();
            $module = Module::find($mod["id"]);

            return $module;
        }
            
        $application = Application::find($id);
        $mod = collect($application->modules)->where("type", $type)->first();
        $module = Module::find($mod["id"]);

        return $module;
    }

    public function store(string $id, ModuleRequest $request)
    {
        $user = Auth::user();
        $application = Application::find($id);

        $mod = collect($application->modules)->where("type", $request->input("type"))->first();
        if(isset($mod))
        {
            return response()->json([
                "status" => 401,
                "message" => "Ce module existe déjà!"
            ], 401);
        }

        $module = Module::create($request->validated());
        $modules = $application->modules;
        if(isset($modules)) {
            array_push($modules, [
                "id" => $module->_id,
                "type" => $module->type
            ]);
        } else {
            $modules = array([
                "id" => $module->_id,
                "type" => $module->type
            ]);
        }
        $application->modules = $modules;
        $application->save();

        return [
            "notification" => [
                "type" => "success",
                "layout" => "notification",
                "title" => "Succès!",
                "content" => "Vous avez ajouté le module."
            ]
        ];
    }

    public function edit(string $id, ModuleRequest $request)
    {
        $application = Application::find($id);
        $mod = collect($application->modules)->where("type", $type)->first();
        $module = Module::find($mod["id"]);

        $module->update($request->validated());
        return [
            "notification" => [
                "type" => "success",
                "layout" => "notification",
                "title" => "Succès!",
                "content" => "Vous avez édité les informations du module."
            ]
        ];
    }
}
