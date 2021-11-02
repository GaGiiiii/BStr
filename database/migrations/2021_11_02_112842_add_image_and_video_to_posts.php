<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddImageAndVideoToPosts extends Migration {
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up() {
    Schema::table('posts', function (Blueprint $table) {
      $table->string('image', 100)->nullable()->default(null);
      $table->string('video', 100)->nullable()->default(null);
      $table->string('body', 10000)->nullable()->default(null)->change();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down() {
    Schema::table('posts', function (Blueprint $table) {
      $table->dropColumn('image');
      $table->dropColumn('video');
      $table->string('body', 10000);
    });
  }
}
