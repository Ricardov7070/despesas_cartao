<?php

namespace App\Models\Users;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class typeUsers extends Model
{

    protected $table = 'type_users';

    protected $fillable = [
        'description',
    ];


    public function searchTypeUser () {

        return DB::table('type_users')
                    ->select('id as type', 'description')
                    ->whereNull('deleted_at')
                    ->get();

    }

}
