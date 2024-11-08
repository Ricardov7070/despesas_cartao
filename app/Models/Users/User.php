<?php

namespace App\Models\Users;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'password',
        'type_users_id'
    ];


    public function searchUser ($id) {

        if ($id) {

            $userRating = DB::table('users as us')
                                ->join('type_users as tsu', 'tsu.id', '=', 'us.type_users_id')
                                ->where('us.id', $id)
                                ->whereNull('tsu.deleted_at')
                                ->whereNull('us.deleted_at')
                                ->select('tsu.description as description')
                                ->first();

            if (!empty($userRating) && $userRating->description == 'Administrator') {

                return DB::table('users as us')
                        ->join('type_users as tsu', 'tsu.id', '=', 'us.type_users_id')
                        ->whereNull('us.deleted_at')
                        ->whereNull('tsu.deleted_at')
                        ->select(
                            'us.id as id',
                            'us.name as user',
                            'tsu.description as classification',
                            'us.email as email',
                            DB::raw("DATE_FORMAT(us.created_at, '%d/%m/%Y') as creation")
                        )
                        ->get()
                        ->ToArray();

            } else {

                return DB::table('users as us')
                        ->join('type_users as tsu', 'tsu.id', '=', 'us.type_users_id')
                        ->where('us.id', $id)
                        ->whereNull('us.deleted_at')
                        ->whereNull('tsu.deleted_at')
                        ->select(
                            'us.id as id',
                            'us.name as user',
                            'tsu.description as classification',
                            'us.email as email',
                            DB::raw("DATE_FORMAT(us.created_at, '%d/%m/%Y') as creation")
                        )
                        ->get()
                        ->ToArray();

            }
           
        } else {

            return DB::table('users as us')
                        ->join('type_users as tsu', 'tsu.id', '=', 'us.type_users_id')
                        ->whereNull('us.deleted_at')
                        ->whereNull('tsu.deleted_at')
                        ->select(
                            'us.id as id',
                            'us.name as user',
                            'tsu.description as classification',
                            'us.email as email',
                            DB::raw("DATE_FORMAT(us.created_at, '%d/%m/%Y') as creation")
                        )
                        ->get()
                        ->ToArray();

        }

    }


    public function userValidation ($request) {

        return DB::table('users')
                    ->where('name', $request->input('name'))
                    ->where('email', $request->input('email'))
                    ->whereNull('deleted_at')
                    ->get()
                    ->toArray();

    }


    public function userEmailValidation ($request) {

        return DB::table('users')
                    ->where('email', $request->input('email'))
                    ->whereNull('deleted_at')
                    ->get()
                    ->toArray();

    }


    public function insertUpdateUser ($request, $id) {

        if ($id) {

            DB::table('users')
                ->where('id', $id) 
                ->whereNull('deleted_at')
                ->update([
                    'name' => $request->input('name'),
                    'email' => $request->input('email'),
                    'password' => bcrypt($request->input('password')),
                    'type_users_id' => $request->input('type'),
                    'updated_at' => now()
                ]);

        } else {

            DB::table('users')
                ->insert([
                    'name' => $request->input('name'),
                    'email' => $request->input('email'),
                    'password' => bcrypt($request->input('password')),
                    'type_users_id' => $request->input('type'),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

        }

    }


    public function deleteUser($id) {
        
        $idCards = DB::table('cards_users')
            ->select('id')
            ->where('users_id', $id)
            ->whereNull('deleted_at')
            ->get()
            ->map(function ($card) {
                return $card->id;
            })
            ->toArray();


        DB::table('users')
            ->where('id', $id)
            ->whereNull('deleted_at')
            ->update([
                'deleted_at' => now()
            ]);

        DB::table('cards_users')
            ->where('users_id', $id)
            ->whereNull('deleted_at')
            ->update([
                'deleted_at' => now()
            ]);

        DB::table('expenses_users')
            ->whereIn('cards_users_id', $idCards)
            ->whereNull('deleted_at')
            ->update([
                'deleted_at' => now()
            ]);

    }

}
