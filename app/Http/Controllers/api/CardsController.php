<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Models\Cards\cardsUsers;
use App\Models\Users\User;

class CardsController extends Controller
{

    protected $modelCardsUsers;
    protected $modelUsers;


    public function __construct (cardsUsers $modelCardsUsers, User $modelUsers) {

        $this->modelCardsUsers = $modelCardsUsers;
        $this->modelUsers = $modelUsers;
    
    }


    public function index () {

        $id = "";
        $cardData = $this->modelCardsUsers->searchCards($id);

        return empty($cardData) 
                ? response()->json(['alert' => 'No registration!'], 420)
                : $cardData;

    }


    public function store (Request $request) {

        try {

            $request->validate([
                'number' => 'required|regex:/^[0-9]+$/|digits:12',
                'balance' => 'required|regex:/^[0-9]+,[0-9]+$/',
                'user' => 'required|numeric', 
            ], [
                'number.regex' => 'The number field must contain only digits (no letters or special characters).',
                'number.digits' => 'The number field must contain exactly 12 digits.',
                'balance.regex' => 'The balance field must be a valid positive float number with a comma as a decimal separator.',
                'user.numeric' => 'The type field must contain only numbers.',
            ]);


            $existingUser = $this->modelUsers->searchUser($request->input('user'));

            if (empty($existingUser)) {

                return response()->json([
                    'warning' => 'Undefined User!',
                ], 425);

            } else {

                $cardValidation = $this->modelCardsUsers->cardValidation($request);

                if (!empty($cardValidation)) {

                    return response()->json([
                        'warning' => 'Card already registered for the user ' . $cardValidation[0]->user . '!',
                    ], 428);
            
                }


                $id = "";
                $this->modelCardsUsers->insertUpdateCard($request, $id);


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


    public function show ($id)  {
        
        $existingUser = $this->modelUsers->searchUser($id);

        if (empty($existingUser)) {

            return response()->json([
                'warning' => 'Undefined User!',
            ], 425);

        } else {

            $cardData = $this->modelCardsUsers->searchCards($id);

            return empty($cardData) 
                ? response()->json(['alert' => 'No registration!'], 420)
                : $cardData;

        }

    }

  
    public function update (Request $request, $id) {

        try {

            $existingCard = $this->modelCardsUsers->validateExistenceCard($id);

            if (empty($existingCard)) {

                return response()->json([
                    'warning' => 'Undefined Card!',
                ], 429);

            } else {

                $request->validate([
                    'number' => 'required|regex:/^[0-9]+$/|digits:12',
                    'balance' => 'required|regex:/^[0-9]+,[0-9]+$/',
                ], [
                    'number.regex' => 'The number field must contain only digits (no letters or special characters).',
                    'number.digits' => 'The number field must contain exactly 12 digits.',
                    'balance.regex' => 'The balance field must be a valid positive float number with a comma as a decimal separator.',
                ]);


                $cardValidation = $this->modelCardsUsers->cardValidation($request);

                if (!empty($cardValidation)) {

                    if ($id != $cardValidation[0]->id) {

                        return response()->json([
                            'warning' => 'Card already registered for the user ' . $cardValidation[0]->user . '!',
                        ], 428);

                    }
            
                }


                $this->modelCardsUsers->insertUpdateCard($request, $id);


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


    public function destroy ($id) {

        try {

            $existingCard = $this->modelCardsUsers->validateExistenceCard($id);

            if (empty($existingCard)) {

                return response()->json([
                    'warning' => 'Undefined Card!',
                ], 429);

            } else {

                $this->modelCardsUsers->deleteCard($id);


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
