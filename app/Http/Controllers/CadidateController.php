<?php

namespace App\Http\Controllers;

use App\Models\Cadidate;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CadidateController extends Controller
{
    public function index(): JsonResponse
    {
        $params = request()->all();
        $search = isset($params['search']) ? $params['search'] : '';

        $cadidate = Cadidate::select('id', 'ketua', 'wakil', 'image', 'description')
            ->where(function ($query) use ($search) {
                $query->whereRaw('LOWER(ketua) LIKE ?', ['%' . strtolower($search) . '%'])
                    ->orWhereRaw('LOWER(wakil) LIKE ?', ['%' . strtolower($search) . '%']);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'status' => 'success',
            'cadidate' => $cadidate,
        ]);
    }

    public function show($id): JsonResponse
    {
        $cadidate = Cadidate::find($id);
        if (!$cadidate) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cadidate not found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'cadidate' => $cadidate,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'ketua' => 'required|string',
            'wakil' => 'required|string',
            'description' => 'nullable|string',
        ]);

        $imageName = null;
        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $image = $request->file('image');
            $image->move(storage_path('app/public/images/kadidat'), $imageName);
            $imageName = '/storage/images/kadidat/' . $imageName;
        }

        $cadidate = Cadidate::create([
            'ketua' => $request->ketua,
            'wakil' => $request->wakil,
            'image' => $imageName,
            'description' => $request->description,
        ]);

        return response()->json([
            'message' => 'Success',
            'data' => $cadidate,
        ]);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $request->validate([
            'ketua' => 'required|string',
            'wakil' => 'required|string',
            'description' => 'nullable|string',
        ]);

        $cadidate = Cadidate::find($id);
        if (!$cadidate) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cadidate not found',
            ], 404);
        }

        $imageName = $cadidate->image;
        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $image = $request->file('image');
            $image->move(storage_path('app/public/images/kadidat'), $imageName);
            $imageName = '/storage/images/kadidat/' . $imageName;
        }

        $cadidate->update([
            'ketua' => $request->ketua,
            'wakil' => $request->wakil,
            'image' => $imageName,
            'description' => $request->description,
        ]);

        return response()->json([
            'message' => 'Success',
            'data' => $cadidate,
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $cadidate = Cadidate::find($id);
        if (!$cadidate) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cadidate not found',
            ], 404);
        }

        $cadidate->delete();
        return response()->json([
            'message' => 'Success',
        ]);
    }
}
