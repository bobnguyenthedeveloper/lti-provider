<?php

namespace App;

use App\Models\LtiDeployment;
use App\Models\LtiKey;
use App\Models\LtiRegistration;
use Illuminate\Support\Facades\Log;
use \IMSGlobal\LTI;
use function PHPUnit\Framework\throwException;

class Database implements LTI\Database {
	public function find_registration_by_issuer($iss) {
		$registration = LtiRegistration::where('issuer', $iss)->first();
		if (!$registration) {
			throw new \Exception('Issuer Not Found');
		}
		return LTI\LTI_Registration::new()
			->set_auth_login_url($registration->auth_login_endpoint)
			->set_auth_token_url($registration->auth_token_endpoint)
			->set_client_id($registration->client_id)
			->set_key_set_url($registration->jwks_endpoint)
			->set_kid($registration->ltiKey->kid)
			->set_issuer($iss)
			->set_tool_private_key($registration->ltiKey->private_key);
	}

	public function find_deployment($iss, $deployment_id) {
		$registration = LtiRegistration::where('issuer', $iss)->first();
		if (!$registration) {
			throw new \Exception('Issuer Not Found');
		}
		$deployment = LtiDeployment::where('deployment_id', $deployment_id)->where('registration_id', $registration->id)->first();
		if (!$deployment) {
			throw new \Exception('Deployment Not Found');
		}
		return LTI\LTI_Deployment::new()
			->set_deployment_id($deployment_id);
	}
}
