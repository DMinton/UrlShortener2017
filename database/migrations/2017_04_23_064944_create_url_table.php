<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUrlTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('url')) {
            Schema::create('url', function (Blueprint $table) {
                $table->increments('id');
                $table->string('shortenedUrl', 10)->unique();
                $table->text('fullUrl', 200);
                $table->string('hashUrl', 100)->unique();
                $table->integer('visits')->default(0);
                $table->timestamps();

                $table->index(array('shortenedUrl', 'hashUrl'));
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
        if (Schema::hasTable('url')) {
            Schema::drop('url');
        }
    }
}
