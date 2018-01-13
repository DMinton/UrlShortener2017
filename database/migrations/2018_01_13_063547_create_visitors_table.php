<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVisitorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('visitor')) {
            Schema::create('visitor', function (Blueprint $table) {
                $table->increments('id');
                $table->string('ip', 50);
                $table->string('country', 100);
                $table->string('region', 100);
                $table->string('city', 100);
                $table->string('path', 100);
                $table->longText('ip_payload');
                $table->longText('request_payload');
                $table->timestamps();

                $table->index('ip');
                $table->index('country');
                $table->index('region');
                $table->index('city');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('visitor')) {
            Schema::drop('visitor');
        }
    }
}
