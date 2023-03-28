<?php

use Domain\Blog\Entities\Article;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->uuid('id')->primary()->index();
            $table->string('title', Article::TITLE_MAX_LENGTH);
            $table->text('content')->nullable();
            $table->enum('status', array_column(\Domain\Blog\ValueObjects\Status::cases(), 'value'));
            $table->dateTime('published_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
