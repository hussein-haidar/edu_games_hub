<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Game "urutkan" & "menjodohkan" menyimpan jawaban berupa gabungan
     * beberapa item/pasangan — bisa melebihi 255 karakter, jadi kolomnya
     * diubah menjadi text.
     */
    public function up(): void
    {
        Schema::table('soals', function (Blueprint $table) {
            $table->text('jawaban_benar')->change();
        });
    }

    public function down(): void
    {
        Schema::table('soals', function (Blueprint $table) {
            $table->string('jawaban_benar', 255)->change();
        });
    }
};
