<?php

use App\Models\LtiDeployment;
use App\Models\LtiKey;
use App\Models\LtiRegistration;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RegistrationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
	    $res = openssl_pkey_new();
	    openssl_pkey_export($res, $private_key);
	    $public_key = openssl_pkey_get_details($res)['key'];

		$key = LtiKey::create(array(
			'kid' => Str::uuid(),
			'public_key' => $public_key,
			'private_key' => $private_key
		));
        $registration = LtiRegistration::create(array(
	        'issuer' => 'http://mdl4base.localhost',
	        'client_id' => 'vClgm53wc4ejJ9S',
	        'auth_login_endpoint' => ' http://mdl4base.localhost/mod/lti/auth.php',
	        'auth_token_endpoint' => 'http://mdl4base.localhost/mod/lti/token.php',
	        'jwks_endpoint' => 'http://mdl4base.localhost/mod/lti/certs.php',
	        'key_id' => $key->id,
        ));
		LtiDeployment::create(array(
			'registration_id' => $registration->id,
			'deployment_id' => 4
		));
    }
}
