<?php

namespace App\Services\Question;

use InvalidArgumentException;

/**
 * Pemilih driver AI — pola yang sama dengan PaymentManager.
 */
class QuestionGeneratorManager
{
    private array $generators = [];

    public function generator(?string $driver = null): QuestionGenerator
    {
        $driver = $driver ?: (string) config('ai.driver', 'mock');

        if (isset($this->generators[$driver])) {
            return $this->generators[$driver];
        }

        $generator = match ($driver) {
            'mock' => new MockQuestionGenerator(),
            'gemini' => new GeminiQuestionGenerator(),
            'groq' => new GroqQuestionGenerator(),
            default => throw new InvalidArgumentException("Driver AI [{$driver}] tidak didukung."),
        };

        return $this->generators[$driver] = $generator;
    }
}
