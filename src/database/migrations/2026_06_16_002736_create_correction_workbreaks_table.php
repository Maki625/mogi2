<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCorrectionWorkbreaksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('correction_workbreaks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('correction_request_id')->constrained()
                    ->onDelete('cascade');
            $table->dateTime('break_start')->nullable();
            $table->dateTime('break_end')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('correction_workbreaks');
    }
}
