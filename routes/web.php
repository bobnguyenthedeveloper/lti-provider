<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use \IMSGlobal\LTI;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::post('lti-login', 'LTIController@login');
Route::post('/redirector', 'LTIController@handleRedirectAfterLogin');

Route::get('/', 'LTIController@greeting');
Route::get('/deeplink-select', 'LTIController@selectQuizLevel');
Route::get('do-quiz', 'LTIController@doQuiz');
Route::post('submit-quiz', 'LTIController@handleQuizSubmitted');
Route::get('quiz-completed', 'LTIController@quizCompleted');
Route::get('jwks', 'LTIController@jwks');
