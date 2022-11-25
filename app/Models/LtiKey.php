<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LtiKey extends Model
{
    protected $fillable = ['kid', 'private_key', 'public_key'];

	public static function getKeySets() {
		$keys = LtiKey::all();
		$results = [];
		foreach ($keys as $key) {
			$results[$key->kid] = $key->private_key;
		}
		return $results;
	}
}
