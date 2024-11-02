<?php

namespace App\Http\Controllers;

use App\Models\Vote;
use App\Models\Cadidate;
use App\Exports\VoteExport;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Exp;

class VoteController extends Controller
{
    public function index(): JsonResponse
    {
        $params = request()->all();
        $search = isset($params['search']) ? $params['search'] : '';
        $cadidate = Vote::with(['cadidate', 'user'])->whereHas('user', function ($query) use ($search) {
            $query->whereRaw('LOWER(nis) LIKE ?', ['%' . strtolower($search) . '%'])
                ->orWhereRaw('LOWER(username) LIKE ?', ['%' . strtolower($search) . '%'])
                ->orWhereRaw('LOWER(nama) LIKE ?', ['%' . strtolower($search) . '%']);
        })
            ->paginate(10);

        return response()->json([
            'status' => 'success',
            'vote' => $cadidate,
        ]);
    }

    public function indexVote(): JsonResponse
    {
        $cadidate = Cadidate::all();

        return response()->json([
            'status' => 'success',
            'cadidate' => $cadidate,
        ]);
    }

    public function publicVote(): JsonResponse
    {
        //return all candidate with vote count and percentage
        $cadidate = Cadidate::withCount('votes')->get();
        $totalVote = Vote::count();

        if ($totalVote == 0) {
            foreach ($cadidate as $key => $value) {
                $cadidate[$key]['percentage'] = 0;
            }
        } else {
            foreach ($cadidate as $key => $value) {
                $cadidate[$key]['percentage'] = $value->votes_count / $totalVote * 100;
            }
        }

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

        //check if user already vote
        $vote = Vote::where('user_id', auth()->user()->id)->first();
        if ($vote) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda sudah memilih',
            ], 400);
        }

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

    public function export()
    {
        //get from query param
        $encrypt = request()->get('encrypt') == 'true' ? true : false;

        return Excel::download(new VoteExport($encrypt), 'vote.xlsx');
    }
}
