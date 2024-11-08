<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Users\User;
use Illuminate\Validation\ValidationException;

class UsersController extends Controller
{

    protected $modelUsers;


    public function __construct (User $modelUsers) {

        $this->modelUsers = $modelUsers;
    
    }


    /**
     * @OA\Get(
     *     path="/api/users",
     *     summary="Lista todos os usuários cadastrados no sistema.",
     *     tags={"Usuários"},
     *     @OA\Response(
     *         response=420,
     *         description="No registration!"
     *     ),
     * )
     */
    public function index () {

        $id = "";
        $userData =  $this->modelUsers->searchUser($id);

        return empty($userData) 
                ? response()->json(['alert' => 'No registration!'], 420)
                : $userData;

    }


    /**
     * @OA\Post(
     *     path="/api/users/",
     *     summary="Insere na base de dados os registros do novo usuário.",
     *     tags={"Usuários"},
     *     @OA\Response(
     *         response=200,
     *         description="Successfully Done!"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Unexpected error!"
     *     ),
     *      @OA\Response(
     *         response=422,
     *         description="User already registered!"
     *     ),
     *     @OA\Response(
     *         response=423,
     *         description="Email already registered with another user!"
     *     ),
     *     @OA\Response(
     *         response=424,
     *         description="Request error, validate the data and try again!"
     *     ),
     * )
     */
    public function store (Request $request) {

        try {

            $request->validate([
                'name' => [
                    'required',
                    'regex:/^[a-zA-ZÀ-ÿ\s]+$/u',
                ],
                'email' => 'required|email',
                'password' => 'required',
                'type' => 'required|numeric', 
            ], [
                'name.regex' => 'The name field must contain only letters and spaces.',
                'email.email' => 'Please provide a valid email address.',
                'password.required' => 'The password field is mandatory.',
                'type.numeric' => 'The type field must contain only numbers.',
            ]);
            

            $userValidation = $this->modelUsers->userValidation($request);
            $userEmailValidation = $this->modelUsers->userEmailValidation($request);

            if (!empty($userValidation)) {

                return response()->json([
                    'warning' => 'User already registered!',
                ], 422);
           
            }

            if (!empty($userEmailValidation)) {

                return response()->json([
                    'warning' => 'Email already registered with another user!',
                ], 423);
           
            }


            $id = "";
            $this->modelUsers->insertUpdateUser($request, $id);


            return response()->json([
                'success' => 'Successfully Done!',
            ], 200);

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
     *     path="/api/users/{id_user}",
     *     summary="Lista os dados somente do usuário selecionado.",
     *     tags={"Usuários"},
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

            return empty($existingUser) 
                ? response()->json(['alert' => 'No registration!'], 420)
                : $existingUser;

        }

    }

  
    /**
     * @OA\Put(
     *     path="/api/users/{id_user}",
     *     summary="Atualiza os dados somente do usuário selecionado já cadastrado.",
     *     tags={"Usuários"},
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
     *         response=425,
     *         description="Undefined User!"
     *     ),
     *      @OA\Response(
     *         response=426,
     *         description="There is already a user with these credentials registered!"
     *     ),
     *     @OA\Response(
     *         response=427,
     *         description="There is already a user with this email registered!"
     *     ),
     * )
     */
    public function update (Request $request, $id) {

        try {

            $existingUser = $this->modelUsers->searchUser($id);

            if (empty($existingUser)) {

                return response()->json([
                    'warning' => 'Undefined User!',
                ], 425);

            } else {

                $request->validate([
                    'name' => [
                        'required',
                        'regex:/^[a-zA-ZÀ-ÿ\s]+$/u',
                    ],
                    'email' => 'required|email',
                    'password' => 'required',
                    'type' => 'required|numeric', 
                ], [
                    'name.regex' => 'The name field must contain only letters and spaces.',
                    'email.email' => 'Please provide a valid email address.',
                    'password.required' => 'The password field is mandatory.',
                    'type.numeric' => 'The type field must contain only numbers.',
                ]);


                $userValidation = $this->modelUsers->userValidation($request);
                $userEmailValidation = $this->modelUsers->userEmailValidation($request);

                if (!empty($userValidation)) {

                    if ($id != $userValidation[0]->id) {

                        return response()->json([
                            'warning' => 'There is already a user with these credentials registered!',
                        ], 426);

                    }
                    
                }

                if (!empty($userEmailValidation)) {

                    if ($id != $userEmailValidation[0]->id) {

                        return response()->json([
                            'warning' => 'There is already a user with this email registered!',
                        ], 427);
                    
                    }
            
                }


                $this->modelUsers->insertUpdateUser($request, $id);


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
     *     path="/api/users/{id_user}",
     *     summary="Exclui na base de dados os registros do usuário selecionado.",
     *     tags={"Usuários"},
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
     *         response=425,
     *         description="Undefined User!"
     *     ),
     * )
     */
    public function destroy ($id) {

        try {

            $existingUser = $this->modelUsers->searchUser($id);

            if (empty($existingUser)) {

                return response()->json([
                    'warning' => 'Undefined User!',
                ], 425);

            } else {

                $this->modelUsers->deleteUser($id);


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
