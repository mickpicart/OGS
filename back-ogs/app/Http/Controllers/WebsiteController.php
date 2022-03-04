<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\SupervisionDatasController;
use App\Models\Website;

class WebsiteController extends Controller
{
    /**
    * Instantiate a new WebsiteController instance.
    *
    * @return void
    */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
    * Register a new webSite.
    *
    * @param \Illuminate\Http\Request $request
    * @return \Illuminate\Http\JsonResponse
    */

    /**
    * @OA\Put(
    *      path="/website",
    *      security={{"access_token": {}}},
    *      operationId="websiteRegister",
    *      tags={"Websites"},
    *
    *      summary="Register a new website",
    *      @OA\Parameter(
    *          name="url",
    *          required=true,
    *          in="query",
    *          description="The website url",
    *          @OA\Schema(
    *              type="string"
    *          )
    *      ),
    *      @OA\Response(
    *          response=201,
    *          description="Website successfully registered",
    *          @OA\MediaType(
    *          mediaType="application/json",
    *          )
    *      ),
    *      @OA\Response(
    *          response=400,
    *          description="Bad request",
    *      ),
    *      @OA\Response(
    *          response=401,
    *          description="Unauthorized",
    *      ),
    *      @OA\Response(
    *          response=404,
    *          description="Not found"
    *      ),
    *      @OA\Response(
    *          response=409,
    *          description="Resource already exists"
    *      )
    * )
    */


    public function websiteRegister(Request $request)
    {
        // Validate url format
        $validator = Validator::make($request->all(), [
            'url' => 'required|url',
        ]);

        // If validation fails return json error and 400
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        // If validation OK create a new line in Database websites table
        // Check first if url already exists in DataBase
        $website = Website::where('url', $validator->validated()['url'])->first();
        // If url doesn't exist, it's created
        if ($website == null) {
            $website = Website::create($validator->validated());
            // Then get last website id created in Database and load both associated supervision
            // datas and errors to Database for this website
            $lastId = DB::getPdo()->lastInsertId();
            SupervisionDatasController::loadOneWebsiteExtDatasAndErrorsToDb($lastId);

            // Return json message, created website details and 201
            return response()->json([
                'message' => 'Website successfully registered',
                'website' => $website
            ], 201);
        // If url already exists, error 409
        } else {
            return response()->json([
                'message' => 'Resource already exists',
                'website' => $website
            ], 409);
        }
    }


    /**
    * Add a webSite for supervision.
    *
    * @param integer $id
    * @return \Illuminate\Http\JsonResponse
    */

    /**
    * @OA\Patch(
    *      path="/website/{id}",
    *      security={{"access_token": {}}},
    *      operationId="websiteSupervised",
    *      tags={"Websites"},
    *
    *      summary="Modify a website supervision status",
    *      @OA\Parameter(
    *          name="id",
    *          required=true,
    *          in="path",
    *          description="The website id",
    *          @OA\Schema(
    *              type="integer"
    *          )
    *      ),
    *      @OA\Response(
    *          response=200,
    *          description="Website supervision parameter successfully modified",
    *          @OA\MediaType(
    *          mediaType="application/json",
    *          )
    *      ),
    *      @OA\Response(
    *          response=400,
    *          description="Bad request",
    *      ),
    *      @OA\Response(
    *          response=401,
    *          description="Unauthorized",
    *      ),
    *      @OA\Response(
    *          response=404,
    *          description="Not found"
    *      )
    * )
    */

    public function websiteSupervised(int $id)
    {
        // Validate id format
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|numeric'
        ]);

        // If validation fails return json error and 400
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        } else {
            // Change supervised status for this website
            $supervised = Website::find($id)->supervised;
            Website::find($id)->update(['supervised' => !$supervised]);
            // Load both associated supervision datas and errors to Database for this website
            SupervisionDatasController::loadOneWebsiteExtDatasAndErrorsToDb($id);
            return response()->json([
                'message' => 'Website supervision status successfully modified',
                'website' => Website::find($id)
            ], 200);
        }
    }


    /**
    * List all websites from DataBase
    *
    * @return object
    */

    /**
    * @OA\Get(
    *      path="/website",
    *      security={{"access_token": {}}},
    *      operationId="websitesList",
    *      tags={"Websites"},
    *
    *      summary="Get websites list",
    *      description="Returns websites list from DataBase",
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
    *          response=404,
    *          description="Not found"
    *      )
    *     )
    */

    public static function websitesList()
    {
        return Website::all();
    }


    /**
    * Delete a webSite for DataBase.
    *
    * @param integer $id
    * @return \Illuminate\Http\JsonResponse
    */

    /**
    * @OA\Delete(
    *      path="/website/{id}",
    *      security={{"access_token": {}}},
    *      operationId="websiteSupervised",
    *      tags={"Websites"},
    *
    *      summary="Delete a website and all its datas from Database using its id",
    *      @OA\Parameter(
    *          name="id",
    *          required=true,
    *          in="path",
    *          description="The website id",
    *          @OA\Schema(
    *              type="integer"
    *          )
    *      ),
    *      @OA\Response(
    *          response=200,
    *          description="Website and related datas successfully deleted from Database",
    *          @OA\MediaType(
    *          mediaType="application/json",
    *          )
    *      ),
    *      @OA\Response(
    *          response=400,
    *          description="Bad request",
    *      ),
    *      @OA\Response(
    *          response=401,
    *          description="Unauthorized",
    *      ),
    *      @OA\Response(
    *          response=404,
    *          description="Not found"
    *      )
    * )
    */


    public function websiteDeleteFromDb(int $id)
    {
        Website::find($id)->delete();

        return response()->json(['message' => 'Website and related datas successfully deleted from Database'], 200);
    }
}
