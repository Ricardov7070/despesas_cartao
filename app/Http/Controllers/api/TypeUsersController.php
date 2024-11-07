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


    public function index () {

       return $this->modelTypeUsers->searchTypeUser();

    }

}
