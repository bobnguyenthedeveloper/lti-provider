<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LtiRegistration extends Model
{
    protected $fillable = ['id', 'issuer', 'client_id', 'auth_login_endpoint', 'auth_token_endpoint', 'jwks_endpoint', 'key_id'];

	public function ltiKey() {
		return $this->belongsTo(LtiKey::class, 'key_id');
	}
}
