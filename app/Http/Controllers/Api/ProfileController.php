<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    /**
     * @throws ValidationException
     */
    public function update(UpdateProfileRequest $request)
    {
        $profile = Auth::user();
        $profile->name = $request->name;
        $profile->email = $request->email;

        if ($profile->save()) {
            return $this->successResponse($profile, 'User updated');;
        }
        return response()->json(['status' => 403, 'success' => false]);
    }

    public function user(Request $request)
    {
        $user = $request->user()->load('roles');
        $avatar = '';
        
        try {
            if ($user->media && count($user->media) > 0) {
                $avatar = $user->media[0]->original_url;
            }
        } catch (\Exception $e) {
            // Puedes registrar el error si lo necesitas
            // \Log::error('Error al acceder a media: ' . $e->getMessage());
        }
        
        $user->avatar = $avatar;
        return $this->successResponse($user, 'User found');
    }
}
