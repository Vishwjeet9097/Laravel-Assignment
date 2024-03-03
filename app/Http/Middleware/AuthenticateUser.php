<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Repositories\AuthRepository;
use Closure;

class AuthenticateUser
{
    public function handle($request, Closure $next, $guard = null)
    {
        $userRepository = new AuthRepository(new User());
        $selfData = $userRepository->self($request);
        $data = (array) $selfData->getData();
        if ($data && array_key_exists("status", $data)) {
            $request->request->add([
                "user_id" => $data["data"]->id,
                "user_type" => $data["data"]->user_type,
            ]);
            return $next($request);
        } else {
            return $selfData;
        }
    }
}
