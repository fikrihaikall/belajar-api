<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('beritas', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->string('slug');
            $table->string('foto');
            $table->text('deskripsi');
            $table->unsignedBigInteger('id_kategori');
            $table->unsignedBigInteger('id_user');

            $table->foreign('id_kategori')->references('id')->on('kategoris')->onDelete('cascade');
            $table->foreign('id_user')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
        Schema::create('tag_berita', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_tag');
            $table->unsignedBigInteger('id_berita');
            $table->foreign('id_tag')->references('id')->on('tags')->onDelete('cascade');
            $table->foreign('id_berita')->references('id')->on('beritas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('beritas');
        Schema::dropIfExists('tag_berita');
    }
};
