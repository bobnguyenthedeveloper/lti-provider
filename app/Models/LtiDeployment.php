<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LtiDeployment extends Model
{
    protected $fillable = ['deployment_id', 'registration_id'];
}
