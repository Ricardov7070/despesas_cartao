<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Users\User;
use App\Models\Expenses\ExpensesUsers;
use Illuminate\Validation\ValidationException;
use App\Models\Cards\cardsUsers;

class ExpensesController extends Controller
{

    protected $modelExpensesUsers;
    protected $modelUsers;
    protected $modelCardsUsers;


    public function __construct (ExpensesUsers $modelExpensesUsers, User $modelUsers, cardsUsers $modelCardsUsers) {

        $this->modelExpensesUsers = $modelExpensesUsers;
        $this->modelUsers = $modelUsers;
        $this->modelCardsUsers = $modelCardsUsers;
    
    }


    /**
     * @OA\Get(
     *     path="/api/expenses_users",
     *     summary="Lista todas as despesas cadastradas no sistema.",
     *     tags={"Despesas"},
     *     @OA\Response(
     *         response=420,
     *         description="No registration!"
     *     ),
     * )
     */
    public function index () {

        $id = "";
        $expensesData = $this->modelExpensesUsers->searchExpenses($id);

        return empty($expensesData) 
                ? response()->json(['alert' => 'No registration!'], 420)
                : $expensesData;

    }


    /**
     * @OA\Post(
     *     path="/api/expenses_users/",
     *     summary="Insere na base de dados os registros da nova despesa.",
     *     tags={"Despesas"},
     *     @OA\Response(
     *         response=200,
     *         description="Successfully Done!"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Unexpected error!"
     *     ),
     *     @OA\Response(
     *         response=424,
     *         description="Request error, validate the data and try again!"
     *     ),
     *      @OA\Response(
     *         response=429,
     *         description="Undefined Card!"
     *     ),
     *     @OA\Response(
     *         response=430,
     *         description="It is not possible to enter this expense because the ID card balance ('') is insufficient!"
     *     ),
     *     @OA\Response(
     *         response=431,
     *         description="It is not possible to enter an expense on this ID card ('') because its balance is zero!"
     *     ),
     * )
     */
    public function store (Request $request) {

        try {

            $request->validate([
                'id_card' => 'required|numeric',
                'description' => 'required',
                'expense' => 'required|regex:/^[0-9]+,[0-9]+$/',
            ], [
                'id_card.regex' => 'The number field must contain only digits (no letters or special characters).',
                'description.required' => 'The description field is mandatory.',
                'expense.regex' => 'The expense field must be a valid positive float number with a comma as a decimal separator.',
            ]);


            $existingCard = $this->modelCardsUsers->validateExistenceCard($request->input('id_card'));

            if (empty($existingCard)) {

                return response()->json([
                    'warning' => 'Undefined Card!',
                ], 429);

            } else {

                $originalValues = "";
                $validateCardBalance = $this->modelCardsUsers->validateCardBalance($request, $originalValues);

                if (!empty($validateCardBalance)) {

                    if ($validateCardBalance[0]->balance != 0) {

                        return response()->json([
                            'warning' => 'It is not possible to enter this expense because the ID card balance ('.$validateCardBalance[0]->id.') is insufficient!',
                        ], 430);

                    } else {

                        return response()->json([
                            'warning' => 'It is not possible to enter an expense on this ID card ('.$validateCardBalance[0]->id.') because its balance is zero!',
                        ], 431);

                    }
            
                }

                
                $id = "";
                $this->modelExpensesUsers->insertUpdateExpense($request, $id);
                $this->modelCardsUsers->subtractBalance($request, $existingCard);


                $subject = 'Eviction Generated Successfully!';
                $this->modelExpensesUsers->validateEmailSending($request->input('id_card'), $subject);


                return response()->json([
                    'success' => 'Successfully Done!',
                ], 200);

            }

        } catch (ValidationException $e) {

            return response()->json([
                'alert' => 'Request error, validate the data and try again!',
                'errors' => $e->errors(),
            ], 424);

        } catch (\Throwable $th) {

            return response()->json([
                'error' => 'Unexpected error!',
            ], 500);

        }

    }


