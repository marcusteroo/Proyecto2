<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    /**
     * Actualiza el perfil del usuario, incluyendo la imagen de avatar si se envía.
     *
     * @throws ValidationException
     */
    public function update(Request $request)
    {
        $profile = Auth::user();
        $profile->name = $request->name;
        $profile->email = $request->email;

        // Si se envía un archivo 'avatar', se almacena y se guarda la URL en el perfil
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            // Puedes agregar validaciones adicionales aquí si lo deseas
            $path = $file->store('avatars', 'public'); // Guarda la imagen en storage/app/public/avatars
            $profile->avatar = Storage::url($path); // Genera una URL pública para el archivo
        }

        if ($profile->save()) {
            return response()->json([
                'success' => true, 
                'data' => $profile, 
                'message' => 'User updated'
            ], 200);
        }
        return response()->json(['status' => 403, 'success' => false], 403);
    }

    /**
     * Retorna los datos del usuario, incluyendo el avatar.
     */
    public function user(Request $request)
    {
        $user = $request->user()->load('roles');
        $avatar = '';
        
        try {
            if ($user->media && count($user->media) > 0) {
                $avatar = $user->media[0]->original_url;
            }
        } catch (\Exception $e) {
            // Opcional: Registrar el error si es necesario
        }
        
        // Si se tiene avatar en la tabla, se prioriza ese valor
        if ($user->avatar) {
            $avatar = $user->avatar;
        }
        $user->avatar = $avatar;
        return response()->json([
            'success' => true, 
            'data' => $user, 
            'message' => 'User found'
        ], 200);
    }
}
