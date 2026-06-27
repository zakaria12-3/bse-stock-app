<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Support\Facades\Log;

class FinanceTransactionException extends Exception
{
    public static function creationFailed(string $message, array $context = []): self
    {
        Log::error("Finance Transaction creation failed: {$message}", $context);
        return new self("Echec de la creation de la transaction. {$message}");
    }

    public static function updateFailed(string $message, array $context = []): self
    {
        Log::error("Finance Transaction update failed: {$message}", $context);
        return new self("Echec de la mise a jour de la transaction. {$message}");
    }

    public static function deletionFailed(string $message, array $context = []): self
    {
        Log::error("Finance Transaction deletion failed: {$message}", $context);
        return new self("Echec de la suppression de la transaction. {$message}");
    }
}
