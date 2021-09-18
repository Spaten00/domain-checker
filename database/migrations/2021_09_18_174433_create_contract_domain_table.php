<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContractDomainTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contract_domain', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->unsignedBigInteger('contract_id');
            $table->unsignedBigInteger('domain_id');

            // foreign keys
            $table->foreign('contract_id')->references('id')->on('contracts');
            $table->foreign('domain_id')->references('id')->on('domains');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contract_domain');
    }
}