    /**
     * @OA\Get(
     *     path="/api/expenses_users/{id_user}",
     *     summary="Lista todas as despesas, somente do usuário selecionado.",
     *     tags={"Despesas"},
     *     @OA\Response(
     *         response=420,
     *         description="No registration!"
     *     ),
     *      @OA\Response(
     *         response=425,
     *         description="Undefined User!"
     *     ),
     * )
     */
    public function show ($id)  {
        
        $existingUser = $this->modelUsers->searchUser($id);

        if (empty($existingUser)) {

            return response()->json([
                'warning' => 'Undefined User!',
            ], 425);

        } else {

            $expensesData = $this->modelExpensesUsers->searchExpenses($id);

            return empty($expensesData) 
                ? response()->json(['alert' => 'No registration!'], 420)
                : $expensesData;

        }

    }

  
    /**
     * @OA\Put(
     *     path="/api/expenses_users/{id_expense}",
     *     summary="Atualiza os dados somente da despesa selecionada já cadastrada.",
     *     tags={"Despesas"},
     *     @OA\Response(
     *         response=200,
     *         description="Successfully Done!"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Unexpected error!"
     *     ),
     *     @OA\Response(
     *         response=424,
     *         description="Request error, validate the data and try again!"
     *     ),
     *     @OA\Response(
     *         response=430,
     *         description="It is not possible to enter this expense because the ID card balance ('') is insufficient!"
     *     ),
     *     @OA\Response(
     *         response=431,
     *         description="It is not possible to enter an expense on this ID card ('') because its balance is zero!"
     *     ),
     *     @OA\Response(
     *         response=432,
     *         description="Undefined Expense!"
     *     ),
     *      @OA\Response(
     *         response=433,
     *         description="This expense does not refer to this card ID!"
     *     ),
     * )
     */
    public function update (Request $request, $id) {

        try {

            $request->validate([
                'id_card' => 'required|numeric',
                'description' => 'required',
                'expense' => 'required|regex:/^[0-9]+,[0-9]+$/',
            ], [
                'id_card.regex' => 'The number field must contain only digits (no letters or special characters).',
                'description.required' => 'The description field is mandatory.',
                'expense.regex' => 'The expense field must be a valid positive float number with a comma as a decimal separator.',
            ]);


            $existingExpense = $this->modelExpensesUsers->validateExpense($id);

            if (empty($existingExpense)) {

                return response()->json([
                    'warning' => 'Undefined Expense!',
                ], 432);

            } else {

                if ($existingExpense[0]->cards_users_id != $request->input('id_card')) {

                    return response()->json([
                        'warning' => 'This expense does not refer to this card ID!',
                    ], 433);

                }


                $originalValues = $this->modelCardsUsers->resetBalance($id);
                $validateCardBalance = $this->modelCardsUsers->validateCardBalance($request, $originalValues);

                if (!empty($validateCardBalance)) {

                    if ($validateCardBalance[0]->balance != 0) {

                        return response()->json([
                            'warning' => 'It is not possible to enter this expense because the ID card balance ('.$validateCardBalance[0]->id.') is insufficient!',
                        ], 430);

                    } else {

                        return response()->json([
                            'warning' => 'It is not possible to enter an expense on this ID card ('.$validateCardBalance[0]->id.') because its balance is zero!',
                        ], 431);

                    }
            
                }


                $this->modelExpensesUsers->insertUpdateExpense($request, $id);
                $this->modelCardsUsers->subtractBalance($request, $originalValues);


                $subject = 'Expense id ("' .$id. '") updated successfully!';
                $this->modelExpensesUsers->validateEmailSending($request->input('id_card'), $subject);


                return response()->json([
                    'success' => 'Successfully Done!',
                ], 200);

            }

        } catch (ValidationException $e) {

            return response()->json([
                'alert' => 'Request error, validate the data and try again!',
                'errors' => $e->errors(),
            ], 424);

        } catch (\Throwable $th) {

            return response()->json([
                'error' => 'Unexpected error!',
            ], 500);

        }
        
    }


    /**
     * @OA\Delete(
     *     path="/api/expenses_users/{id_expense}",
     *     summary="Exclui na base de dados os registros da despesa selecionada.",
     *     tags={"Despesas"},
     *     @OA\Response(
     *         response=200,
     *         description="Successfully Done!"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Unexpected error!"
     *     ),
     *     @OA\Response(
     *         response=424,
     *         description="Request error, validate the data and try again!"
     *     ),
     *      @OA\Response(
     *         response=432,
     *         description="Undefined Expense!"
     *     ),
     * )
     */
    public function destroy ($id) {

        try {

            $existingExpense = $this->modelExpensesUsers->validateExpense($id);

            if (empty($existingExpense)) {

                return response()->json([
                    'warning' => 'Undefined Expense!',
                ], 432);

            } else {

                $originalValues = $this->modelCardsUsers->resetBalance($id);
                $this->modelExpensesUsers->deleteExpense($id, $originalValues);


                $subject = 'Expense ID ("' .$id. '") successfully deleted!';
                $this->modelExpensesUsers->validateEmailSending($originalValues[0]->id, $subject);


                return response()->json([
                    'success' => 'Successfully Done!',
                ], 200);

            }

        } catch (ValidationException $e) {

            return response()->json([
                'alert' => 'Request error, validate the data and try again!',
                'errors' => $e->errors(),
            ], 424);

        } catch (\Throwable $th) {

            return response()->json([
                'error' => 'Unexpected error!',
            ], 500);

        }

    }

}
