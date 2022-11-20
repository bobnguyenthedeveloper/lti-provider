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

Route::post('/login-success', 'LTIController@handleRedirectAfterLogin');

//Auth::routes();

Route::get('/', 'LTIController@greeting');
Route::get('/quiz-configure', 'LTIController@selectQuizLevel');
Route::get('do-quiz', 'LTIController@doQuiz');
Route::post('do-submit', 'LTIController@handleQuizSubmitted');
Route::get('quiz-completed', 'LTIController@quizCompleted');

//Route::middleware('auth')->group(function() {
//
//});
