<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ResizeColumnsStartingDateAndPublishedInAnalyzes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('analyzes', function (Blueprint $table) {
            $table->string('starting_date', 500)->change();
            $table->string('published', 500)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('analyzes', function (Blueprint $table) {
            $table->string('starting_date', 60)->change();
            $table->string('published', 60)->change();
        });
    }
}
