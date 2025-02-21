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


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('forget-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('forget.password.post');
Route::post('reset-password', [ResetPasswordController::class, 'reset'])->name('password.reset');

Route::group(['middleware' => 'auth:sanctum'], function() {

    Route::apiResource('users', UserController::class);

    Route::post('users/updateimg', [UserController::class,'updateimg']); //Listar

    Route::apiResource('posts', PostControllerAdvance::class);
    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('roles', RoleController::class);

    Route::get('role-list', [RoleController::class, 'getList']);
    Route::get('role-permissions/{id}', [PermissionController::class, 'getRolePermissions']);
    Route::put('/role-permissions', [PermissionController::class, 'updateRolePermissions']);
    Route::apiResource('permissions', PermissionController::class);

    Route::get('category-list', [CategoryController::class, 'getList']);
    Route::get('/user', [ProfileController::class, 'user']);
    Route::put('/user', [ProfileController::class, 'update']);

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
});

Route::get('category-list', [CategoryController::class, 'getList']);

Route::get('get-posts', [PostControllerAdvance::class, 'getPosts']);
Route::get('get-category-posts/{id}', [PostControllerAdvance::class, 'getCategoryByPosts']);
Route::get('get-post/{id}', [PostControllerAdvance::class, 'getPost']);

Route::get('note',[NoteController::class,'index'])->name('note.index');
Route::post('note',[NoteController::class,'store'])->name('note.store');
Route::get('note/{id}',[NoteController::class,'show'])->name('note.show');
Route::put('note/{id}',[NoteController::class,'update'])->name('note.update');
Route::delete('note/{id}',[NoteController::class,'destroy'])->name('note.destroy');
Route::get('author',[AuthorController::class,'index']);
Route::post('author',[AuthorController::class,'store']);
Route::delete('author/{author}',[AuthorController::class,'destroy'])->name('author.destroy');
Route::get('author/{id}',[AuthorController::class,'show']);
Route::put('author/{id}',[AuthorController::class,'update']);
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
Route::delete('kanban/{id}', [KanbanController::class, 'destroy']);