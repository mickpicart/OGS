<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateErrorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('errors', function(Blueprint $table) {
			$table->increments('id');
			$table->foreignId('supervision_datas_id')
            ->constrained()
            ->onUpdate('cascade')
            ->onDelete('cascade');
			// $table->json('message')->collation('utf8mb4_general_ci')->nullable();
            $table->json('message')->nullable();
			$table->integer('criticity')->nullable();
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
        Schema::dropIfExists('errors');
    }
}
