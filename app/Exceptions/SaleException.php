<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Support\Facades\Log;

class SaleException extends Exception
{
    public static function creationFailed(string $message, array $context = []): self
    {
        Log::error("Sale creation failed: {$message}", $context);
        return new self("Echec de la creation du dossier : {$message}");
    }

    public static function updateFailed(string $message, array $context = []): self
    {
        Log::error("Sale update failed: {$message}", $context);
        return new self("Echec de la mise a jour du dossier : {$message}");
    }

    public static function cancellationFailed(string $message, array $context = []): self
    {
        Log::error("Sale cancellation failed: {$message}", $context);
        return new self("Echec de l annulation du dossier : {$message}");
    }

    public static function invalidStatus(string $action, string $status, array $context = []): self
    {
        $message = "Impossible d effectuer l action {$action} sur un dossier avec le statut '{$status}'.";
        Log::warning($message, $context);
        return new self($message);
    }

    public static function missingReference(string $reference, array $context = []): self
    {
        $message = "Reference obligatoire manquante : {$reference}.";
        Log::warning($message, $context);
        return new self($message);
    }

    public static function insufficientStock(string $productName, int $requested, int $available): self
    {
        $message = "Stock insuffisant pour la piece '{$productName}'. Demande : {$requested}, disponible : {$available}.";
        Log::warning($message);
        return new self($message);
    }

    public static function productNotFound(int $productId): self
    {
        $message = "Piece avec l ID {$productId} introuvable pendant le traitement du dossier.";
        Log::error($message);
        return new self($message);
    }

    public static function invalidDiscount(string $reason): self
    {
        Log::warning("Invalid discount applied: {$reason}");
        return new self("Remise invalide : {$reason}");
    }

    public static function insufficientPayment(float $total, float $received): self
    {
        $message = "Insufficient payment. Total: {$total}, Received: {$received}";
        Log::warning($message);
        return new self("Le paiement est insuffisant. Veuillez encaisser le montant total.");
    }
}
