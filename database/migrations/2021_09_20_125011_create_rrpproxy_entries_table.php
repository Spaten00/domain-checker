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

            $table->timestamps('contract_start');
            $table->timestamps('contract_end');
            $table->timestamps('contract_renewal');
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
