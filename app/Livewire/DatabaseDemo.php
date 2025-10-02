<?php

declare(strict_types=1);

namespace App\Livewire;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;

class DatabaseDemo extends Component
{
    public string $selectedDriver = 'sqlite';

    public string $name = '';

    public string $description = '';

    public bool $isActive = true;

    public ?string $errorMessage = null;

    public ?string $successMessage = null;

    public function mount(): void
    {
        $this->selectedDriver = config('database.default');
    }

    public function switchDriver(): void
    {
        $this->errorMessage = null;
        $this->successMessage = null;

        try {
            // Validate driver exists
            if (! in_array($this->selectedDriver, ['sqlite', 'mysql', 'pgsql', 'sqlsrv'])) {
                $this->errorMessage = 'Invalid database driver selected.';

                return;
            }

            // Update runtime config
            Config::set('database.default', $this->selectedDriver);

            // Test connection
            DB::connection($this->selectedDriver)->getPdo();

            // Run migrations if needed
            Artisan::call('migrate', ['--force' => true]);

            $this->successMessage = "Successfully switched to {$this->selectedDriver} driver and migrations completed!";
        } catch (\Exception $e) {
            $this->errorMessage = "Failed to switch driver: {$e->getMessage()}";
        }
    }

    public function addRecord(): void
    {
        $this->errorMessage = null;
        $this->successMessage = null;

        $this->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        try {
            DB::table('database_demos')->insert([
                'name' => $this->name,
                'database_driver' => $this->selectedDriver,
                'is_active' => $this->isActive,
                'description' => $this->description,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $this->successMessage = 'Record added successfully!';
            $this->reset(['name', 'description', 'isActive']);
        } catch (\Exception $e) {
            $this->errorMessage = "Failed to add record: {$e->getMessage()}";
        }
    }

    public function deleteRecord(int $id): void
    {
        try {
            DB::table('database_demos')->where('id', $id)->delete();
            $this->successMessage = 'Record deleted successfully!';
        } catch (\Exception $e) {
            $this->errorMessage = "Failed to delete record: {$e->getMessage()}";
        }
    }

    #[Computed]
    public function records()
    {
        try {
            return DB::table('database_demos')->orderBy('created_at', 'desc')->get();
        } catch (\Exception $e) {
            $this->errorMessage = "Failed to fetch records: {$e->getMessage()}";

            return collect();
        }
    }

    #[Computed]
    public function connectionInfo(): array
    {
        try {
            $connection = DB::connection($this->selectedDriver);
            $config = config("database.connections.{$this->selectedDriver}");

            return [
                'driver' => $this->selectedDriver,
                'host' => $config['host'] ?? 'N/A',
                'database' => $config['database'] ?? 'N/A',
                'connected' => true,
            ];
        } catch (\Exception $e) {
            return [
                'driver' => $this->selectedDriver,
                'connected' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function render()
    {
        return view('livewire.database-demo')->layout('layouts.app');
    }
}
