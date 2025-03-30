<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->enum('user_type', ['regular', 'private_advertiser', 'business_advertiser'])->default('regular');
            $table->string('business_name')->nullable();
            $table->text('business_details')->nullable();
            $table->boolean('contract_approved')->default(false);
            $table->string('locale')->default('nl'); // Voor meertaligheid
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
};