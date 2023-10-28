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
        $user = User::select('nis', 'username', 'nama', 'jenis_kelamin', 'jurusan', 'kelas', 'role', 'status', 'image')
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
        $user = User::where('role', 'user')->find($id);
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
        $user = User::where('role', 'user')->find($id);
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found'
            ], 404);
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = 'user';
        $user->phone_number = $request->phone_number;
        if ($request->password != null || $request->password != '') {
            $user->password = bcrypt($request->password);
        }

        if ($request->hasFile('image')) {
            $imageName = $user->id . '.' . $request->image->extension();
            $image = $request->file('image');
            $image->move(storage_path('app/public/images/user'), $imageName);
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
