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
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sub_topic_id')->constrained('sub_topics')->cascadeOnDelete();
            $table->string('isbn')->unique();
            $table->string('cover');
            $table->string('title');
            $table->string('language');
            $table->integer('num_of_pages');
            $table->string('author');
            $table->string('publisher');
            $table->string('publication_date');
            $table->decimal("price");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
