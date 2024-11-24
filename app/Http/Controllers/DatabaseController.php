<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use App\Models\Person;
use App\Models\Activity;
use App\Models\ListItem;
use App\Models\CustomList;
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
        try {
            Log::info('Starting database reset process');
            
            // Disable foreign key checks
            DB::statement('PRAGMA foreign_keys = OFF');
            
            // Clear all tables in the correct order
            DB::table('list_items')->truncate();
            DB::table('custom_lists')->truncate();
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
            
            return response()->json([
                'message' => 'Database has been reset and reseeded successfully!',
                'status' => 'success'
            ]);
        } catch (\Exception $e) {
            Log::error('Database reset failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Make sure to re-enable foreign keys even if there's an error
            DB::statement('PRAGMA foreign_keys = ON');
            
            return response()->json([
                'message' => 'Failed to reset database: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
