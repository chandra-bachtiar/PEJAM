<?php

namespace App\Exports;

use App\Models\Vote;
use Maatwebsite\Excel\Concerns\FromCollection;

class VoteExport implements FromCollection
{
    protected $encrypt;

    public function __construct($encrypt)
    {
        $this->encrypt = $encrypt;
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // export nama, nis, kelas, jurusan, vote, status, waktu vote
        $vote = Vote::with(['user', 'cadidate'])->get();

        $data = [];
        foreach ($vote as $key => $value) {
            $data[$key]['nama'] = $this->encrypt ? encrypt($value->user->nama) : $value->user->nama;
            $data[$key]['nis'] = $this->encrypt ? encrypt($value->user->nis) : $value->user->nis;
            $data[$key]['kelas'] = $value->user->kelas;
            $data[$key]['jurusan'] = $value->user->jurusan;
            $data[$key]['vote'] = $value->cadidate->ketua . ' - ' . $value->cadidate->wakil;
            $data[$key]['status'] = $value->user->status;
            $data[$key]['waktu_vote'] = $value->created_at;
        }

        return collect($data);
    }
}
