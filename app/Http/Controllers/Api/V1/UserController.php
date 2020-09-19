<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\Users\UpdateRequest;
use App\Http\Resources\Users\UserCollection;
use App\Http\Resources\Users\UserResource;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;

use Illuminate\Support\Facades\Auth;

use App\Helpers\Discord;

use File;
use Response;
use Image;
use Storage;

/**
 * @group User
 * Endpoint relative to users.
 */
class UserController extends Controller
{
    public function me()
    {
        return new UserResource( Auth::user() );
    }
}
