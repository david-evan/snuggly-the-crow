<?php

use Domain\Users\Entities\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary()->index();
            $table->string('username', User::USERNAME_MAX_LENGTH)->unique();
            $table->string('password', 255);
            $table->dateTime('last_login')->nullable();
            $table->string('api_key', 36)->nullable();
            $table->dateTime('api_key_expire_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
