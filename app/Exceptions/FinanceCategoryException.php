<?php

namespace App\Exceptions;

use Exception;

use Illuminate\Support\Facades\Log;

class FinanceCategoryException extends Exception
{
    public static function creationFailed(string $message, array $context = []): self
    {
        Log::error("Finance Category creation failed: {$message}", $context);
        return new self("Echec de la creation de la categorie financiere. {$message}");
    }

    public static function updateFailed(string $message, array $context = []): self
    {
        Log::error("Finance Category update failed: {$message}", $context);
        return new self("Echec de la mise a jour de la categorie financiere. {$message}");
    }

    public static function deletionFailed(string $message, array $context = []): self
    {
        Log::error("Finance Category deletion failed: {$message}", $context);
        return new self("Echec de la suppression de la categorie financiere. {$message}");
    }
}
