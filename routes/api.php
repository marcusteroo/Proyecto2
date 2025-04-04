<?php

use App\Http\Controllers\Api\AuthorController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\PostControllerAdvance;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\NoteController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Api\WhatsAppController;
use App\Http\Controllers\Api\WorkflowController;
use App\Http\Controllers\Api\WorkflowActionController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Api\KanbanController;
use App\Http\Controllers\Api\TableroController; 
use App\Http\Controllers\Api\FavoritoController;
use App\Http\Controllers\Api\SubtareaController;
use App\Http\Controllers\Api\RatingController;
use App\Http\Controllers\Api\PreciosController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('forget-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('forget.password.post');
Route::post('reset-password', [ResetPasswordController::class, 'reset'])->name('password.reset');

Route::group(['middleware' => 'auth:sanctum'], function() {

    Route::apiResource('users', UserController::class);
    Route::post('users/updateimg', [UserController::class, 'updateimg']);

    Route::apiResource('posts', PostControllerAdvance::class);
    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('roles', RoleController::class);

    Route::get('role-list', [RoleController::class, 'getList']);
    Route::get('role-permissions/{id}', [PermissionController::class, 'getRolePermissions']);
    Route::put('/role-permissions', [PermissionController::class, 'updateRolePermissions']);
    Route::apiResource('permissions', PermissionController::class);

    Route::get('category-list', [CategoryController::class, 'getList']);
    Route::get('/user', [ProfileController::class, 'user']);
    // El endpoint para actualizar el perfil ahora soporta subida de imágenes mediante multipart/form-data
    Route::put('/user', [ProfileController::class, 'update']);
    Route::post('/workflows/{id}/share', [WorkflowController::class, 'shareWorkflow']);
    Route::get('/workflows/users/potential', [WorkflowController::class, 'getPotentialUsers']);
    Route::get('/tableros/users/potential-share', [TableroController::class, 'getPotentialUsers']);
    Route::post('/tableros/{id}/share', [TableroController::class, 'shareTablero']);
    Route::get('/favoritos', [FavoritoController::class, 'index']);
    Route::post('/favoritos/toggle', [FavoritoController::class, 'toggleFavorito']);
    Route::post('/favoritos/check', [FavoritoController::class, 'isFavorito']);
    Route::apiResource('precios', PreciosController::class);
    Route::get('abilities', function(Request $request) {
        return $request->user()->roles()->with('permissions')
            ->get()
            ->pluck('permissions')
            ->flatten()
            ->pluck('name')
            ->unique()
            ->values()
            ->toArray();
    });
    Route::prefix('ratings')->group(function() {
        Route::get('/', [RatingController::class, 'index']);
        Route::post('/', [RatingController::class, 'store']);
        Route::get('/{id}', [RatingController::class, 'show']);
        Route::put('/{id}', [RatingController::class, 'update']);
        Route::delete('/{id}', [RatingController::class, 'destroy']);
    });
});

Route::get('category-list', [CategoryController::class, 'getList']);

Route::get('get-posts', [PostControllerAdvance::class, 'getPosts']);
Route::get('get-category-posts/{id}', [PostControllerAdvance::class, 'getCategoryByPosts']);
Route::get('/get-post/{id}', [PostControllerAdvance::class, 'getPost']);

Route::get('note', [NoteController::class, 'index'])->name('note.index');
Route::post('note', [NoteController::class, 'store'])->name('note.store');
Route::get('note/{id}', [NoteController::class, 'show'])->name('note.show');
Route::put('note/{id}', [NoteController::class, 'update'])->name('note.update');
Route::delete('note/{id}', [NoteController::class, 'destroy'])->name('note.destroy');

Route::get('author', [AuthorController::class, 'index']);
Route::post('author', [AuthorController::class, 'store']);
Route::delete('author/{author}', [AuthorController::class, 'destroy'])->name('author.destroy');
Route::get('author/{id}', [AuthorController::class, 'show']);
Route::put('author/{id}', [AuthorController::class, 'update']);

Route::post('send-message', [WhatsAppController::class, 'sendMessage']);

Route::post('/workflows', [WorkflowController::class, 'store']);
Route::post('/workflows/{id}/actions', [WorkflowActionController::class, 'store']);
Route::get('/workflows', [WorkflowController::class, 'index']);
Route::delete('/workflows/{id}', [WorkflowController::class, 'destroy']);
Route::put('/workflows/{id}', [WorkflowController::class, 'update']);
Route::put('/workflow-actions/{id}', [WorkflowActionController::class, 'update']);
Route::get('/workflows/{id}', [WorkflowController::class, 'show']);
Route::get('/workflows-action/{id}', [WorkflowController::class, 'getActions']);

Route::post('/check-email', [RegisterController::class, 'checkEmail']);

Route::apiResource('kanbans', KanbanController::class);
Route::get('kanban/{id}', [KanbanController::class, 'show']);
Route::post('kanban', [KanbanController::class, 'store']);
Route::put('kanban/{id}', [KanbanController::class, 'update']);
Route::patch('kanban/{id}', [KanbanController::class, 'update']);
Route::delete('kanban/{id}', [KanbanController::class, 'destroy']);
Route::get('/kanban', [KanbanController::class, 'getBoards']);
Route::get('/kanban/{id}/tasks', [KanbanController::class, 'getTasks']);

Route::apiResource('tableros', TableroController::class);
Route::get('/execute-workflow/{id}', [WorkflowController::class, 'execute']);
Route::put('/password', [UserController::class, 'updatePassword'])->middleware('auth:sanctum');
Route::get('tableros/user/{id}', [TableroController::class, 'getByUser']);

Route::prefix('subtareas')->group(function () {
    Route::get('/', [SubtareaController::class, 'index']);
    Route::post('/', [SubtareaController::class, 'store']);
    Route::get('/{id}', [SubtareaController::class, 'show']);
    Route::put('/{id}', [SubtareaController::class, 'update']);
    Route::delete('/{id}', [SubtareaController::class, 'destroy']);
    Route::get('/tarea/{idTarea}', [SubtareaController::class, 'getSubtareasByTarea']);
    Route::put('/tarea/{idTarea}', [SubtareaController::class, 'updateSubtareas']);
});
Route::get('public/precios', [PreciosController::class, 'getPrecios']);
Route::post('/user/avatar', [ProfileController::class, 'updateAvatar'])->middleware('auth:sanctum');
Route::delete('/user/avatar', [ProfileController::class, 'removeAvatar'])->middleware('auth:sanctum');