<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGenreCategoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('genre_category', function (Blueprint $table) {
            $table->uuid('genre_id')->index();
            $table->foreign('genre_id')->references('id')->on('genres');
            $table->uuid('category_id')->index();
            $table->foreign('category_id')->references('id')->on('categories');
            $table->unique(['genre_id', 'category_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('genre_category');
    }
}