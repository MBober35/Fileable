<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('files', function (Blueprint $table) {
            $table->id();

            $table->string("path")
                ->comment("Путь к файлу");

            $table->string("name")
                ->comment("Имя файла");

            $table->string("mime")
                ->nullable()
                ->comment("Расширение файла");

            $table->unsignedBigInteger("priority")
                ->default(0)
                ->comment("Приоритет вывода");

            $table->string("type")
                ->default("file")
                ->comment("Тип файла");

            $table->nullableMorphs("fileable");

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('files');
    }
}
