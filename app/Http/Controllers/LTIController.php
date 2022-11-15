<?php

namespace App\Http\Controllers;
use Exception;
use Illuminate\Support\Facades\Log;
use \IMSGlobal\LTI;

class LTIController extends Controller
{
	public function login()
	{
		LTI\LTI_OIDC_Login::new(new \App\Database())
			->do_oidc_login_redirect('http://lti-provider.localhost/login-success')
			->do_redirect();
	}

	public function handleRedirectAfterLogin() {
		try {
			$launch = LTI\LTI_Message_Launch::new(new \App\Database())->validate();
		} catch (Exception $e) {
			return response()->json(array('success' => false, 'message' => 'Authentication failed'), 400);
		}
		Log::info($launch->get_launch_data());
		//Get the email and login
		$email = $launch->get_launch_data()['email'];
		$user = \App\User::where('email', $email)->first();
		if (!$user) {
			return response()->json(array('success' => false, 'message' => 'User not found'), 404);
		}
		auth()->login($user, true);

		if ($launch->is_deep_link_launch()) {
			return view('quiz-level-select', array('launch_id' => $launch->get_launch_id()));
		} else {
			try {
				$level = $launch->get_launch_data()['https://purl.imsglobal.org/spec/lti/claim/custom']['level'];
				return view('do-quiz', array('level' => $level));
			} catch (Exception $e) {
				return response()->redirectTo('/greeting?launch_id='.$launch->get_launch_id());
			}
		}
	}

	public function quiz() {
		$queries = request()->query->all();
		$launch_id = $queries['launch_id'];
		$level = $queries['level'];

		$launch = LTI\LTI_Message_Launch::from_cache($launch_id, new \App\Database());
		if (!$launch->is_deep_link_launch()) {
			return response()->json(array('success' => false, 'message' => 'Must be a deep link launch!'), 400);
		}
		$resource = LTI\LTI_Deep_Link_Resource::new()
			->set_url(env('APP_URL').'/do-quiz')
			->set_custom_params(array('level' => $level))
			->set_title('You are doing the quiz of '.$level.' mode!');

		$launch->get_deep_link()->output_response_form(array($resource));
	}

	public function greeting() {
		$launch_id = request()->query->get('launch_id');
		$launch = LTI\LTI_Message_Launch::from_cache($launch_id, new \App\Database());
		$data = $launch->get_launch_data();
		return response()->json(array('success' => true, 'message' => 'Hello '.$data['name']));
	}

}
