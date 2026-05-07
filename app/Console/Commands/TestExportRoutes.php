<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\ReportController;
use App\Services\ReportService;

class TestExportRoutes extends Command
{
    protected $signature = 'test:export-routes';
    protected $description = 'Test all export routes';

    public function handle()
    {
        $this->info("\n=== TESTING ALL EXPORT ROUTES ===\n");

        $routes = [
            'Customer Export' => ['controller' => CustomerController::class, 'method' => 'export'],
            'Material Export' => ['controller' => MaterialController::class, 'method' => 'export'],
            'Order Export' => ['controller' => OrderController::class, 'method' => 'export'],
            'Purchase Export' => ['controller' => PurchaseController::class, 'method' => 'export'],
            'Stock Movement Export' => ['controller' => StockMovementController::class, 'method' => 'export'],
            'Payment Export' => ['controller' => ReportController::class, 'method' => 'exportPayments', 'service' => ReportService::class],
        ];

        $passed = 0;
        $failed = 0;

        foreach ($routes as $name => $config) {
            try {
                $controller = app($config['controller']);
                
                if (isset($config['service'])) {
                    $service = app($config['service']);
                    $response = $controller->{$config['method']}();
                } else {
                    $response = $controller->{$config['method']}();
                }
                
                if ($response && method_exists($response, 'getFile')) {
                    $file = $response->getFile();
                    $filename = $file->getFilename();
                    
                    $this->line("✓ <fg=green>{$name}</>");
                    $this->line("  File: <fg=cyan>{$filename}</>");
                    $this->line("  Size: <fg=yellow>" . filesize($file->getPathname()) . " bytes</>");
                    $this->line("");
                    $passed++;
                } else if (is_object($response)) {
                    $this->line("✓ <fg=green>{$name}</>");
                    $this->line("  Response Type: <fg=cyan>" . class_basename($response) . "</>");
                    $this->line("");
                    $passed++;
                }
            } catch (\Exception $e) {
                $this->line("✗ <fg=red>{$name}</>");
                $this->line("  ERROR: <fg=red>{$e->getMessage()}</>");
                $this->line("");
                $failed++;
            }
        }

        $this->info("=== TEST RESULTS ===");
        $this->line("<fg=green>Passed: {$passed}</>");
        if ($failed > 0) {
            $this->line("<fg=red>Failed: {$failed}</>");
        }
        $this->info("=== TEST COMPLETE ===\n");

        return $failed === 0 ? 0 : 1;
    }
}
