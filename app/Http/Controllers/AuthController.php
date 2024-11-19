<?php

namespace App\Http\Controllers;

use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login()
    {
        if (Auth::check()) {
            return redirect('/home');
        }
        return view('auth.login');
    }

    public function postlogin(Request $request)
    {
        try {
            // Validasi input
            $validator = Validator::make($request->all(), [
                'username' => 'required|string',
                'password' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Cek kredensial
            $user = UserModel::where('username', $request->username)->first();
            
            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Username atau password salah'
                ], 401);
            }

            // Login user
            Auth::login($user);

            // Set session
            if ($user->foto_profil) {
                session(['profile_img_path' => $user->foto_profil]);
            }
            session(['user_id' => $user->user_id]);
            session(['level_id' => $user->level_id]);

            return response()->json([
                'status' => true,
                'message' => 'Login Berhasil',
                'redirect' => url('/home'),
                'user' => $user->load('level') // Load relasi level
            ], 200);

        } catch (\Exception $e) {
            Log::error('Login Error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function register()
    {
        return view('auth.register');
    }

    public function postRegister(Request $request)
    {
        try {
            Log::info('Register Request:', $request->all());

            // Validasi input sesuai schema baru
            $validator = Validator::make($request->all(), [
                'username' => 'required|string|min:3|max:50|unique:m_user,username',
                'nama' => 'required|string|max:100',
                'password' => 'required|min:6',
                'email' => 'required|email|unique:m_user,email',
                'nip' => 'required|string|max:50|unique:m_user,nip',
                'foto_profil' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
            ], [
                'username.required' => 'Username wajib diisi',
                'username.unique' => 'Username sudah digunakan',
                'nama.required' => 'Nama wajib diisi',
                'email.required' => 'Email wajib diisi',
                'email.email' => 'Format email tidak valid',
                'email.unique' => 'Email sudah digunakan',
                'nip.required' => 'NIP wajib diisi',
                'nip.unique' => 'NIP sudah digunakan',
                'password.required' => 'Password wajib diisi',
                'password.min' => 'Password minimal 6 karakter',
                'foto_profil.image' => 'File harus berupa gambar',
                'foto_profil.mimes' => 'Format gambar harus jpeg, png, atau jpg',
                'foto_profil.max' => 'Ukuran gambar maksimal 2MB'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();
            
            try {
                $userData = [
                    'username' => $request->username,
                    'nama' => $request->nama,
                    'password' => Hash::make($request->password),
                    'email' => $request->email,
                    'nip' => $request->nip,
                    'level_id' => 3, // Default level DOSEN
                    'email_verified_at' => now() // Opsional, sesuai kebutuhan
                ];

                // Handle upload foto profil
                if ($request->hasFile('foto_profil')) {
                    $file = $request->file('foto_profil');
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $file->storeAs('public/profile_photos', $filename);
                    $userData['foto_profil'] = 'profile_photos/' . $filename;
                }

                $user = UserModel::create($userData);

                DB::commit();

                return response()->json([
                    'status' => true,
                    'message' => 'Register Berhasil',
                    'redirect' => url('/login'),
                    'user' => $user
                ], 201);

            } catch (\Exception $e) {
                DB::rollback();
                Log::error('Registration Error:', [
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e;
            }

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'detail' => env('APP_DEBUG') ? $e->getTraceAsString() : null
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            return response()->json([
                'status' => true,
                'message' => 'Logout berhasil',
                'redirect' => url('/login')
            ]);
            
        } catch (\Exception $e) {
            Log::error('Logout Error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat logout: ' . $e->getMessage()
            ], 500);
        }
    }
}