<?php

namespace App\Imports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;

class UsersImport implements ToModel
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $user = User::updateOrCreate(
            [
                'nis' => $row[0],
            ],
            [
                'username' => $row[1],
                'nama' => $row[2],
                'jenis_kelamin' => $row[3],
                'jurusan' => $row[4],
                'kelas' => $row[5],
                'role' => $row[6],
                'status' => $row[7],
                'password' => bcrypt($row[8]),
                'is_active' => true,
            ]
        );

        return $user;
    }
}
