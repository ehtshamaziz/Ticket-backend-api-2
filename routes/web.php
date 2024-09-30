<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

Route::get('/', function () {
    return response()->json([], 200);
});

// Route::get('/api/csrf-cookie', function () {
//     return response()->json(['csrfToken' => csrf_token()])
//         ->header('Access-Control-Allow-Origin', 'http://localhost:5173')
//         ->header('Access-Control-Allow-Credentials', 'true');
// });

Route::post('/admin/login', [AdminLoginController::class, 'login'])->name('admin.login.submit');

// Admin login route (GET request to show the login form)
//Route::get('/admin/login', [AdminLoginController::class, 'showLoginForm'])->name('admin.login');
// Admin login route (POST request to handle login)

  Route::options('{any}', function () {
     return response()->json([], 200);
  })->where('any', '.*');

Route::prefix('api')->group(function () {
    // Your API routes here
    Route::get('/csrf-token', function() {
        return response()->json(['token' => csrf_token()]);
    });
    // Other API routes
});


Route::middleware('auth:admin')->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin/users', [AdminController::class, 'users'])->name('admin.users');
    Route::patch('/admin/users/{id}', [AdminController::class, 'updateUser'])->name('admin.users.update');
    Route::delete('/admin/users/{id}', [AdminController::class, 'deleteUser']);

    Route::get('/admin/tasks', [AdminController::class, 'tasks'])->name('admin.tasks');
    Route::patch('/admin/tasks/{id}', [AdminController::class, 'updateTask'])->name('admin.task.update');
    Route::post('/admin/tasks', [AdminController::class, 'storeTask'])->name('admin.task.store');
    Route::delete('/admin/tasks/{id}', [AdminController::class, 'deleteTask']);

    Route::get('/admin/tasks/create', [AdminController::class, 'createTask'])->name('admin.create_task');


    Route::get('/admin/mission-levels', [AdminController::class, 'mission_levels'])->name('admin.mission_levels');
    Route::patch('/admin/mission-levels/{id}', [AdminController::class, 'updateMissionLevels'])->name('admin.mission_level.update');
    Route::post('/admin/mission-levels', [AdminController::class, 'storeMissionLevels'])->name('admin.missions_level.store');
    Route::delete('/admin/mission-levels/{id}', [AdminController::class, 'deleteMissionLevel',])->name('admin.missions_level.delete');

    Route::get('/admin/mission-types', [AdminController::class, 'mission_types'])->name('admin.mission_types');
    Route::post('/admin/mission-types', [AdminController::class, 'storeMissionType'])->name('admin.mission_type.add');
    Route::patch('/admin/mission-types/{id}', [AdminController::class, 'updateMissionType'])->name('admin.mission_type.update');
    Route::delete('/admin/mission-types/{id}', [AdminController::class, 'deleteMissionType'])->name('admin.mission_type.delete');

    Route::post('/admin/telegram-user-tasks', [AdminController::class, 'storeTelegramUserTask'])->name('admin.telegram_user_task.store');
    Route::get('/admin/telegram-user-tasks', [AdminController::class, 'telegram_user_tasks'])->name('admin.telegram_user_tasks');
    Route::patch('/admin/telegram-user-tasks/{id}', [AdminController::class, 'updateTelegramUserTask'])->name('admin.telegram_user_task.update');
    Route::delete('/admin/telegram-user-tasks/{id}', [AdminController::class, 'deleteTelegramUserTask'])->name('admin.telegram_user_task.delete');


    Route::get('/admin/missions', [AdminController::class, 'missions'])->name('admin.missions');
    Route::post('/admin/missions', [AdminController::class, 'storeMission'])->name('admin.missions.store');
    Route::patch('/admin/missions/{id}', [AdminController::class, 'updateMission']);
    Route::delete('/admin/missions/{id}', [AdminController::class, 'deleteMission']);



    // Route::post('/admin/tasks', [AdminController::class, 'storeTask'])->name('admin.store_task');

    Route::get('/admin/daily-tasks', [AdminController::class, 'dailyTasks'])->name('admin.daily_tasks');
    Route::get('/admin/daily-tasks/create', [AdminController::class, 'createDailyTask'])->name('admin.create_daily_task');
    Route::post('/admin/daily-tasks', [AdminController::class, 'storeDailyTask'])->name('admin.store_daily_task');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::get('/tasks', [AdminController::class, 'tasks'])->name('tasks');
    Route::get('/tasks/create', [AdminController::class, 'createTask'])->name('create_task');
    Route::post('/tasks', [AdminController::class, 'storeTask'])->name('store_task');
    Route::get('/daily-tasks', [AdminController::class, 'dailyTasks'])->name('daily_tasks');
    Route::get('/daily-tasks/create', [AdminController::class, 'createDailyTask'])->name('create_daily_task');
    Route::post('/daily-tasks', [AdminController::class, 'storeDailyTask'])->name('store_daily_task');
});

require __DIR__.'/auth.php';
