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
        Schema::create('articles', function (Blueprint $table)
        {
            $table->id();

            // since articles can't exist without a user, we will cascade delete
            $table->foreignId('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('CASCADE');

            // 200 characters as per requirements
            $table->char('title', 200)
                ->index('title_index')
                ->nullable(false);

            // 1000 characters as per requirements
            $table->string('body', 1000)
                ->index('body_index')
                ->nullable(false);

            // published_at is nullable as we will use it for soft deletes
            $table->timestampTz('published_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
