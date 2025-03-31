<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Rating;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RatingController extends Controller
{
    // Listar todas las valoraciones
    public function index()
    {
        $ratings = Rating::with('user:id,name')->get();
        return response()->json($ratings);
    }

    // Guardar una valoración
    public function store(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Usuario no autenticado'], 401);
        }

        $validated = $request->validate([
            'score' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
            'categories' => 'required|array|min:1|max:2',
            'categories.*' => 'in:Marketing,Comunicacion,Desarrollo,Customizacion,Startup,Escalabilidad',
            'job_position' => 'required|string|max:100',
            'company' => 'required|string|max:100'
        ]);

        $user = Auth::user();
        
        $rating = Rating::create([
            'user_id' => $user->id,
            'score' => $validated['score'],
            'comment' => $validated['comment'] ?? null,
            'categories' => $validated['categories'],
            'job_position' => $validated['job_position'],
            'company' => $validated['company']
        ]);

        return response()->json([
            'message' => '¡Valoración guardada con éxito!',
            'rating' => $rating,
            'user_name' => $user->name
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