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

            return DB::table('users')
                        ->select(
                            'id',
                            'name as user',
                            'email',
                            'password',
                        DB::raw("DATE_FORMAT(created_at, '%d/%m/%Y') as creation")
                            )
                        ->where('id', $id)
                        ->whereNull('deleted_at')
                        ->get()
                        ->ToArray();
           
        } else {

            return DB::table('users')
                        ->select(
                            'id',
                            'name as user',
                            'email',
                            'password',
                        DB::raw("DATE_FORMAT(created_at, '%d/%m/%Y') as creation")
                            )
                        ->whereNull('deleted_at')
                        ->get();

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


    public function deleteUser ($id) {

        DB::table('users')
                ->where('id', $id) 
                ->whereNull('deleted_at')
                ->update([
                    'deleted_at' => now()
                ]);

    }

}
