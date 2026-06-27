<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Support\Facades\Log;

class SupplierException extends Exception
{
    public static function creationFailed(string $message, array $context = []): self
    {
        Log::error("Supplier creation failed: {$message}", $context);
        return new self("Echec de la creation du fournisseur. {$message}");
    }

    public static function updateFailed(string $message, array $context = []): self
    {
        Log::error("Supplier update failed: {$message}", $context);
        return new self("Echec de la mise a jour du fournisseur. {$message}");
    }

    public static function deletionFailed(string $message, array $context = []): self
    {
        Log::error("Supplier deletion failed: {$message}", $context);
        return new self("Echec de la suppression du fournisseur. {$message}");
    }
}
