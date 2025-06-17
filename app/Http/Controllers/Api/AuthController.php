<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
     /**
     * @OA\Post(
     * path="/api/login",
     * tags={"Autentikasi"},
     * summary="Login Pengguna",
     * description="Mengautentikasi pengguna dan mengembalikan Bearer Token jika berhasil.",
     * @OA\RequestBody(
     * required=true,
     * description="Kredensial pengguna",
     * @OA\JsonContent(
     * required={"email", "password"},
     * @OA\Property(property="email", type="string", format="email", example="admin@mail.com"),
     * @OA\Property(property="password", type="string", format="password", example="password")
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="Login Berhasil",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Login success"),
     * @OA\Property(property="access_token", type="string", example="5|Abc..."),
     * @OA\Property(property="token_type", type="string", example="Bearer")
     * )
     * ),
     * @OA\Response(
     * response=401,
     * description="Unauthorized (Kredensial salah)"
     * )
     * )
     */
    /**
     * @OA\Post(
     * path="/api/register",
     * tags={"Autentikasi"},
     * summary="Registrasi User Baru",
     * description="Membuat akun pengguna baru dan langsung mengembalikan token.",
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"name", "email", "password", "password_confirmation"},
     * @OA\Property(property="name", type="string", example="User Tester"),
     * @OA\Property(property="email", type="string", format="email", example="tester@mail.com"),
     * @OA\Property(property="password", type="string", format="password", example="password"),
     * @OA\Property(property="password_confirmation", type="string", format="password", example="password")
     * )
     * ),
     * @OA\Response(response=201, description="Registrasi Berhasil"),
     * @OA\Response(response=422, description="Data tidak valid")
     * )
     */
    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 201);
    }
    /**
     * @OA\Post(
     * path="/api/login",
     * tags={"Autentikasi"},
     * summary="Login Pengguna",
     * description="Mengautentikasi pengguna dan mengembalikan Bearer Token jika berhasil.",
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"email","password"},
     * @OA\Property(property="email", type="string", format="email", example="admin@mail.com"),
     * @OA\Property(property="password", type="string", format="password", example="password")
     * )
     * ),
     * @OA\Response(response=200, description="Login Berhasil"),
     * @OA\Response(response=401, description="Unauthorized (Kredensial salah)")
     * )
     */

    public function login(Request $request)
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $user = User::where('email', $request['email'])->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login success',
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }
    /**
     * @OA\Post(
     * path="/api/logout",
     * tags={"Autentikasi"},
     * summary="Logout Pengguna",
     * description="Mencabut token yang sedang aktif. Memerlukan autentikasi.",
     * security={{"bearerAuth":{}}},
     * @OA\Response(response=200, description="Logout Berhasil"),
     * @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Successfully logged out']);
    }
}