<?php

namespace App\Http\Controllers;

use App\Users;

class SecureController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Login.
     *
     * @return void
     */
    public function login()
    {
        return $this->response(['message' => 'ok']);
    }

     /**
      * Logout.
      *
      * @return     void
      */
    public function logout()
    {
        return $this->response(['message' => 'ok']);
    }
}
