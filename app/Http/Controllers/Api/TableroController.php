<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tablero;
use App\Models\User;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\DB;

class TableroController extends Controller
{
    public function index()
    {
        $tableros = Tablero::all();
        return response()->json($tableros);
    }
    
    public function store(Request $request)
    {
        $tablero = new Tablero();
        $tablero->nombre = $request->nombre;
        $tablero->id_creador = $request->id_creador;
        // Si enviamos el color desde el frontend
        if ($request->has('color_fondo')) {
            $tablero->color_fondo = $request->color_fondo;
        }
        $tablero->save();

        return response()->json(['id_tablero' => $tablero->id_tablero], 201);
    }
    
    public function destroy($id)
    {
        $tablero = Tablero::findOrFail($id);
        $tablero->delete();    

        return response()->json(['message' => 'Tablero eliminado'], 200);
    }
    
    public function update(Request $request, $id)
    {
        $tablero = Tablero::findOrFail($id);
        $tablero->update([
            'nombre' => $request->nombre ?? $tablero->nombre,
            'id_creador' => $request->id_creador ?? $tablero->id_creador,
        ]);

        return response()->json(['message' => 'Tablero actualizado'], 200);
    }
    
    public function show($id)
    {
        $tablero = Tablero::findOrFail($id);
        return response()->json($tablero);
    }
    public function getByUser($userId)
{
    // Esto es para evitar que un usuario simplemente cambie el ID en la URL para acceder a tableros de otros usuarios.
    if (Auth::id() != $userId) {
        return response()->json(['error' => 'No autorizado'], 403);
    }

    // 1. Obtener los tableros creados por el usuario
    $ownedTableros = Tablero::where('id_creador', $userId)->get()
        ->map(function($tablero) {
            $tablero->is_shared = false;
            $tablero->is_owner = true;
            return $tablero;
        });
    
    // 2. Obtener los tableros compartidos con el usuario
    $sharedTableroIds = \DB::table('usuarios_tableros')
        ->where('user_id', $userId)
        ->pluck('tablero_id');
    
    $sharedTableros = Tablero::whereIn('id_tablero', $sharedTableroIds)
        ->where('id_creador', '!=', $userId)
        ->get()
        ->map(function($tablero) {
            // Buscar quién compartió este tablero (el creador)
            $creador = User::find($tablero->id_creador);
            
            $tablero->is_shared = true;
            $tablero->is_owner = false;
            $tablero->shared_by = $creador ? $creador->name : 'Usuario desconocido';
            return $tablero;
        });
    
    // 3. Combinar ambos conjuntos de tableros
    $allTableros = $ownedTableros->concat($sharedTableros);
    
    return response()->json($allTableros);
}
    public function shareTablero(Request $request, $id)
{
    $tablero = Tablero::findOrFail($id);
    
    // Verificar que el usuario actual es el propietario
    if (!$tablero->isOwnedBy(Auth::id())) {
        return response()->json([
            'message' => 'No tienes permisos para compartir este tablero'
        ], 403);
    }
    
    $request->validate([
        'user_id' => 'required|exists:users,id',
    ]);
    
    // Verificar que no se esté compartiendo consigo mismo
    if ($request->user_id == Auth::id()) {
        return response()->json([
            'message' => 'No puedes compartir el tablero contigo mismo'
        ], 422);
    }
    
    // Compartir el tablero
    $tablero->usuariosCompartidos()->syncWithoutDetaching([
        $request->user_id => [
            'fecha_compartido' => now()
        ]
    ]);
    
    return response()->json([
        'message' => 'Tablero compartido exitosamente'
    ]);
}
public function getPotentialUsers()
    {
        $users = User::select('id', 'name', 'email')
                ->where('id', '!=', Auth::id())
                ->get();
                
        return response()->json($users);
    }
}
