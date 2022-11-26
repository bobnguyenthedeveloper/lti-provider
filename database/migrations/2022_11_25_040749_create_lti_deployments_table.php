<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLtiDeploymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lti_deployments', function (Blueprint $table) {
			$table->string('deployment_id', 255);
			$table->unsignedBigInteger('registration_id');
	        $table->foreign('registration_id')->references('id')->on('lti_registrations')->onDelete('cascade');
			$table->primary(array('deployment_id', 'registration_id'));
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
        Schema::dropIfExists('lti_deployments');
    }
}
