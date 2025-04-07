<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Rating;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
class RatingController extends Controller
{
    // Listar todas las valoraciones
    public function index()
    {
        $ratings = Rating::with('user:id,name')->get();
        return response()->json($ratings);
    }
    private function normalizeUrl($url) {
        // Elimina barras duplicadas exceptuando aquellas que siguen a http: o https:
        return preg_replace('/([^:])\/+/', '$1/', $url);
    }
    // Guardar una valoración
    public function store(Request $request)
    {
        $request->validate([
            'score' => 'required|integer|between:1,5',
            'comment' => 'required|string',
            'categories' => 'required|string', // Esperamos un string separado por comas
            'job_position' => 'required|string',
            'company' => 'required|string',
        ]);

        $user = Auth::user();
        
        // Crear la reseña
        $rating = new Rating();
        $rating->user_id = $user->id;
        $rating->score = $request->score;
        $rating->comment = $request->comment;
        $rating->categories = $request->categories; // Ya viene como string
        $rating->job_position = $request->job_position;
        $rating->company = $request->company;
        
        // Obtener la foto de perfil del usuario
        if ($user->avatar) {
            Log::info('Usuario tiene avatar: ' . $user->avatar);
            $rating->photo_path = $this->normalizeUrl($user->avatar);
        } else {
            // Intentar obtener avatar de media library si existe
            try {
                $media = $user->getFirstMedia('avatars');
                if ($media) {
                    Log::info('Usuario tiene media: ' . $media->getUrl());
                    $rating->photo_path = $this->normalizeUrl($media->getUrl());
                } else {
                    Log::info('Usuario sin avatar ni media');
                    // Usar avatar por defecto
                    $rating->photo_path = '/images/testimonials/placeholder.webp';
                }
            } catch (\Exception $e) {
                Log::error('Error al obtener avatar de media: ' . $e->getMessage());
                // Usar avatar por defecto en caso de error
                $rating->photo_path = '/images/testimonials/placeholder.webp';
            }
        }
        
        // Guardar la reseña con la información de la foto
        $rating->save();
        
        Log::info('Rating creado', [
            'user_id' => $user->id,
            'photo_path' => $rating->photo_path
        ]);

        return response()->json([
            'success' => true,
            'data' => $rating,
            'message' => 'Valoración creada correctamente'
        ], 201);
    }

    // Mostrar una valoración específica
    public function show($id)
    {
        $rating = Rating::with('user:id,name')->findOrFail($id);
        return response()->json($rating);
    }

    // Actualizar una valoración
    public function update(Request $request, $id)
    {
        $rating = Rating::findOrFail($id);

        // Verificar que el usuario sea dueño de la valoración o admin
        if ($rating->user_id !== Auth::id()) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        // Validar
        $validated = $request->validate([
            'score' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
            'categories' => 'required|array|min:1|max:2',
            'categories.*' => 'in:Marketing,Comunicacion,Desarrollo,Customizacion,Startup,Escalabilidad',
            'job_position' => 'required|string|max:100',
            'company' => 'required|string|max:100'
        ]);

        $rating->update($validated);

        return response()->json([
            'message' => 'Valoración actualizada',
            'rating' => $rating
        ]);
    }

    // Borrar una valoración
    public function destroy($id)
    {
        $rating = Rating::findOrFail($id);

        // Verificar que el usuario sea dueño o admin
        if ($rating->user_id !== Auth::id()) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $rating->delete();

        return response()->json(['message' => 'Valoración eliminada']);
    }
}