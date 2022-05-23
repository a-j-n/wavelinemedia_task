<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId(\App\Models\User::class)->references('id')->on('users');
            $table->string('name');
            $table->unsignedBigInteger('booked_banner_impressions');
            $table->double('cost');
            $table->unsignedBigInteger('target_audience_country_id');
            $table->unsignedBigInteger('target_audience_city_id');
            $table->timestamp('start_at');
            $table->timestamp('end_at');
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
        Schema::dropIfExists('campaigns');
    }
};
