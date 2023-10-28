<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    public function index(): JsonResponse
    {
        $params = request()->all();
        $search = isset($params['search']) ? $params['search'] : '';
        $user = User::select('id', 'nis', 'username', 'nama', 'jenis_kelamin', 'jurusan', 'kelas', 'role', 'status', 'image')
            ->where(function ($query) use ($search) {
                $query->where('nis', 'like', '%' . $search . '%')
                    ->orWhere('username', 'like', '%' . $search . '%')
                    ->orWhere('nama', 'like', '%' . $search . '%')
                    ->orWhere('jenis_kelamin', 'like', '%' . $search . '%')
                    ->orWhere('jurusan', 'like', '%' . $search . '%')
                    ->orWhere('kelas', 'like', '%' . $search . '%')
                    ->orWhere('role', 'like', '%' . $search . '%')
                    ->orWhere('status', 'like', '%' . $search . '%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        return response()->json([
            'status' => 'success',
            'user' => $user
        ]);
    }

    public function show($id): JsonResponse
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found'
            ], 404);
        }
        return response()->json([
            'status' => 'success',
            'user' => $user
        ]);
    }

    public function store(Request $request): JsonResponse
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

        return response()->json([
            'status' => 'success',
            'user' => $user,
            'message' => 'User created successfully'
        ]);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found'
            ], 404);
        }

        $user->nis = $request->nis;
        $user->username = $request->username;
        $user->nama = $request->nama;
        $user->jenis_kelamin = $request->jenis_kelamin;
        $user->jurusan = $request->jurusan;
        $user->kelas = $request->kelas;
        $user->role = $request->role;
        $user->status = $request->status;
        $user->is_active = $request->is_active;

        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $image = $request->file('image');
            $image->move(storage_path('app/public/images/user'), $imageName);
            $imageName = '/storage/images/user/' . $imageName;
            $user->image = $imageName;
        }

        $user->save();
        return response()->json([
            'status' => 'success',
            'user' => $user,
            'message' => 'User updated successfully'
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $user = User::where('role', 'user')->find($id);
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found'
            ], 404);
        }

        $user->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'User deleted successfully'
        ]);
    }
}
