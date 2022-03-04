<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWebsitesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('websites', function(Blueprint $table) {
            $table->id();
            $table->string('url', 255)->unique();
			$table->boolean('supervised')->default(0);
			$table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
		});
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('websites');
    }
}
