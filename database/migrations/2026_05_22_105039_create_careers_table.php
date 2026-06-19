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
        Schema::create('career_categories', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('name');
            $table->string('slug')->unique();
            $table->foreignUlid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('careers', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('title');
            $table->string('slug');
            $table->enum('type', [ 'security', 'corporate', 'technology', ])->nullable();
            $table->foreignUlid('career_category_id')->constrained('career_categories');
            $table->string('location')->nullable();
            $table->enum('employment_type', [ 'full_time', 'part_time', 'contract', 'internship', 'temporary', ])->nullable();
            $table->enum('work_mode', [ 'onsite', 'remote', 'hybrid', ])->nullable();
            $table->json('overview')->nullable();
            $table->json('description')->nullable();
            $table->json('requirements')->nullable();
            $table->string('application_url')->nullable();
            $table->dateTime('deadline')->nullable();
            $table->enum('status', ['draft', 'published', 'closed'])->default('draft');

            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->string('canonical_url')->nullable();
            $table->string('og_title')->nullable();
            $table->text('og_description')->nullable();
            $table->boolean('no_index')->default(false);

            $table->foreignUlid('created_by')->constrained('users');
            $table->foreignUlid('updated_by')->nullable()->constrained('users');
            $table->timestamp('published_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();

            $table->unique('slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('careers');
        Schema::dropIfExists('career_categories');
    }
};
