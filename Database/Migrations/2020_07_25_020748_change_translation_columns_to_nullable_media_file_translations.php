<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeTranslationColumnsToNullableMediaFileTranslations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('media__file_translations', function (Blueprint $table) {
            $table->string('description')->nullable()->change();
            $table->string('alt_attribute')->nullable()->change();
            $table->string('keywords')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('', function (Blueprint $table){
            $table->string('description')->nullable(false)->change();
            $table->string('alt_attribute')->nullable(false)->change();
            $table->string('keywords')->nullable(false)->change();
        });
    }
}
