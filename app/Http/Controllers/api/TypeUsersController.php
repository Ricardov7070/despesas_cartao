<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Users\typeUsers;

class TypeUsersController extends Controller
{

    protected $modelTypeUsers;


    public function __construct (typeUsers $modelTypeUsers) {

        $this->modelTypeUsers = $modelTypeUsers;
    
    }


    /**
     * @OA\Get(
     *     path="/api/type_users",
     *     summary="Lista todas categorias de usuários cadastradas no sistema.",
     *     tags={"Categoria de Usuários"},
     *     @OA\Response(
     *         response=420,
     *         description="No registration!"
     *     ),
     * )
     */
    public function index () {

       $typeData = $this->modelTypeUsers->searchTypeUser();

       return empty($typeData) 
                ? response()->json(['alert' => 'No registration!'], 420)
                : $typeData;

    }

}
