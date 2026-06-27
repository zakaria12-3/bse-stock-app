<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Support\Facades\Log;

class PurchaseException extends Exception
{
    public static function creationFailed(string $message, array $context = []): self
    {
        Log::error("Purchase creation failed: {$message}", $context);
        return new self("Echec de la creation de l achat : {$message}");
    }

    public static function updateFailed(string $message, array $context = []): self
    {
        Log::error("Purchase update failed: {$message}", $context);
        return new self("Echec de la mise a jour de l achat : {$message}");
    }

    public static function deletionFailed(string $message, array $context = []): self
    {
        Log::error("Purchase deletion failed: {$message}", $context);
        return new self("Echec de la suppression de l achat. {$message}");
    }

    public static function invalidStatus(string $action, string $status, array $context = []): self
    {
        $message = "Impossible d effectuer l action {$action} sur un achat avec le statut '{$status}'.";
        Log::warning($message, $context);
        return new self($message);
    }

    public static function missingReference(string $reference, array $context = []): self
    {
        $message = "Reference obligatoire manquante : {$reference}.";
        Log::warning($message, $context);
        return new self($message);
    }
}
