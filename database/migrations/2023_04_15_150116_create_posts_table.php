<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->unsigned()->references('id')->on('users')->onDelete('cascade');
            $table->string('thumbnail', 100)->nullable();
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('content');
            $table->boolean('active')->default(false);
            $table->datetime('published_at')->nullable();
            $table->string('meta_title', 255)->nullable();
            $table->string('meta_description', 255)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('post_tags', function (Blueprint $table) {
            $table->id();
            $table->integer('tag_id')->unsigned()->references('id')->on('tags')->onDelete('cascade');
            $table->integer('post_id')->unsigned()->references('id')->on('posts')->onDelete('cascade');
        });

        Schema::create('post_categories', function (Blueprint $table) {
            $table->id();
            $table->integer('category_id')->unsigned()->references('id')->on('categories')->onDelete('cascade');
            $table->integer('post_id')->unsigned()->references('id')->on('posts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
        Schema::dropIfExists('post_tags');
        Schema::dropIfExists('post_categories');
    }
};
