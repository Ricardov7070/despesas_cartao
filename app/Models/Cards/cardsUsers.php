<?php

namespace App\Models\Cards;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class cardsUsers extends Model
{
    
    protected $table = 'cards_users';

    protected $fillable = [
        'users_id',
        'number',
        'balance',
    ];


    public function searchCards ($id) {

        $registrationSaved = [];

        if ($id) {

            $userRating = DB::table('users as us')
                                ->join('type_users as tsu', 'tsu.id', '=', 'us.type_users_id')
                                ->where('us.id', $id)
                                ->whereNull('tsu.deleted_at')
                                ->whereNull('us.deleted_at')
                                ->select('tsu.description as description')
                                ->get();

            if ($userRating[0]->description == 'Administrator') {

                $registrationSaved = DB::table('cards_users as csu')
                                        ->join('users as us', 'us.id', '=', 'csu.users_id')
                                        ->whereNull('us.deleted_at')
                                        ->whereNull('csu.deleted_at')
                                        ->select(
                                            DB::raw("CONCAT(us.id, ' - ', us.name) as user"),
                                            'csu.number as number_card',
                                            'csu.balance as balance'
                                        )
                                        ->get()
                                        ->toArray();
                                
            } else {

                $registrationSaved = DB::table('cards_users as csu')
                                        ->join('users as us', 'us.id', '=', 'csu.users_id')
                                        ->where('us.id', $id)
                                        ->whereNull('us.deleted_at')
                                        ->whereNull('csu.deleted_at')
                                        ->select(
                                            DB::raw("CONCAT(us.id, ' - ', us.name) as user"),
                                            'csu.number as number_card',
                                            'csu.balance as balance'
                                        )
                                        ->get()
                                        ->toArray();

            }         
            
        } else {

            $registrationSaved = DB::table('cards_users as csu')
                                    ->join('users as us', 'us.id', '=', 'csu.users_id')
                                    ->whereNull('us.deleted_at')
                                    ->whereNull('csu.deleted_at')
                                    ->select(
                                        DB::raw("CONCAT(us.id, ' - ', us.name) as user"),
                                        'csu.number as number_card',
                                        'csu.balance as balance'
                                    )
                                    ->get()
                                    ->toArray();

        }

        foreach ($registrationSaved as &$item) {

            $item->balance = number_format($item->balance, 2, ',', '.');
     
        }

        return $registrationSaved;

    }


    public function cardValidation ($request) {

        return DB::table('cards_users as csu')
                    ->join('users as us', 'us.id', '=', 'csu.users_id')
                    ->where('csu.number', $request->input('number'))
                    ->whereNull('us.deleted_at')
                    ->whereNull('csu.deleted_at')
                    ->select(
                        DB::raw("CONCAT(us.id, ' - ', us.name) as user"),
                    )
                    ->get()
                    ->toArray();

    }


    public function insertUpdateCard ($request, $id) {

        if ($id) {

            DB::table('cards_users')
                ->where('users_id', $id) 
                ->whereNull('deleted_at')
                ->update([
                    'number' => $request->input('number'),
                    'balance' => str_replace(',', '.', $request->input('balance')),
                    'updated_at' => now()
                ]);
            
        } else {

            DB::table('cards_users')
                ->insert([
                    'users_id' => $request->input('user'),
                    'number' => $request->input('number'),
                    'balance' => str_replace(',', '.', $request->input('balance')),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

        }

    }


    public function deleteCard ($id) {

        DB::table('cards_users')
        ->where('id', $id) 
        ->whereNull('deleted_at')
        ->update([
            'deleted_at' => now()
        ]);

    }

}
