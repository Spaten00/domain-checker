<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContractHostingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contract_hosting', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->unsignedBigInteger('contract_id');
            $table->unsignedBigInteger('hosting_id');

            // foreign keys
            $table->foreign('contract_id')->references('id')->on('contracts');
            $table->foreign('hosting_id')->references('id')->on('hostings');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contract_hosting');
    }
}
