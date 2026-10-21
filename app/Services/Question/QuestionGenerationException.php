<?php

namespace App\Services\Question;

use RuntimeException;

/**
 * Kegagalan pembuatan soal AI (API error, key kosong, respons tidak valid).
 * Ditangkap controller untuk ditampilkan sebagai pesan ramah ke admin.
 */
class QuestionGenerationException extends RuntimeException
{
}
