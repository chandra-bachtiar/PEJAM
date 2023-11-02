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
        $isNotVoted = isset($params['is_not_voted']) ? $params['is_not_voted'] : false;
        $role = isset($params['role']) ? $params['role'] : '';
        if ($isNotVoted == "false") {
            $user = User::with('vote')
                ->where(function ($query) use ($search) {
                    $query->whereRaw('LOWER(nis) LIKE ?', ['%' . strtolower($search) . '%'])
                        ->orWhereRaw('LOWER(username) LIKE ?', ['%' . strtolower($search) . '%'])
                        ->orWhereRaw('LOWER(nama) LIKE ?', ['%' . strtolower($search) . '%']);
                })
                ->where(function ($query) use ($role) {
                    if ($role != '') {
                        $roleActual = $role == 'Semua' ? '' : $role;
                        $query->whereRaw('LOWER(role) LIKE ?', ['%' . strtolower($roleActual) . '%']);
                    }
                })
                ->orderBy('created_at', 'desc')
                ->paginate(10);
            return response()->json([
                'status' => 'success',
                'user' => $user
            ]);
        } else {
            $user = User::with('vote')
                ->where(function ($query) use ($search) {
                    $query->whereRaw('LOWER(nis) LIKE ?', ['%' . strtolower($search) . '%'])
                        ->orWhereRaw('LOWER(username) LIKE ?', ['%' . strtolower($search) . '%'])
                        ->orWhereRaw('LOWER(nama) LIKE ?', ['%' . strtolower($search) . '%']);
                })
                ->where(function ($query) use ($role) {
                    if ($role != '') {
                        $roleActual = $role == 'Semua' ? '' : $role;
                        $query->whereRaw('LOWER(role) LIKE ?', ['%' . strtolower($roleActual) . '%']);
                    }
                })
                ->whereDoesntHave('vote')
                ->orderBy('created_at', 'desc')
                ->paginate(10);
            return response()->json([
                'status' => 'success',
                'user' => $user
            ]);
        }
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
        $user->is_active = $request->is_active == true ? 1 : 0;

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
        $user = User::find($id);
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
