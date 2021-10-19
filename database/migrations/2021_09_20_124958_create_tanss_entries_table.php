<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTanssEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tanss_entries', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes();

            $table->unsignedBigInteger('domain_id');
            $table->unsignedBigInteger('customer_id');

            $table->string('external_id');
            $table->string('provider_name')->nullable();
            $table->timestamp('contract_start')->nullable();
            $table->timestamp('contract_end')->nullable();

            // foreign keys
            $table->foreign('domain_id')->references('id')->on('domains');
            $table->foreign('customer_id')->references('id')->on('customers');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tanss_entries');
    }
}
