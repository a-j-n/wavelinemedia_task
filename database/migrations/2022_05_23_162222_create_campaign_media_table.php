<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('campaign_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId(\App\Models\Campaign::class)->references('id')->on('campaigns');
            $table->enum('type', ['images', 'banners', 'videos']);
            $table->enum('position', ['top', 'middle', 'bottom']);
            $table->string('media_url');
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
        Schema::dropIfExists('campaign_media');
    }
};
