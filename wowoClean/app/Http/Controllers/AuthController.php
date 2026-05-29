<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{

    /**
 * @OA\Post(
 *     path="/api/v1/login",
 *     summary="Login User",
 *     tags={"Authentication"},
 *
 *     @OA\RequestBody(
 *         required=true,
 *
 *         @OA\JsonContent(
 *             required={"email","password"},
 *
 *             @OA\Property(
 *                 property="email",
 *                 type="string",
 *                 example="admin@gmail.com"
 *             ),
 *
 *             @OA\Property(
 *                 property="password",
 *                 type="string",
 *                 example="12345678"
 *             )
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="Login berhasil"
 *     ),
 *
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized"
 *     )
 * )
 */
    // LOGIN
    public function login(Request $request)
    {
        $credentials = $request->only(
            'email',
            'password'
        );

        // LOGIN CHECK
        if (!$token = auth()->attempt($credentials)) {

            return response()->json([
                'message' => 'Email atau password salah'
            ], 401);
        }

        return response()->json([
            'message' => 'Login berhasil',

            'token' => $token,

            'user' => auth()->user()
        ]);
    }

    // PROFILE
    public function profile()
    {
        return response()->json(
            auth()->user()
        );
    }

    // LOGOUT
     
    public function logout()
    {
        auth()->logout();

        return response()->json([
            'message' => 'Logout berhasil'
        ]);
    }
}