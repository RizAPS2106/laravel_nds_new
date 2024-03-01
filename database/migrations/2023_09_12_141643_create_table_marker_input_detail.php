<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableMarkerInputDetail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('marker_input_detail', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('marker_id');
            $table->bigInteger('so_det_id');
            $table->double('ratio');
            $table->double('cut_qty');
            $table->char('cancel');
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
        Schema::dropIfExists('marker_input_detail');
    }
}
