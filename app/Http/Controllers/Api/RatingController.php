<?php

namespace App\Http\Controllers;

use App\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RatingController extends Controller
{
    // Listar todas las valoraciones (opcional, si quieres verlas en el admin)
    public function index()
    {
        // Ejemplo: traer todas las valoraciones con usuario
        $ratings = Rating::with('user')->get();
        return response()->json($ratings);
    }

    // Guardar una valoración
    public function store(Request $request)
    {
        // Validar datos
        $validated = $request->validate([
            'score' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500'
        ]);

        // Crear la valoración
        $rating = Rating::create([
            'user_id' => Auth::id(),     // o $request->user()->id
            'score'   => $validated['score'],
            'comment' => $validated['comment'] ?? null,
        ]);

        return response()->json([
            'message' => '¡Valoración guardada con éxito!',
            'rating'  => $rating
        ], 201);
    }

    // Mostrar una valoración específica
    public function show($id)
    {
        $rating = Rating::with('user')->findOrFail($id);
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
            'comment' => 'nullable|string|max:500'
        ]);

        $rating->update($validated);

        return response()->json([
            'message' => 'Valoración actualizada',
            'rating'  => $rating
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
