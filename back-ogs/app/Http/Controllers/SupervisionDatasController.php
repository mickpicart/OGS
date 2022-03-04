<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use App\Models\SupervisionDatas;
use App\Models\Error;
use App\Models\Website;
use Illuminate\Support\Facades\DB;
use App\Helpers\DataHelper;
use App\Http\Controllers\ErrorController;

class SupervisionDatasController extends Controller
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
    * Load external datas (WP Extension datas etc...) to DataBase for one website
    *
    * @param integer $id
    * @return \Illuminate\Http\JsonResponse
    */

    /**
    * @OA\Put(
    *      path="/loadextdataserrors/{id}",
    *      security={{"access_token": {}}},
    *      operationId="loadOneWebsiteExtDatasAndErrorsToDb",
    *      tags={"External Datas and Errors by Id"},
    *
    *      summary="Load external datas and errors to DataBase for one website",
    *      description="Load external datas and errors to DataBase for one website",
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
    *          response=201,
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
    * )
    */

    public static function loadOneWebsiteExtDatasAndErrorsToDb(int $id)
    {
        // Validate id format
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|numeric'
        ]);

        // If validation fails return json error and 400
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        } else {
            // Retrieve id associated website url from Database
            $website = Website::where('id', $id)->find($id);

            // If website is supervised
            if ($website->supervised == 1) {
                $main_domain = DataHelper::getDomainFromUrl($website->url);

                // Apply all methods from DataHelper class and associate
                // returned datas with corresponding column from Database supervision_datas table
                // and create a line for this website in Database
                $supervisionDatas = [
                    "website_id" => $website->id,
                    "wp_ext_datas" => DataHelper::getWpExtensionDatas($website),
                    "cms" => DataHelper::whichCms($website),
                    "is_ssl_valid" => DataHelper::isSslValid($main_domain),
                    "is_robotxt" => DataHelper::getRobotsTxt($website),
                    "get_header_response" => DataHelper::getHeader($website),
                    "is_sitemap" => DataHelper::getSitemapXml($website)
                    ];
                SupervisionDatas::create($supervisionDatas);

                // Get last supervision datas website id and translate such website
                // supervision datas into errors to be loaded in Database. This way every time
                // supervision datas are loaded in Database, associated errors will be as well.
                $lastId = DB::getPdo()->lastInsertId();
                ErrorController::loadOneWebsiteErrorsInDb($lastId);
            } else {
                // For website that are not supervised yet, just create empty lines
                // in supervision_datas and errors table in Database for them to be displayed
                // in the Front-End Dashboard
                SupervisionDatas::create(["website_id" => $website->id]);
                $lastId = DB::getPdo()->lastInsertId();
                Error::create(["supervision_datas_id" => $lastId]);
            }
            return response()->json(['message' => 'External Datas and Errors successfully loaded for this website'], 201);
        }
    }


    /**
    * Load external datas (WP Extension datas etc...) to DataBase for all websites
    *
    * @return \Illuminate\Http\JsonResponse
    */

    /**
    * @OA\Put(
    *      path="/loadextdataserrorsall",
    *      security={{"access_token": {}}},
    *      operationId="loadAllWebsiteExtDatasAndErrorsToDb",
    *      tags={"All External Datas and Errors"},
    *
    *      summary="Load external datas and errors to DataBase for all websites",
    *      description="Load external datas and errors to DataBase for all websites",
    *      @OA\Response(
    *          response=201,
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

    public static function loadAllWebsiteExtDatasAndErrorsToDb()
    {
        $websites = WebsiteController::websitesList();

        foreach ($websites as $website) {
            SupervisionDatasController::loadOneWebsiteExtDatasAndErrorsToDb($website->id);
        }

        return response()->json(['message' => 'External Datas and Errors successfully loaded'], 201);
    }


    /**
    * Get all most recent supervision Datas from DataBase
    *
    * @return array $datas
    */

    /**
    * @OA\Get(
    *      path="/supervisiondbdatas",
    *      security={{"access_token": {}}},
    *      operationId="supervisionDBDatas",
    *      tags={"Supervision DB Datas"},
    *
    *      summary="Get all supervision datas from DataBase",
    *      description="Returns supervision datas from DataBase",
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
    *     )
    */

    public static function supervisionDbDatas()
    {
        $datas = [];
        $i = 0;

        // !!! Security code review : when using DB::raw function,
        // passed datas must be escaped but here we do not pass any data !!!

        // Identify most recent supervision datas from database
        $lastUpdateDatas = SupervisionDatas::select('website_id', SupervisionDatas::raw('MAX(updated_at) as last_datas_update'))
            ->groupBy('website_id')
            ->get();

        // Merge supervision datas with errors
        $dbDatas = DB::table('supervision_datas')
            ->join('websites', 'websites.id', '=', 'supervision_datas.website_id')
            ->join('errors', 'errors.supervision_datas_id', '=', 'supervision_datas.id')
            ->get();

        // Retrieve most recent supervision datas and errors from database. Such object $datas
        // is the main one consumed by Front-End dashboard
        foreach ($dbDatas as $dbData) {
            foreach ($lastUpdateDatas as $lastUpdateData) {
                if (($lastUpdateData->website_id == $dbData->website_id) && ($lastUpdateData->last_datas_update == $dbData->updated_at)) {
                    $datas[$i] = $dbData;
                    $i = $i + 1;
                }
            }
        }
        return $datas;
    }
}
