<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class DatabaseController extends Controller
{
    public function resetDatabase()
    {
        try {
            Log::info('Starting database reset process');
            
            // For SQLite, we need to close the connection first
            DB::disconnect();
            
            // Get the database path
            $databasePath = database_path('database.sqlite');
            Log::info('Database path: ' . $databasePath);
            
            // Ensure the database file exists and is writable
            if (!File::exists($databasePath)) {
                File::put($databasePath, '');
            }
            chmod($databasePath, 0666);
            Log::info('Database file permissions updated');
            
            // Clear config and cache
            Artisan::call('config:clear');
            Artisan::call('cache:clear');
            Log::info('Config and cache cleared');
            
            // Run the migrations fresh with seed
            Log::info('Starting migrate:fresh --seed');
            $migrateOutput = Artisan::call('migrate:fresh', [
                '--seed' => true,
                '--force' => true,
                '--no-interaction' => true,
                '--database' => 'sqlite'
            ]);
            
            $output = Artisan::output();
            Log::info('Migration output: ' . $output);
            
            if ($migrateOutput !== 0) {
                throw new \Exception('Migration failed with output: ' . $output);
            }
            
            return response()->json([
                'message' => 'Database has been reset and reseeded successfully!',
                'migrate_output' => $output,
                'status' => 'success'
            ]);
        } catch (\Exception $e) {
            Log::error('Database reset failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'message' => 'Failed to reset database',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
