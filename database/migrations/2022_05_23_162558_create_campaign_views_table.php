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
        Schema::create('campaign_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId(\App\Models\CampaignMedia::class)->references('id')->on('campaign_media');
            $table->boolean('clicked')->default(false);
            $table->ipAddress('ip');
            $table->unsignedBigInteger('county_id')->nullable();
            $table->unsignedBigInteger('city_id')->nullable();
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
        Schema::dropIfExists('campaign_views');
    }
};
