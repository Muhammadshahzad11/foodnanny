<?php

use App\Enums\Status;
use App\Enums\CampaignType;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug');
            $table->longText('description');
            $table->timestamp('start_date');
            $table->timestamp('end_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->tinyInteger('type')->default(CampaignType::FREE);
            $table->decimal('amount', 19, 6)->default(0);
            $table->tinyInteger('status')->default(Status::ACTIVE);
            $table->string('creator_type',)->nullable();
            $table->bigInteger('creator_id',)->nullable();
            $table->string('editor_type',)->nullable();
            $table->bigInteger('editor_id',)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaigns');
    }
};
