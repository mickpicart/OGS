<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\WebsiteController;
use App\Http\Controllers\SupervisionDatasController;
use App\Http\Controllers\ErrorController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Unsign a given user and revoke associated token
Route::post('/logout', [AuthController::class, 'logout']);

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'

], function () {
    Route::post('/login', [AuthController::class, 'login']);
    // Register a new user in DataBase, this route is not used by the Front-End yet
    Route::put('/register', [AuthController::class, 'register']);
    // Get user details from DataBase except credentials
    Route::get('/user-profile', [AuthController::class, 'userProfile']);
    // Send reset password mail
    Route::put('/password/reset-link', [AuthController::class, 'sendPasswordResetLink']);
    // Handle reset password form process
    Route::patch('/password/reset', [AuthController::class, 'callResetPassword']);
});

Route::group([
    'middleware' => 'api',
], function () {
    // Websites CRUD routes
    // Register a new website in DataBase
    Route::put('/website', [WebsiteController::class, 'websiteRegister']);
    // Get websites list from Database
    Route::get('/website', [WebsiteController::class, 'websitesList']);
    // Modify supervision status for one website
    Route::patch('/website/{id}', [WebsiteController::class, 'websiteSupervised']);
    // Delete one website from DataBase and its associated datas and errors
    Route::delete('/website/{id}', [WebsiteController::class, 'websiteDeleteFromDb']);

    // Supervision Datas routes
    // Get last supervision datas and errors from DataBase for supervised websites only
    Route::get('/supervisiondbdatas', [SupervisionDatasController::class, 'supervisionDbDatas']);
    // Load Database with supervision datas and errors for one supervised website
    Route::put('/loadextdataserrors/{id}', [SupervisionDatasController::class, 'loadOneWebsiteExtDatasAndErrorsToDb']);
    // Load Database with supervision datas and errors for all supervised websites
    Route::put('/loadextdataserrorsall', [SupervisionDatasController::class, 'loadAllWebsiteExtDatasAndErrorsToDb']);

    // Load DataBase with errors related to one supervised website
    Route::put('/loaderrors/{id}', [ErrorController::class, 'loadOneWebsiteErrorsInDb']);
});
