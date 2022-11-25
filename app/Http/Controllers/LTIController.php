<?php

namespace App\Http\Controllers;

use App\Models\LtiKey;
use DateTime;
use Exception;
use Illuminate\Support\Facades\Log;
use \IMSGlobal\LTI;

class LTIController extends Controller {
	public function login() {
		LTI\LTI_OIDC_Login::new(new \App\Database())
			->do_oidc_login_redirect('http://lti-provider.localhost/redirector')
			->do_redirect();
	}

	public function handleRedirectAfterLogin() {
		try {
			$launch = LTI\LTI_Message_Launch::new(new \App\Database())->validate();
		} catch (Exception $e) {
			return response()->json(array('success' => false, 'message' => 'Authentication failed'), 400);
		}
		Log::info($launch->get_launch_data());
		if ($launch->is_deep_link_launch()) {
			return view('quiz-level-select', array('launch_id' => $launch->get_launch_id()));
		}
		$target_url = $launch->get_launch_data()['https://purl.imsglobal.org/spec/lti/claim/target_link_uri'];
		return response()->redirectTo($target_url . '?launch_id=' . $launch->get_launch_id());
	}

	public function greeting() {
		$launch_id = request()->query->get('launch_id');
		$launch = LTI\LTI_Message_Launch::from_cache($launch_id, new \App\Database());
		$data = $launch->get_launch_data();
		return response()->json(array('success' => true, 'message' => 'Hello ' . $data['name']));
	}

	public function selectQuizLevel() {
		$queries = request()->query->all();
		$launch_id = $queries['launch_id'];
		$level = $queries['level'];

		$launch = LTI\LTI_Message_Launch::from_cache($launch_id, new \App\Database());
		if (!$launch->is_deep_link_launch()) {
			return response()->json(array('success' => false, 'message' => 'Must be a deep link launch!'), 400);
		}
		$resource = LTI\LTI_Deep_Link_Resource::new()
			->set_url(env('APP_URL') . '/do-quiz')
			->set_custom_params(array('level' => $level))
			->set_title('The ' . $level . ' quiz!');

		$launch->get_deep_link()->output_response_form(array($resource));
	}

	public function doQuiz() {
		$launch_id = request()->query->get('launch_id');
		$launch = LTI\LTI_Message_Launch::from_cache($launch_id, new \App\Database());
		try {
			$level = $launch->get_launch_data()['https://purl.imsglobal.org/spec/lti/claim/custom']['level'];
			return view('do-quiz', array('level' => $level, 'launch_id' => $launch_id));
		} catch (Exception $e) {
			return response()->json(array('success' => false, 'message' => 'Required param [level] missing!'), 400);
		}
	}

	public function handleQuizSubmitted() {
		$req = request()->only(['launch_id', 'ans']);
		$launch_id = $req['launch_id'];
		$ans = $req['ans'];
		$launch = LTI\LTI_Message_Launch::from_cache($launch_id, new \App\Database());
		if (!$launch->has_ags()) {
			return response()->json(array('success' => false, 'message' => 'Do not have grades!'), 400);
		}
		try {
			$launch_data = $launch->get_launch_data();
			$level = $launch_data['https://purl.imsglobal.org/spec/lti/claim/custom']['level'];
			$correctAnswer = $this->getCorrectAnswer($level);
			$point = 1;
			if ($ans == $correctAnswer) $point = 10;
			$ags = $launch->get_ags(); //Assignments and grades services
			$grade = LTI\LTI_Grade::new()
				->set_score_given($point)
				->set_score_maximum(10) //This one will be compared with the maximum set on LMS and respectively calculated
				->set_timestamp(date(DateTime::ISO8601))
				->set_activity_progress('Completed')
				->set_grading_progress('FullyGraded')
				->set_user_id($launch_data['sub']);

			$ags->put_grade($grade);
			$res = $correctAnswer == $ans ? 'correct' : 'incorrect';
			return response()->redirectTo('/quiz-completed?res='.$res.'&launch_id='.$launch_id);
		} catch (Exception $e) {
			return response()->json(array('success' => false, 'message' => 'Can not return score!'), 400);
		}
	}

	public function quizCompleted() {
		$res = request()->query->get('res');
		$launch_id = request()->query->get('launch_id');
		$launch = LTI\LTI_Message_Launch::from_cache($launch_id, new \App\Database());
		$launch_data = $launch->get_launch_data();
		$data = [];
		if ($launch->has_nrps() && $launch->has_ags()) {
			$members = $launch->get_nrps()->get_members();
			$resourceid = $launch_data['https://purl.imsglobal.org/spec/lti/claim/resource_link']['id'];
			$scores = $launch->get_ags()->get_grades(LTI\LTI_Lineitem::new()->set_resource_id($resourceid));
			usort($scores, function($a, $b) { return $b['resultScore'] - $a['resultScore']; });
			foreach ($scores as $score) {
				$userid = $score['userId'];
				$user = null;
				foreach($members as $mem)
				{
					if ($mem['user_id'] == $userid)
					{
						$user = $mem;
						break;
					}
				}
				$data[] = array(
					'name' => $user['name'],
					'score' => $score['resultScore']
				);
			}
		}
		return view('quiz-result', array('result' => $res, 'ranking' => $data));
	}

	/**
	 * @throws Exception
	 */
	private function getCorrectAnswer($level) {
		switch ($level) {
			case 'easy':
				return 2;
			case 'medium':
				return 20;
			case 'hard':
				return 40;
			default:
				throw new Exception('Invalid level');
		}
	}

	public function jwks() {
		LTI\JWKS_Endpoint::new(LtiKey::getKeySets())->output_jwks();
	}

}
