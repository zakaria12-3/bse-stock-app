<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class BackupDatabase extends Command
{
    protected $signature = 'database:backup
        {--force : Creer une sauvegarde meme si une sauvegarde existe deja aujourd hui}
        {--if-due : Ne creer une sauvegarde que si aucune sauvegarde du jour n existe}';

    protected $description = 'Creer une sauvegarde locale de la base SQLite BSE';

    public function handle(): int
    {
        if (config('database.default') !== 'sqlite') {
            $this->warn('La sauvegarde automatique integree est prevue pour SQLite.');
            return self::FAILURE;
        }

        $databasePath = $this->resolveDatabasePath();

        if (!$databasePath || !File::exists($databasePath)) {
            $this->error('Base SQLite introuvable : ' . ($databasePath ?: 'chemin non configure'));
            return self::FAILURE;
        }

        $backupDirectory = storage_path('app/backups/database');
        File::ensureDirectoryExists($backupDirectory);

        if ($this->option('if-due') && !$this->option('force') && $this->hasBackupForToday($backupDirectory)) {
            $this->info('Sauvegarde deja presente pour aujourd hui.');
            $this->pruneOldBackups($backupDirectory);
            return self::SUCCESS;
        }

        $timestamp = Carbon::now()->format('Y-m-d_His');
        $backupPath = $backupDirectory . DIRECTORY_SEPARATOR . "bse-database-{$timestamp}.sqlite";

        try {
            $this->createSqliteBackup($backupPath);
            $this->pruneOldBackups($backupDirectory);
        } catch (\Throwable $e) {
            if (File::exists($backupPath)) {
                File::delete($backupPath);
            }

            $this->error('Echec de la sauvegarde : ' . $e->getMessage());
            return self::FAILURE;
        }

        $this->info('Sauvegarde creee : ' . $backupPath);

        return self::SUCCESS;
    }

    private function resolveDatabasePath(): ?string
    {
        $configuredPath = config('database.connections.sqlite.database');

        if (!$configuredPath || $configuredPath === ':memory:') {
            return null;
        }

        if ($this->isAbsolutePath($configuredPath)) {
            return $configuredPath;
        }

        return base_path($configuredPath);
    }

    private function isAbsolutePath(string $path): bool
    {
        return str_starts_with($path, DIRECTORY_SEPARATOR)
            || preg_match('/^[A-Za-z]:[\/\\\\]/', $path) === 1
            || str_starts_with($path, '\\\\');
    }

    private function hasBackupForToday(string $backupDirectory): bool
    {
        $prefix = 'bse-database-' . Carbon::now()->format('Y-m-d') . '_';

        foreach (File::files($backupDirectory) as $file) {
            if (str_starts_with($file->getFilename(), $prefix) && $file->getExtension() === 'sqlite') {
                return true;
            }
        }

        return false;
    }

    private function createSqliteBackup(string $backupPath): void
    {
        $escapedPath = str_replace("'", "''", $backupPath);

        DB::statement("VACUUM INTO '{$escapedPath}'");
    }

    private function pruneOldBackups(string $backupDirectory): void
    {
        $keepDays = max(1, (int) env('BACKUP_KEEP_DAYS', 30));
        $cutoff = Carbon::now()->subDays($keepDays);

        foreach (File::files($backupDirectory) as $file) {
            if ($file->getExtension() !== 'sqlite') {
                continue;
            }

            if (!str_starts_with($file->getFilename(), 'bse-database-')) {
                continue;
            }

            if (Carbon::createFromTimestamp($file->getMTime())->lt($cutoff)) {
                File::delete($file->getPathname());
            }
        }
    }
}
