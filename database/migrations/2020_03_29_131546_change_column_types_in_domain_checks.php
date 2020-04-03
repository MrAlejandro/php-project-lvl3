<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeColumnTypesInDomainChecks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('domain_checks', function (Blueprint $table) {
            $table->text('h1')->change();
            $table->text('keywords')->change();
            $table->text('description')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('domain_checks', function (Blueprint $table) {
            $table->string('h1')->change();
            $table->string('keywords')->change();
            $table->string('description')->change();
        });
    }
}
