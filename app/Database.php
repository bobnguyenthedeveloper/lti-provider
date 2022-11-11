<?php

namespace App;

use Illuminate\Support\Facades\Log;
use \IMSGlobal\LTI;

class Database implements LTI\Database {

	public function find_registration_by_issuer($iss) {
		return LTI\LTI_Registration::new()
			->set_auth_login_url("http://mdl4base.localhost/mod/lti/auth.php")
			->set_auth_token_url("http://mdl4base.localhost/mod/lti/token.php")
			->set_client_id("vClgm53wc4ejJ9S")
			->set_key_set_url("http://mdl4base.localhost/mod/lti/certs.php")
			->set_kid("kid-123")
			->set_issuer($iss)
			->set_tool_private_key($this->get_private_key());
	}

	public function find_deployment($iss, $deployment_id) {
		return LTI\LTI_Deployment::new()
			->set_deployment_id($deployment_id);
	}

	private function get_private_key() {
		return file_get_contents(__DIR__ . '/priv_key');
	}
}
