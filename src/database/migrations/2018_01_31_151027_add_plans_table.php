<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plans', function (Blueprint $table){
            $table->string('provider_id')->primary();
            $table->json('name');
            $table->json('description');
            $table->float('price', 10, 2);
            $table->json('features')->nullable();
            $table->enum('period', ['monthly', 'yearly'])->default('monthly');
            $table->boolean('archived')->default(false)->index();
            $table->boolean('default')->default(0);
            $table->integer('trial')->default(0);

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
        Schema::drop('plans');
    }
}
