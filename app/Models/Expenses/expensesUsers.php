<?php

namespace App\Models\Expenses;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Services\EmailService;

class expensesUsers extends Model
{
    protected $table = 'expenses_users';

    protected $fillable = [
        'cards_users_id',
        'description',
        'expense'
    ];


    protected $emailService;

    public function __construct (EmailService $emailService) {

        $this->emailService = $emailService;
    
    }


    public function searchExpenses ($id) {

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

                $registrationSaved = DB::table('users as us')
                                        ->join('cards_users as cds', 'us.id', '=', 'cds.users_id')
                                        ->join('expenses_users as esu', 'cds.id', '=', 'esu.cards_users_id')
                                        ->whereNull('us.deleted_at')
                                        ->whereNull('cds.deleted_at')
                                        ->whereNull('esu.deleted_at')
                                        ->select(
                                   DB::raw("CONCAT(us.id, ' - ', us.name) as user"),
                                            'esu.id as id_expense',
                                            'esu.description as description',
                                            'esu.expense as expense',
                                            'cds.id as id_card',
                                            'cds.number as number_card'
                                        )
                                        ->get()
                                        ->toArray();
              
            } else {

                $registrationSaved = DB::table('users as us')
                                        ->join('cards_users as cds', 'us.id', '=', 'cds.users_id')
                                        ->join('expenses_users as esu', 'cds.id', '=', 'esu.cards_users_id')
                                        ->where('us.id', $id)
                                        ->whereNull('us.deleted_at')
                                        ->whereNull('cds.deleted_at')
                                        ->whereNull('esu.deleted_at')
                                        ->select(
                                   DB::raw("CONCAT(us.id, ' - ', us.name) as user"),
                                            'esu.id as id_expense',
                                            'esu.description as description',
                                            'esu.expense as expense',
                                            'cds.id as id_card',
                                            'cds.number as number_card'
                                        )
                                        ->get()
                                        ->toArray();

            }         
            
        } else {

            $registrationSaved = DB::table('users as us')
                                    ->join('cards_users as cds', 'us.id', '=', 'cds.users_id')
                                    ->join('expenses_users as esu', 'cds.id', '=', 'esu.cards_users_id')
                                    ->whereNull('us.deleted_at')
                                    ->whereNull('cds.deleted_at')
                                    ->whereNull('esu.deleted_at')
                                    ->select(
                               DB::raw("CONCAT(us.id, ' - ', us.name) as user"),
                                        'esu.id as id_expense',
                                        'esu.description as description',
                                        'esu.expense as expense',
                                        'cds.id as id_card',
                                        'cds.number as number_card'
                                    )
                                    ->get()
                                    ->toArray();

        }

        foreach ($registrationSaved as &$item) {

            $item->expense = number_format($item->expense, 2, ',', '.');
     
        }

        return $registrationSaved;

    }


    public function insertUpdateExpense ($request, $id) {

        if ($id) {

            DB::table('expenses_users')
                ->where('id', $id) 
                ->whereNull('deleted_at')
                ->update([
                    'description' => $request->input('description'),
                    'expense' => str_replace(',', '.', $request->input('expense')),
                    'updated_at' => now()
                ]);

        } else {

            DB::table('expenses_users')
                ->insert([
                    'cards_users_id' => $request->input('id_card'),
                    'description' => $request->input('description'),
                    'expense' => str_replace(',', '.', $request->input('expense')),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

        }

    }


    public function validateExpense ($id) {

        return DB::table('expenses_users')
                    ->where('id', $id)
                    ->whereNull('deleted_at')
                    ->get()
                    ->toArray();
    
    }


    public function deleteExpense ($id, $cardUser) {

        DB::table('expenses_users')
        ->where('id', $id) 
        ->whereNull('deleted_at')
        ->update([
            'deleted_at' => now()
        ]);

        DB::table('cards_users')
            ->where('id', $cardUser[0]->id)
            ->whereNull('deleted_at')
            ->update(['balance' => $cardUser[0]->balance, 'balance_used' => $cardUser[0]->balance_used]);

    }


    public function validateEmailSending ($id, $subject) {

        $infoCard = DB::table('cards_users as cds')
                        ->join('users as us', 'us.id', '=', 'cds.users_id')
                        ->where('cds.id', $id)
                        ->whereNull('cds.deleted_at')
                        ->whereNull('us.deleted_at')
                        ->select(
                            'us.email as email',
                            'cds.number as number',
                            DB::raw("CONCAT(us.id, ' - ', us.name) as user")
                        )
                        ->get()
                        ->toArray();

        $emailsAdministrator = DB::table('type_users as tsu')
                                    ->join('users as us', 'tsu.id', '=', 'us.type_users_id')
                                    ->where('tsu.description', 'Administrator')
                                    ->whereNull('tsu.deleted_at')
                                    ->whereNull('us.deleted_at')
                                    ->select('us.email as email')
                                    ->get()
                                    ->toArray();

        $combinedEmails = array_merge($infoCard, $emailsAdministrator);
        $uniqueEmails = array_unique(array_column($combinedEmails, 'email'));

        $content = 'Expense card: ("' . $infoCard[0]->number . '")' . PHP_EOL . 
                   'Card user: ("' . $infoCard[0]->user . '")!';

        foreach ($uniqueEmails as $item) {

            $this->emailService->sendEmail($item, $subject, $content);

        }

    }

}
