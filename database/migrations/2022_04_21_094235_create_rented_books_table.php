<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRentedBooksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rented_books', function (Blueprint $table) {
            $table->bigIncrements('id')->index();
            
            $table->bigInteger('users_id')->unsigned()->nullable();
            $table->index('users_id');
            $table->foreign('users_id')->references('id')->on('users');

            $table->bigInteger('books_id')->unsigned()->nullable();
            $table->index('books_id');
            $table->foreign('books_id')->references('id')->on('books');
            
            $table->timestamp('books_issued_date')->nullable();
            $table->timestamp('books_returned_date')->nullable();
            $table->timestamps();
            $table->softDeletes(); 

            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rented_books');
    }
}
