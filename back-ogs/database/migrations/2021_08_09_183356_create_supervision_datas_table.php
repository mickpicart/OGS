<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupervisionDatasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */

	public function up()
	{
		Schema::create('supervision_datas', function(Blueprint $table) {
			$table->id();
            $table->foreignId('website_id')
            ->constrained()
            ->onUpdate('cascade')
            ->onDelete('cascade');
			$table->json('wp_ext_datas')->nullable();
			$table->string('cms', 50)->nullable();
			$table->boolean('is_ssl_valid')->nullable();
			$table->longText('is_robotxt')->nullable();
			$table->json('get_header_response')->nullable();
			$table->longText('is_sitemap')->nullable();
			$table->string('ga_datas')->nullable();
			$table->string('google_webmaster')->nullable();
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
        Schema::dropIfExists('supervision_datas');
    }
}
