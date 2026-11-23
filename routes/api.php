<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\APIController;

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


Route::get('/schedules', [APIController::class, 'index']);
Route::get('/jobApplicationList', [APIController::class, 'jobApplicationList']);
Route::match(['get','post'], '/api_login', [APIController::class, 'api_login']);

Route::get('linkedin/auth', [APIController::class, 'redirectToLinkedin']);
Route::get('linkedin/callback', [APIController::class, 'handleCallback']);
Route::post('linkedin/post', [APIController::class, 'postToLinkedin']);
Route::post('/linkedin/organization', [APIController::class, 'getOrganization']);
Route::get('/google-analytics', [APIController::class, 'getAnalytics']);

