<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Application;
use App\Models\Module;
use Illuminate\Support\Facades\Auth;

class ApplicationCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $role = null, $modo = null)
    {
        $id = $request->route("id");
        $type = $request->route("type");

        if(!$id) return response()->json([
                "status" => "404",
                "message" => __("error.404.message")
        ], 404);
        
        $application = Application::where("_id", $id)
            ->first();

        $header = $request->header("Authorization");
        $user = $request->user("api");
        $collaborators = isset($application->collaborators) ? $application->collaborators : [];
        $collaborator = ($role && $user) ? collect($collaborators)->where("user_id", $user->discord_id)->first() : null;

        // xor ((isset($collaborator) && isset($collaborator["role"])) && $collaborator["role"] == $role || isset($modo) && $collaborator["role"] == $modo)
        if(
            !$application 
            || ($user ? $application->owner_id != $user->id : $application->bot_token != $header)
        )
        {
            return response()->json([
                "status" => "404",
                "message" => __("error.404.message")
            ], 404);
        }

        $request->request->add(["collaborator" => isset($collaborator) ? $collaborator["role"] : false]);

        app()->instance("application", $application);

        if($type) {
            $modules = config("ro-bot.modules");
            if(!isset($modules[$type])) return response()->json([
                "status" => 404,
                "message" => []
            ], 404);

            $module = collect($application->modules)->where("type", $type)->first();

            if($module) {
                $mod = Module::where("_id", $module["id"])->first();
                app()->instance("module", $mod);
            } else {
                app()->instance("module", []);
            }
        } else {
            app()->instance("module", false);
        }
    
        return $next($request);
    }
}
