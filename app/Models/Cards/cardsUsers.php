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
        'balance_used',
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
                                ->first();

            if (!empty($userRating) && $userRating->description == 'Administrator') {

                $registrationSaved = DB::table('cards_users as csu')
                                        ->join('users as us', 'us.id', '=', 'csu.users_id')
                                        ->whereNull('us.deleted_at')
                                        ->whereNull('csu.deleted_at')
                                        ->select(
                                            DB::raw("CONCAT(us.id, ' - ', us.name) as user"),
                                            'csu.id as id_card',
                                            'csu.number as number_card',
                                            'csu.balance as balance',
                                            'csu.balance_used as balance_used'
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
                                            'csu.id as id_card',
                                            'csu.number as number_card',
                                            'csu.balance as balance',
                                            'csu.balance_used as balance_used'
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
                                        'csu.id as id_card',
                                        'csu.number as number_card',
                                        'csu.balance as balance',
                                        'csu.balance_used as balance_used'
                                    )
                                    ->get()
                                    ->toArray();

        }

        foreach ($registrationSaved as &$item) {

            $item->balance = number_format($item->balance, 2, ',', '.');
            $item->balance_used = number_format($item->balance_used, 2, ',', '.');

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
                                'csu.id as id'
                    )
                    ->get()
                    ->toArray();

    }


    public function insertUpdateCard ($request, $id) {

        if ($id) {

            DB::table('cards_users')
                ->where('id', $id) 
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
                    'balance_used' => 0,
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

        DB::table('expenses_users')
        ->where('cards_users_id', $id) 
        ->whereNull('deleted_at')
        ->update([
            'deleted_at' => now()
        ]);

    }


    public function validateExistenceCard ($id) {

        return DB::table('cards_users')
                ->where('id', $id)
                ->whereNull('deleted_at')
                ->get()
                ->toArray();

    }


    public function validateCardBalance ($request, $originalValues) {

        $cardBalance = DB::table('cards_users')
                        ->where('id', $request->input('id_card'))
                        ->where('balance', 0)
                        ->whereNull('deleted_at')
                        ->get()
                        ->toArray();

        if (!empty($cardBalance)) {

            return $cardBalance;

        } else {

            $cardValidate = DB::table('cards_users')
                        ->where('id', $request->input('id_card'))
                        ->whereNull('deleted_at')
                        ->get()
                        ->toArray();

            if ($originalValues) {

                if ($originalValues[0]->balance >= str_replace(',', '.', $request->input('expense'))) {

                    return [];
    
                } else {
    
                    return DB::table('cards_users')
                            ->where('id', $request->input('id_card'))
                            ->whereNull('deleted_at')
                            ->get()
                            ->toArray();
    
                }

            } else {

                if ($cardValidate[0]->balance >= str_replace(',', '.', $request->input('expense'))) {

                    return [];
    
                } else {
    
                    return DB::table('cards_users')
                            ->where('id', $request->input('id_card'))
                            ->whereNull('deleted_at')
                            ->get()
                            ->toArray();
    
                }

            }

        }

    }


    public function subtractBalance ($request, $cardUser) {

        $remainingBalance = $cardUser[0]->balance - str_replace(',', '.', $request->input('expense'));
        $balanceUsed = $cardUser[0]->balance_used + str_replace(',', '.', $request->input('expense'));

        DB::table('cards_users')
            ->where('id', $request->input('id_card'))
            ->whereNull('deleted_at')
            ->update(['balance' => $remainingBalance, 'balance_used' => $balanceUsed]);

    }


    public function resetBalance ($id) {

        return DB::table('expenses_users as esu')
                    ->join('cards_users as cds', 'cds.id', '=', 'esu.cards_users_id')
                    ->where('esu.id', $id)
                    ->whereNull('esu.deleted_at')
                    ->whereNull('cds.deleted_at')
                    ->select(
               DB::raw('cds.balance_used - esu.expense as balance_used'),
                        DB::raw('esu.expense + cds.balance as balance'),
                        'cds.id as id',
                    )
                    ->get()
                    ->toArray();

    }

}
