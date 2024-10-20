<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOtpsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('otps', function (Blueprint $table) {
                $table->id();
                $table->integer('user_id');
                $table->foreign('user_id')->references('id')->on('users')
                    ->onCascade('delete');
                $table->string('otp_code');
                $table->timestamp('expires_at');
                $table->timestamp('used_at')->nullble();

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
        Schema::dropIfExists('otps');
    }
}
