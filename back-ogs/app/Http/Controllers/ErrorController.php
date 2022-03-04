<?php

namespace App\Http\Controllers;

use App\Models\Error;
use App\Models\SupervisionDatas;
use Illuminate\Support\Facades\Validator;

class ErrorController extends Controller
{

    /**
    * Instantiate a new SupervisionDatasController instance.
    *
    * @return void
    */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
    * Load external datas (WP Extension datas etc...) to DataBase for all websites
    *
    * @param int $id
    * @return \Illuminate\Http\JsonResponse
    */

    /**
    * @OA\Put(
    *      path="/loaderrors/{id}",
    *      security={{"access_token": {}}},
    *      operationId="loadOneWebsiteErrorsInDb",
    *      tags={"Errors"},
    *
    *      summary="Load one website errors to DataBase by Id",
    *      description="Load one website errors to DataBase by Id",
    *      @OA\Parameter(
    *          name="id",
    *          required=true,
    *          in="path",
    *          description="The supervision_datas id",
    *          @OA\Schema(
    *              type="integer"
    *          )
    *      ),
    *      @OA\Response(
    *          response=200,
    *          description="Successful operation",
    *          @OA\MediaType(
    *          mediaType="application/json",
    *          )
    *      ),
    *      @OA\Response(
    *          response=401,
    *          description="Unauthorized",
    *      ),
    *      @OA\Response(
    *          response=400,
    *          description="Bad request"
    *      ),
    *     )
    */

    public static function loadOneWebsiteErrorsInDb(int $id)
    {
        // Validate id format
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|numeric'
        ]);

        // If validation fails return json error and 400
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        } else {
            // Retrieve supervision datas for this website id
            $data = SupervisionDatas::find($id);

            // Define ErrorHelper class directory as variable
            $classImport = 'App\Helpers\ErrorHelper::';
            $errorCrit = 0;

            // List all methods from ErrorHelper class to be applied to the website
            $functions = [
                $classImport . 'checkSsl',
                $classImport . 'checkOnConstruct',
                $classImport . 'checkWpDebugModeLog',
                $classImport . 'checkWpModeLogFn',
                $classImport . 'checkGaScriptId',
                $classImport . 'checkWpCron',
                $classImport . 'checkWpAutoUpdate',
                $classImport . 'checkGetHeaderResponse'
            ];

            // Apply all methods and aggregate associated messages and criticity levels
            foreach ($functions as $function) {
                $error['message'][] = $function($data)['message'];
                $errorCrit = $errorCrit + $function($data)['criticity'];
            };

            // Delete empty messages
            foreach ($error['message'] as $key => $value) {
                if (empty($value)) {
                    unset($error['message'][$key]);
                }
            }

                // Load errors table in Database with collected messages and criticity levels
                $errorDatas = [
                    "supervision_datas_id" => $id,
                    "message" => json_encode($error['message']),
                    "criticity" => $errorCrit
                ];
                Error::create($errorDatas);

                // Unset variables
                unset($error, $errorDatas);
                $errorCrit = 0;

                // Return json message and 201
                return response()->json(['message' => 'Errors successfully loaded in Database.'], 201);
        }
    }
}
