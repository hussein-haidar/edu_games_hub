<?php

namespace App\Services\Question;

use App\Models\Setting;

/**
 * Resolusi pengaturan AI: database (diinput admin) > .env (config).
 * Semua driver AI membaca key/model lewat sini supaya admin bisa
 * mengganti API key dari dashboard tanpa menyentuh file .env.
 */
class AiSettings
{
    /** Driver yang didukung (selain mock). */
    private const DRIVERS = ['gemini', 'groq'];

    /**
     * API key untuk sebuah driver. Kosong ('') berarti belum diatur.
     */
    public static function key(string $driver): string
    {
        if (in_array($driver, self::DRIVERS, true)) {
            $db = Setting::ambil("ai.{$driver}_api_key");
            if ($db !== null && trim($db) !== '') {
                return trim($db);
            }
        }

        return trim((string) config("ai.{$driver}.api_key", ''));
    }

    /**
     * Nama model untuk sebuah driver.
     */
    public static function model(string $driver): string
    {
        if (in_array($driver, self::DRIVERS, true)) {
            $db = Setting::ambil("ai.{$driver}_model");
            if ($db !== null && trim($db) !== '') {
                return trim($db);
            }
        }

        return trim((string) config("ai.{$driver}.model", ''));
    }

    /**
     * Driver aktif untuk pembuatan soal (mock | gemini | groq).
     */
    public static function driverAktif(): string
    {
        $db = Setting::ambil('ai.driver');

        if ($db !== null && trim($db) !== '') {
            return trim($db);
        }

        return (string) config('ai.driver', 'mock');
    }

    /**
     * Apakah sebuah driver sudah punya API key?
     */
    public static function configured(string $driver): bool
    {
        if ($driver === 'mock') {
            return true;
        }

        return self::key($driver) !== '';
    }

    /**
     * Representasi aman key untuk ditampilkan (hanya 4 karakter terakhir).
     */
    public static function masked(string $driver): string
    {
        $key = self::key($driver);

        if ($key === '') {
            return '';
        }

        $ekor = strlen($key) > 4 ? substr($key, -4) : $key;

        return str_repeat('•', 8).$ekor;
    }
}
