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

Route::post('lti-login', function () {
	LTI\LTI_OIDC_Login::new(new \App\Database())
		->do_oidc_login_redirect('http://lti-provider.localhost/login-success')
		->do_redirect();
});

Route::post('/login-success', function () {
	$launch = LTI\LTI_Message_Launch::new(new \App\Database())->validate();
	Log::info($launch->get_launch_data());
	auth()->loginUsingId(1, true);
	return view('welcome');
});


Auth::routes();



Route::middleware('auth')->group(function() {
	Route::get('/lti-route', function () {
		Log::info('Auth'. auth()->check());
		return response()->json('ok');
	});
});
