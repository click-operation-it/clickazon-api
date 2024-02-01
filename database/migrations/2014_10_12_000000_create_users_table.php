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
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('firstname');
            $table->string('lastname');
            $table->string('phoneno')->unique();
            $table->string('pin')->nullable();
            $table->string('country')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('state')->nullable();
            $table->string('address')->nullable();
            $table->string('image')->nullable();
            $table->string('city')->nullable();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->boolean('is_verified')->default(false)->comment('True, False');
            $table->boolean('is_active')->default(false)->comment('True, False');
            $table->boolean('can_login')->default(false)->comment('True, False');
            $table->boolean('is_completed')->default(false)->comment('True, False');
            $table->boolean('2fa')->default(false)->comment('True, False');
            $table->rememberToken();
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
