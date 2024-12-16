<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DosenController;
use App\Http\Controllers\KegiatanController;


Route::get('/', function () {
   return redirect()->route('login');
});

// Auth Routes
Route::get('login', [AuthController::class, 'login'])->name('login');
Route::post('login', [AuthController::class, 'postlogin']);
Route::get('logout', [AuthController::class, 'logout'])->middleware('auth');
// Route Register
Route::get('register', [AuthController::class, 'register']);
Route::post('register', [AuthController::class, 'store']);
// Route Reset Password
Route::get('forgot-password', [AuthController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('forgot-password', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');

// Protected Dosen Routes
Route::middleware(['auth'])->group(function () {
   // Menampilkan daftar kegiatan untuk semua dosen
   Route::get('/kegiatan', [KegiatanController::class, 'index'])->name('kegiatan.index');

   // Route CRUD khusus PIC
   Route::middleware(['role:pic'])->group(function () {
       Route::post('/kegiatan/{id}/agenda', [KegiatanController::class, 'storeAgenda'])->name('agenda.store');
       Route::put('/kegiatan/{id}/agenda/{agendaId}', [KegiatanController::class, 'updateAgenda'])->name('agenda.update');
       Route::delete('/kegiatan/{id}/agenda/{agendaId}', [KegiatanController::class, 'destroyAgenda'])->name('agenda.destroy');
       
       Route::post('/kegiatan/{id}/anggota', [KegiatanController::class, 'storeAnggota'])->name('anggota.store');
       Route::delete('/kegiatan/{id}/anggota/{anggotaId}', [KegiatanController::class, 'destroyAnggota'])->name('anggota.destroy');
   });
});