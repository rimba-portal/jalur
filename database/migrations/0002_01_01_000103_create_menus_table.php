<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
      Schema::create('menus', function (Blueprint $table) {
    $table->id();

    $table->string('category');
    $table->string('group')->nullable();

    $table->string('name');
    $table->string('slug')->unique();
    $table->string('description')->nullable();

    $table->string('icon')->nullable();
    $table->string('color')->nullable();

    $table->foreignId('parent_id')->nullable();
    $table->foreign('parent_id')->references('id')->on('menus');

    $table->string('permission')->nullable();
    $table->string('panel')->nullable();

    $table->integer('sort')->default(0);

    $table->boolean('is_active')->default(true);

    $table->json('attributes')->nullable();

    $table->timestamps();

    $table->index(['category', 'group']);
    $table->index('parent_id');
    $table->index('sort');
});
    }

    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
