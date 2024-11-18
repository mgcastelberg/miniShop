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
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->foreignId('current_team_id')->nullable();
            $table->string('profile_photo_path', 2048)->nullable();
            $table->timestamps();
        });

        DB::table('users')->insert(['id'=> 1, 'password'=> bcrypt('LocalD4sh'), 'email'=> 'admin@virket.com', 'name'=> 'admin', 'created_at'=> date('Y-m-d H:m:s')]);
        DB::table('users')->insert(['id'=> 2, 'password'=> bcrypt('LocalD4sh'), 'email'=> 'manager@virket.com', 'name'=> 'manager', 'created_at'=> date('Y-m-d H:m:s')]);
        DB::table('users')->insert(['id'=> 3, 'password'=> bcrypt('LocalD4sh'), 'email'=> 'jmgc@virket.com', 'name'=> 'ManuxDark', 'created_at'=> date('Y-m-d H:m:s')]);

        DB::table('users')->insert(['id'=> 4, 'password'=> bcrypt('Abc123'), 'email'=> 'test1@google.com', 'name'=> 'Test1 User', 'created_at'=> date('Y-m-d H:m:s')]);
        DB::table('users')->insert(['id'=> 5, 'password'=> bcrypt('Abc123'), 'email'=> 'test2@google.com', 'name'=> 'Test2 User', 'created_at'=> date('Y-m-d H:m:s')]);

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
