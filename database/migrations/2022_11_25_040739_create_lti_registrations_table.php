<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLtiRegistrationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lti_registrations', function (Blueprint $table) {
	        $table->id();
			$table->string('issuer', 255);
			$table->string('client_id', 255);
			$table->string('auth_login_endpoint', 255);
			$table->string('auth_token_endpoint', 255);
			$table->string('jwks_endpoint', 255);
			$table->unsignedBigInteger('key_id');
	        $table->foreign('key_id')->references('id')->on('lti_keys')->onDelete('cascade');
			$table->unique(array('issuer', 'client_id'));
	        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lti_registrations');
    }
}
