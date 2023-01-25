<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use App\Models\Exception;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="Laravel OpenApi Car Shop Documentation",
 *      description="Car Shop OpenApi description",
 *      @OA\Contact(
 *          email="gaoun@globalistic.tech"
 *      ),
 *      @OA\License(
 *          name="Apache 2.0",
 *          url="http://www.apache.org/licenses/LICENSE-2.0.html"
 *      )
 * )
 *
 * @OA\Server(
 *      url=L5_SWAGGER_CONST_HOST,
 *      description="car shop"
 * )
 *       @OA\SecurityScheme(
 *      securityScheme="bearerToken",
 *      type="http",
 *      scheme="bearer"
 * )
 */

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    // ----- function to handle controller function response
    function handleReturn($success, $data, $message){
        return response()->json([
            "success" => $success,
            "data" => $data,
            "message" => $message
        ]);
    }


}
