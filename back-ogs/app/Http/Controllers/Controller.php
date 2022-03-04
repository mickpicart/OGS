<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{

   /**
    * @OA\Info(
    *      version="1.0.0",
    *      title="OGS",
    *      @OA\Contact(
    *          email="mickpicart@gmail.com"
    *      )
    * )
    *
    * @OA\Server(
    *      url=L5_SWAGGER_CONST_HOST,
    *      description="OGS API"
    * )

    *
    *
    */

    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
