<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $orderColumn = request('order_column', 'created_at');
        if (!in_array($orderColumn, ['id', 'name', 'created_at'])) {
            $orderColumn = 'created_at';
        }
        
        $orderDirection = request('order_direction', 'desc');
        if (!in_array($orderDirection, ['asc', 'desc'])) {
            $orderDirection = 'desc';
        }
        
        $users = User::with('roles')
            ->when(request('search_id'), function ($query) {
                $query->where('id', request('search_id'));
            })
            ->when(request('search_title'), function ($query) {
                $query->where('name', 'like', '%'.request('search_title').'%');
            })
            ->when(request('search_global'), function ($query) {
                $query->where(function($q) {
                    $q->where('id', request('search_global'))
                        ->orWhere('name', 'like', '%'.request('search_global').'%');
                });
            })
            ->orderBy($orderColumn, $orderDirection)
            ->paginate(50);

        return UserResource::collection($users);
    }

    public function store(StoreUserRequest $request)
    {
        $role = Role::find($request->role_id);
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->surname1 = $request->surname1;
        $user->surname2 = $request->surname2;

        $user->password = Hash::make($request->password);

        if ($user->save()) {
            if ($role) {
                $user->assignRole($role);
            }
            return new UserResource($user);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return UserResource
     */
    public function show(User $user)
    {
        $user->load('roles')->get();
        return new UserResource($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateUserRequest $request
     * @param User $user
     * @return UserResource
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $role = Role::find($request->role_id);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->surname1 = $request->surname1;
        $user->surname2 = $request->surname2;

        if(!empty($request->password)) {
            $user->password = Hash::make($request->password) ?? $user->password;
        }

        if ($user->save()) {
            if ($role) {
                $user->syncRoles($role);
            }
            return new UserResource($user);
        }
    }


    public function updateimg(Request $request)
    {

        $user = User::find($request->id);

        if($request->hasFile('picture')) {
            $user->media()->delete();
            $media = $user->addMediaFromRequest('picture')->preservingOriginal()->toMediaCollection('images-users');

        }
        $user =  User::with('media')->find($request->id);
        return  $user;
    }

    public function destroy(User $user)
    {
        $this->authorize('user-delete');
        $user->delete();

        return response()->noContent();
    }
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/'
            ],
        ], [
            'password.regex' => 'La contraseña debe contener al menos una letra mayúscula y un número.',
        ]);
        
        $user = auth()->user();
        
        // Verificar si la contraseña actual es correcta
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'errors' => [
                    'current_password' => ['La contraseña actual es incorrecta.']
                ]
            ], 422);
        }
        
        // Verificar si la nueva contraseña es igual a la actual
        if (Hash::check($request->password, $user->password)) {
            return response()->json([
                'errors' => [
                    'password' => ['La nueva contraseña debe ser diferente a la contraseña actual.']
                ]
            ], 422);
        }
        
        $user->password = Hash::make($request->password);
        $user->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Contraseña actualizada correctamente'
        ]);
    }

    public function getUserTasks($id)
    {
        $tasks = Task::where('user_id', $id)->get();
        return response()->json($tasks);
    }

    public function getUserById($id)
    {
        $user = User::find($id);
        return response()->json($user);
    }
public function getPotentialShareUsers()
{
    $users = User::select('id', 'name', 'email')
            ->where('id', '!=', Auth::id())
            ->get();
            
    return response()->json($users);
}
public function getUserMediaFiles($id)
{
    try {
        $user = User::findOrFail($id);
        
        // 1. Verificar si el usuario tiene un avatar en Media Library
        $mediaItem = $user->getFirstMedia('avatars');
        if ($mediaItem) {
            return response()->json([
                'success' => true,
                'avatar_url' => $mediaItem->getUrl()
            ]);
        }
        
        // 2. Si no tiene en Media Library, buscar físicamente en el directorio
        $storagePath = storage_path("app/public/{$id}");
        
        if (file_exists($storagePath) && is_dir($storagePath)) {
            $files = scandir($storagePath);
            // Filtrar archivos ocultos y directorios
            $imageFiles = array_filter($files, function($file) use ($storagePath) {
                $extension = pathinfo($file, PATHINFO_EXTENSION);
                return !in_array($file, ['.', '..']) && 
                       !is_dir($storagePath . '/' . $file) && 
                       in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
            });
            
            if (!empty($imageFiles)) {
                // Tomar el primer archivo de imagen encontrado
                $firstImage = reset($imageFiles);
                $avatarUrl = "/storage/{$id}/{$firstImage}";
                
                return response()->json([
                    'success' => true,
                    'avatar_url' => $avatarUrl
                ]);
            }
        }
        
        // 3. Si no se encuentra ninguna imagen
        return response()->json([
            'success' => false,
            'message' => 'No avatar found for this user'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error retrieving user media files',
            'error' => $e->getMessage()
        ], 500);
    }
}
public function listUserFiles($id)
{
    try {
        $storagePath = storage_path("app/public/{$id}");
        
        if (!file_exists($storagePath) || !is_dir($storagePath)) {
            return response()->json([
                'success' => false,
                'message' => 'No directory found for this user',
                'files' => []
            ]);
        }
        
        $files = scandir($storagePath);
        // Filtrar archivos relevantes (eliminar . y .. y obtener solo imágenes)
        $imageFiles = array_filter($files, function($file) use ($storagePath) {
            $extension = pathinfo($file, PATHINFO_EXTENSION);
            return !in_array($file, ['.', '..']) && 
                   !is_dir($storagePath . '/' . $file) && 
                   in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
        });
        
        return response()->json([
            'success' => true,
            'files' => array_values($imageFiles) // Usar array_values para resetear índices
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error listing user files',
            'error' => $e->getMessage(),
            'files' => []
        ], 500);
    }
}


}
