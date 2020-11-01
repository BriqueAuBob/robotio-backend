<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Module;

use App\Http\Requests\ModuleRequest;

use App\Http\Resources\Modules\ModuleCollection;
use App\Http\Resources\Modules\ModuleResource;

use App\Models\Score;

class ModuleController extends Controller
{
    public function index(string $id)
    {
        $application = resolve("application");
        $modules = [];
        if(!$application->modules) return $modules;
        foreach($application->modules as $key => $mod) {
            $mod = Module::where("_id", $mod["id"])
                ->first();
            $modules[] = $mod;
        }

        return new ModuleCollection($modules);
    }

    public function get(string $id, string $type)
    {
        $module = resolve("module");
        if(!$module) {
            return [];
        }
        return new ModuleResource($module);
    }

    public function update(string $id, string $type, ModuleRequest $request)
    {
        $module = resolve("module");
        
        $module->update($request->validated());
        return [
            "notification" => [
                "type" => "success",
                "layout" => "notification",
                "title" => __("ro-bot.success"),
                "content" => __("ro-bot.success_edit_module")
            ]
        ];
    }

    public function store(string $id, ModuleRequest $request)
    {
        $type = $request->input("type");
        $application = resolve("application");

        $modulesTypes = config("ro-bot.modules");
        if(!$type || !$modulesTypes[$type]) {
            return response()->json([
                "status" => 404,
                "message" => __("ro-bot.module_doesnt_exists_in_list")
            ], 404);
        }

        $module = collect($application->modules)->where("type", $type)->first();

        if($module) {
            return response()->json([
                "status" => 401,
                "message" => __("ro-bot.module_already_exists")
            ], 401);
        }
        $module = Module::create($request->validated());

        $modules = $application->modules;
        if(isset($modules) && is_array($modules)) {
            $modules[] = [
                "id" => $module->_id,
                "type" => $module->type
            ];
        } else {
            $modules = [
                [
                    "id" => $module->_id,
                    "type" => $module->type
                ]
            ];
        }
        $application->modules = $modules;
        $application->save();

        return [
            "notification" => [
                "type" => "success",
                "layout" => "notification",
                "title" => __("ro-bot.success"),
                "content" => __("ro-bot.you_add_module"),
            ]
        ];
    }
}
