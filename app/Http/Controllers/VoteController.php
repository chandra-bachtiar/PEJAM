<?php

namespace App\Http\Controllers;

use App\Models\Vote;
use App\Models\Cadidate;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class VoteController extends Controller
{
    public function index(): JsonResponse
    {
        $cadidate = Vote::with('cadidate')->with('user')->paginate(10);

        return response()->json([
            'status' => 'success',
            'vote' => $cadidate,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'cadidate_id' => 'required|exists:cadidates,id',
        ]);

        $cadidate = Cadidate::find($request->cadidate_id);
        $cadidate->votes()->create([
            'user_id' => auth()->user()->id,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Berhasil memilih',
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

    public function checkVoteUser(): JsonResponse
    {
        $vote = Vote::where('user_id', auth()->user()->id)->first();
        if (!$vote) {
            return response()->json([
                'status' => 'success',
                'isVoted' => false,
                'message' => 'User belum memilih',
            ]);
        }

        return response()->json([
            'status' => 'success',
            'isVoted' => true,
            'message' => 'User sudah memilih',
        ]);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $cadidate = Cadidate::find($id);
        if (!$cadidate) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cadidate not found',
            ], 404);
        }

        $cadidate->update($request->all());

        return response()->json([
            'status' => 'success',
            'cadidate' => $cadidate,
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
            'status' => 'success',
            'message' => 'Cadidate deleted',
        ]);
    }
}
