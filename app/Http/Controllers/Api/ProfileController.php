<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
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
        try {
            // Validar la solicitud
            $request->validate([
                'name' => 'required|string|max:255',
                'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048', // máximo 2MB
            ]);

            $profile = Auth::user();
            $profile->name = $request->name;
            // No actualizar el email para evitar problemas de validación
            // $profile->email = $request->email;

            // Eliminar avatar si se solicita
            if ($request->has('remove_avatar')) {
                $profile->clearMediaCollection('avatars');
            }
            // Usar Spatie Media Library para gestionar el avatar
            elseif ($request->hasFile('avatar')) {
                // Verificar que el archivo existe y es válido
                $file = $request->file('avatar');
                
                \Log::info('Procesando archivo de avatar', [
                    'file_exists' => (bool)$file,
                    'file_valid' => $file ? $file->isValid() : false,
                    'file_size' => $file ? $file->getSize() : 0,
                    'file_mime' => $file ? $file->getMimeType() : 'none'
                ]);
                
                if (!$file->isValid()) {
                    throw new \Exception("El archivo no es válido");
                }

                // Limpiar colección de media existente
                $profile->clearMediaCollection('avatars');
                
                try {
                    // Agregar nueva imagen
                    $media = $profile->addMediaFromRequest('avatar')
                            ->usingName('avatar-'.$profile->id)
                            ->toMediaCollection('avatars');
                    
                    \Log::info('Media creada correctamente', ['media_id' => $media->id, 'url' => $media->getUrl()]);
                } catch (\Exception $e) {
                    \Log::error('Error al procesar media', [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    throw $e;
                }
            }

            if ($profile->save()) {
                // Recuperar el usuario con la media actualizada
                $updatedUser = User::with('media')->find($profile->id);
                
                // Preparar respuesta con datos actualizados
                return response()->json([
                    'success' => true, 
                    'data' => $this->getUserWithAvatar($updatedUser), 
                    'message' => 'User updated'
                ], 200);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'No se pudo guardar el perfil'
            ], 500);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Error de validación al actualizar perfil', ['errors' => $e->errors()]);
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error general al actualizar perfil', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el perfil: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    /**
     * Retorna los datos del usuario, incluyendo el avatar.
     */
    public function user(Request $request)
    {
        $user = $request->user()->load('roles', 'media');
        return response()->json([
            'success' => true,
            'data' => $this->getUserWithAvatar($user)
        ]);
    }
    private function getUserWithAvatar($user)
    {
        try {
            $media = $user->getMedia('avatars')->first();
            
            if ($media) {
                // Obtener la información detallada del media
                $mediaPath = $media->getPath();
                $mediaDirectory = dirname($mediaPath);
                $conversionsDirectory = $mediaDirectory . '/conversions';
                
                // Verificar si el directorio de conversiones existe
                $conversionExists = file_exists($conversionsDirectory);
                
                // Si existe, listar los archivos para ver los nombres exactos
                $conversionFiles = [];
                if ($conversionExists) {
                    $files = scandir($conversionsDirectory);
                    foreach ($files as $file) {
                        if ($file != '.' && $file != '..') {
                            $conversionFiles[] = $file;
                        }
                    }
                }
                
                // Obtener URLs
                $originalUrl = $media->getFullUrl();
                $thumbnailUrl = $media->getFullUrl('thumbnail');
                
                // Log detallado
                \Log::info('Información detallada de media para usuario ' . $user->id, [
                    'media_id' => $media->id,
                    'original_path' => $mediaPath,
                    'conversions_dir' => $conversionsDirectory,
                    'conversions_dir_exists' => $conversionExists,
                    'conversion_files' => $conversionFiles,
                    'original_url' => $originalUrl,
                    'thumbnail_url' => $thumbnailUrl
                ]);
                
                // Normalizar URLs para evitar doble barra
                $normalizedOriginalUrl = str_replace(['http:////', 'https:////', '//'], ['http://', 'https://', '/'], $originalUrl);
                $normalizedThumbnailUrl = str_replace(['http:////', 'https:////', '//'], ['http://', 'https://', '/'], $thumbnailUrl);
                
                // Asignar URLs normalizadas
                $user->avatar = $normalizedOriginalUrl;
                $user->avatar_thumbnail = $normalizedThumbnailUrl;
            } else {
                $user->avatar = null;
                $user->avatar_thumbnail = null;
            }
        } catch (\Exception $e) {
            \Log::error('Error al obtener avatar', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $user->avatar = null;
            $user->avatar_thumbnail = null;
        }
        
        return $user;
    }

    // Agregar este método como una ruta alternativa:
    public function updateAvatar(Request $request)
    {
        try {
            $request->validate([
                'avatar' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            ]);

            $user = Auth::user();

            // Limpiar colección de media existente
            $user->clearMediaCollection('avatars');
            
            // Agregar nueva imagen
            $media = $user->addMediaFromRequest('avatar')
                    ->usingName('avatar-'.$user->id)
                    ->toMediaCollection('avatars');

            // Recuperar el usuario actualizado con su avatar
            $updatedUser = User::with('media')->find($user->id);
            
            return response()->json([
                'success' => true,
                'data' => $this->getUserWithAvatar($updatedUser),
                'message' => 'Avatar actualizado correctamente'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Error de validación', 
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error al subir avatar', [
                'user_id' => Auth::id(),
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error al subir el avatar: ' . $e->getMessage()
            ], 500);
        }
    }
    public function removeAvatar()
    {
        try {
            $user = Auth::user();
            $user->clearMediaCollection('avatars');
            
            return response()->json([
                'success' => true,
                'message' => 'Avatar eliminado correctamente',
                'data' => $this->getUserWithAvatar($user)
            ]);
        } catch (\Exception $e) {
            \Log::error('Error al eliminar avatar', [
                'user_id' => Auth::id(),
                'message' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el avatar: ' . $e->getMessage()
            ], 500);
        }
    }
}
