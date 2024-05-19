<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Models\User;

class UserController extends Controller
{
    public function __construct()
    {
        // Middleware untuk memastikan hanya admin yang bisa mengakses metode ini
        $this->middleware(function ($request, $next) {
            if (Auth::check() && Auth::user()->role == 'admin') {
                return $next($request);
            }
            return redirect('/home')->with('error', 'Anda tidak memiliki akses ke halaman ini');
        })->except(['login', 'store', 'showAll', 'showByID', 'showByName', 'apiUpdate', 'apiDestroy']);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 422);
        }

        $validated = $validator->validated();

        if (Auth::attempt($validated)) {
            $user = Auth::user();
            $payload = [
                'sub' => $user->id,
                'name' => $user->name,
                'role' => $user->role,
                'iat' => Carbon::now()->timestamp,
                'exp' => Carbon::now()->timestamp + 60 * 60 * 2,
            ];

            $token = JWT::encode($payload, env('JWT_SECRET_KEY'), 'HS256');

            return response()->json([
                'msg' => 'token berhasil dibuat',
                'data' => 'Bearer ' . $token
            ], 200);
        } else {
            return response()->json([
                'msg' => 'Email atau Password Salah',
            ], 422);
        }
    }

    public function index()
    {
        $users = User::all();
        return view('users.index', compact('users'));
    }

    public function edit($id)
    {
        $user = User::find($id);

        if (!$user) {
            return redirect()->route('users.index')->with('error', 'User tidak ditemukan');
        }

        return view('users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $id,
            'password' => 'nullable|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user = User::find($id);

        $user->name = $request->input('name');
        $user->email = $request->input('email');

        if ($request->filled('password')) {
            $user->password = Hash::make($request->input('password'));
        }

        $user->save();

        return redirect()->route('users.index')->with('success', 'User berhasil diupdate');
    }

    public function destroy($id)
    {
        $user = User::find($id);

        if ($user) {
            $user->delete();
            return redirect()->route('users.index')->with('success', 'User berhasil dihapus');
        }

        return redirect()->route('users.index')->with('error', 'User tidak ditemukan');
    }

    // API Methods
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role' => 'required|in:user,admin',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 422);
        }

        $payload = $validator->validated();

        $user = User::create([
            'name' => $payload['name'],
            'email' => $payload['email'],
            'password' => Hash::make($payload['password']),
            'role' => $payload['role']
        ]);

        return response()->json([
            'msg' => 'Data User Berhasil Disimpan',
            'data' => $user
        ], 201);
    }

    public function showAll()
    {
        $users = User::all();

        return response()->json([
            'msg' => 'Semua Data User',
            'data' => $users
        ], 200);
    }

    public function showByID($id)
    {
        $user = User::find($id);

        if ($user) {
            return response()->json([
                'msg' => 'User Dengan ID: ' . $id,
                'data' => $user
            ], 200);
        }

        return response()->json([
            'msg' => 'User Dengan ID: ' . $id . ' Tidak Ditemukan'
        ], 404);
    }

    public function showByName($name)
    {
        $users = User::where('name', 'LIKE', '%' . $name . '%')->get();

        if ($users->count() > 0) {
            return response()->json([
                'msg' => 'Ditemukan User Dengan Nama : ' . $name,
                'data' => $users
            ], 200);
        }

        return response()->json([
            'msg' => 'Tidak ada User dengan Nama : ' . $name . ' Ditemukan'
        ], 404);
    }

    public function apiUpdate(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|max:255',
            'email' => 'sometimes|required|email|unique:users,email,' . $id,
            'password' => 'sometimes|nullable|min:6',
            'role' => 'sometimes|required|in:user,admin',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 422);
        }

        $payload = $validator->validated();

        $user = User::find($id);

        if ($user) {
            if (isset($payload['password'])) {
                $payload['password'] = Hash::make($payload['password']);
            } else {
                unset($payload['password']);
            }
            $user->update($payload);

            return response()->json([
                'msg' => 'Data User Berhasil Dirubah',
                'data' => $user
            ], 200);
        }

        return response()->json([
            'msg' => 'Data User dengan ID: ' . $id . ' Tidak Ditemukan'
        ], 404);
    }

    public function apiDestroy($id)
    {
        $user = User::find($id);

        if ($user) {
            $user->delete();

            return response()->json([
                'msg' => 'Data User Berhasil Dihapus'
            ], 200);
        }

        return response()->json([
            'msg' => 'User with ID: ' . $id . ' not found'
        ], 404);
    }
}
