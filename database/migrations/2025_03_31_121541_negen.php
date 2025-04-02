<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('translations', function (Blueprint $table) {
            $table->id();
            $table->string('model');
            $table->unsignedBigInteger('model_id');
            $table->string('attribute');
            $table->string('locale', 5);
            $table->text('value');
            $table->timestamps();
            
            $table->unique(['model', 'model_id', 'attribute', 'locale']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('translations');
    }
};