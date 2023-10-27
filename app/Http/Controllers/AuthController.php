<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register', 'validateToken']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request): Object
    {
        $credentials = request(['username', 'password']);
        if (!$token = auth()->attempt($credentials)) {
            return response()->json([
                'errors' => [
                    'message' => 'Username atau kata sandi tidak valid'
                ]
            ], 401);
        }
        $user = auth()->user();
        return $this->respondWithToken($token, $user);
    }

    /**
     * Register a new user
     */
    public function register(Request $request): Object
    {
        $imageName = null;
        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $image = $request->file('image');
            $image->move(storage_path('app/public/images/user'), $imageName);
            $imageName = '/storage/images/user/' . $imageName;
        }

        $user = User::create([
            'nis' => $request->nis,
            'username' => $request->username,
            'nama' => $request->nama,
            'jenis_kelamin' => $request->jenis_kelamin,
            'jurusan' => $request->jurusan,
            'kelas' => $request->kelas,
            'role' => $request->role,
            'status' => $request->status,
            'image' => $imageName,
            'password' => bcrypt($request->password),
            'is_active' => true
        ]);

        return $this->respondWithToken(auth()->login($user), $user);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me(): Object
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(): Object
    {
        auth()->logout();
        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh(): Object
    {
        return $this->respondWithToken(auth()->refresh());
    }

    public function validateToken(): Object
    {
        //check if token is valid
        return response()->json(['valid' => auth()->check()]);
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token, $user = null): Object
    {
        if ($user) {
            return response()->json([
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth()->factory()->getTTL() * 60,
                'user' => $user
            ]);
        }
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
}
