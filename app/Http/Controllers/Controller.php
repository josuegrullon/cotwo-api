<?php

namespace App\Http\Controllers;
use Illuminate\Http\Response;
use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
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
     * Response
     *
     * @param      mix    $content  
     *
     * @return     Response  json
     */
    public function response($content) 
    {
    	return (new Response($content ,200));
    }
}
