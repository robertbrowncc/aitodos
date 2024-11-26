<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use App\Models\Person;
use App\Models\Activity;
use App\Models\ChecklistItem;
use App\Models\Checklist;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Database\Seeders\TodoSeeder;
use Database\Seeders\ListSeeder;
use Database\Seeders\PersonSeeder;
use Database\Seeders\ActivitySeeder;

class DatabaseController extends Controller
{
    public function resetDatabase()
    {
        // Only allow database reset in local environment
        if (app()->environment() !== 'local') {
            Log::warning('Attempted database reset in non-local environment');
            return response()->json(
                ['error' => 'Database reset is only available in local environment'],
                Response::HTTP_FORBIDDEN
            );
        }

        try {
            Log::info('Starting database reset process');
            
            // Disable foreign key checks
            DB::statement('PRAGMA foreign_keys = OFF');
            
            // Clear all tables in the correct order
            DB::table('checklist_items')->truncate();
            DB::table('checklists')->truncate();
            DB::table('todos')->truncate();
            DB::table('activities')->truncate();
            DB::table('people')->truncate();
            
            // Re-enable foreign key checks
            DB::statement('PRAGMA foreign_keys = ON');
            
            Log::info('All tables cleared');
            
            // Run seeders
            $seeders = [
                PersonSeeder::class,
                TodoSeeder::class,
                ActivitySeeder::class,
                ListSeeder::class,
            ];
            
            foreach ($seeders as $seeder) {
                $instance = new $seeder;
                $instance->run();
            }
            
            Log::info('Database reset completed successfully');
            return response()->json(['message' => 'Database reset successfully']);
            
        } catch (\Exception $e) {
            Log::error('Error resetting database: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to reset database'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
