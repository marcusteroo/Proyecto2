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
use App\Http\Controllers\Api\WorkflowController;
use App\Http\Controllers\Api\WorkflowActionController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Api\KanbanController;
use App\Http\Controllers\Api\TableroController; 
use App\Http\Controllers\Api\FavoritoController;
use App\Http\Controllers\Api\SubtareaController;
use App\Http\Controllers\Api\RatingController;
use App\Http\Controllers\Api\PreciosController;
use App\Http\Controllers\Api\FeaturedRatingController;
use App\Http\Controllers\Api\AdminStatsController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public routes (no authentication required)
Route::post('forget-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('forget.password.post');
Route::post('reset-password', [ResetPasswordController::class, 'reset'])->name('password.reset');
Route::get('category-list', [CategoryController::class, 'getList']);
Route::get('get-posts', [PostControllerAdvance::class, 'getPosts']);
Route::get('get-category-posts/{id}', [PostControllerAdvance::class, 'getCategoryByPosts']);
Route::get('/get-post/{id}', [PostControllerAdvance::class, 'getPost']);
Route::post('/check-email', [RegisterController::class, 'checkEmail']);
Route::get('public/precios', [PreciosController::class, 'getPrecios']);
Route::get('/public/featured-ratings', [FeaturedRatingController::class, 'getFeaturedForHome']);

// Protected routes (authentication required)
Route::group(['middleware' => 'auth:sanctum'], function() {
    // User management
    Route::apiResource('users', UserController::class);
    Route::post('users/updateimg', [UserController::class, 'updateimg']);
    Route::put('/password', [UserController::class, 'updatePassword']);
    Route::get('/user/{id}/media-files', 'App\Http\Controllers\Api\UserController@getUserMediaFiles');
    Route::get('/user/{id}/list-files', [UserController::class, 'listUserFiles']);
    
    // Post and category management
    Route::apiResource('posts', PostControllerAdvance::class);
    Route::apiResource('categories', CategoryController::class);
    Route::get('category-list', [CategoryController::class, 'getList']);
    
    // Role and permission management
    Route::apiResource('roles', RoleController::class);
    Route::get('role-list', [RoleController::class, 'getList']);
    Route::get('role-permissions/{id}', [PermissionController::class, 'getRolePermissions']);
    Route::put('/role-permissions', [PermissionController::class, 'updateRolePermissions']);
    Route::apiResource('permissions', PermissionController::class);
    
    // User profile management
    Route::get('/user', [ProfileController::class, 'user']);
    Route::put('/user', [ProfileController::class, 'update']);
    Route::post('/user/avatar', [ProfileController::class, 'updateAvatar']);
    Route::delete('/user/avatar', [ProfileController::class, 'removeAvatar']);
    
    // Workflow routes (protected)
    Route::post('/workflows', [WorkflowController::class, 'store']);
    Route::post('/workflows/{id}/actions', [WorkflowActionController::class, 'store']);
    Route::get('/workflows', [WorkflowController::class, 'index']);
    Route::delete('/workflows/{id}', [WorkflowController::class, 'destroy']);
    Route::put('/workflows/{id}', [WorkflowController::class, 'update']);
    Route::put('/workflow-actions/{id}', [WorkflowActionController::class, 'update']);
    Route::get('/workflows/{id}', [WorkflowController::class, 'show']);
    Route::get('/workflows-action/{id}', [WorkflowController::class, 'getActions']);
    Route::get('/admin/all-workflows', [WorkflowController::class, 'getAllWorkflows']);
    Route::get('/workflows/{id}/edit', [WorkflowController::class, 'edit'])->name('workflows.edit');
    Route::get('/execute-workflow/{id}', [WorkflowController::class, 'execute']);
    
    // Kanban routes (protected)
    Route::apiResource('kanbans', KanbanController::class);
    Route::get('kanban/{id}', [KanbanController::class, 'show']);
    Route::post('kanban', [KanbanController::class, 'store']);
    Route::put('kanban/{id}', [KanbanController::class, 'update']);
    Route::patch('kanban/{id}', [KanbanController::class, 'update']);
    Route::delete('kanban/{id}', [KanbanController::class, 'destroy']);
    Route::get('/kanban', [KanbanController::class, 'getBoards']);
    Route::get('/kanban/{id}/tasks', [KanbanController::class, 'getTasks']);
    
    // Tablero routes (protected)
    Route::apiResource('tableros', TableroController::class);
    Route::get('tableros/user/{id}', [TableroController::class, 'getByUser']);
    
    // Subtareas routes (protected)
    Route::prefix('subtareas')->group(function () {
        Route::get('/', [SubtareaController::class, 'index']);
        Route::post('/', [SubtareaController::class, 'store']);
        Route::get('/{id}', [SubtareaController::class, 'show']);
        Route::put('/{id}', [SubtareaController::class, 'update']);
        Route::delete('/{id}', [SubtareaController::class, 'destroy']);
        Route::get('/tarea/{idTarea}', [SubtareaController::class, 'getSubtareasByTarea']);
        Route::put('/tarea/{idTarea}', [SubtareaController::class, 'updateSubtareas']);
    });
    
    // Workflow sharing
    Route::post('/workflows/{id}/share', [WorkflowController::class, 'shareWorkflow']);
    Route::get('/workflows/users/potential', [WorkflowController::class, 'getPotentialUsers']);
    
    // Tablero sharing
    Route::get('/tableros/users/potential-share', [TableroController::class, 'getPotentialUsers']);
    Route::post('/tableros/{id}/share', [TableroController::class, 'shareTablero']);
    
    // Favoritos management
    Route::get('/favoritos', [FavoritoController::class, 'index']);
    Route::post('/favoritos/toggle', [FavoritoController::class, 'toggleFavorito']);
    Route::post('/favoritos/check', [FavoritoController::class, 'isFavorito']);
    
    // Pricing management
    Route::apiResource('precios', PreciosController::class);
    
    // Abilities/permissions check
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
    
    // Ratings management
    Route::prefix('ratings')->group(function() {
        Route::get('/', [RatingController::class, 'index']);
        Route::post('/', [RatingController::class, 'store']);
        Route::get('/{id}', [RatingController::class, 'show']);
        Route::put('/{id}', [RatingController::class, 'update']);
        Route::delete('/{id}', [RatingController::class, 'destroy']);
    });
    
    // Admin features
    Route::get('/admin/ratings', [FeaturedRatingController::class, 'index']);
    Route::patch('/admin/ratings/{id}/featured', [FeaturedRatingController::class, 'updateFeaturedStatus']);
    Route::post('/admin/ratings/order', [FeaturedRatingController::class, 'updateOrder']);
    Route::get('/admin/stats', [AdminStatsController::class, 'index']);
});