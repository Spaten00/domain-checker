<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRrpproxyEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rrpproxy_entries', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->unsignedBigInteger('domain_id');

            $table->timestamp('contract_start')->nullable();
            $table->timestamp('contract_end')->nullable();
            $table->timestamp('contract_renewal')->nullable();

            // foreign keys
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
        Schema::dropIfExists('rrpproxy_entries');
    }
}
