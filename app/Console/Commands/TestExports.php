<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Exports\CustomerExport;
use App\Exports\MaterialExport;
use App\Exports\OrderExport;
use App\Exports\PurchaseExport;
use App\Exports\StockMovementExport;
use App\Exports\PaymentExport;

class TestExports extends Command
{
    protected $signature = 'test:exports';
    protected $description = 'Test all export classes';

    public function handle()
    {
        $this->info("\n=== TESTING ALL EXPORT CLASSES ===\n");

        $exports = [
            'CustomerExport' => CustomerExport::class,
            'MaterialExport' => MaterialExport::class,
            'OrderExport' => OrderExport::class,
            'PurchaseExport' => PurchaseExport::class,
            'StockMovementExport' => StockMovementExport::class,
            'PaymentExport' => PaymentExport::class,
        ];

        $passed = 0;
        $failed = 0;

        foreach ($exports as $name => $class) {
            try {
                $export = new $class();
                $data = $export->array();
                $title = method_exists($export, 'title') ? $export->title() : 'N/A';
                
                $this->line("✓ <fg=green>{$name}</>");
                $this->line("  Title: <fg=cyan>{$title}</>");
                $this->line("  Rows: <fg=yellow>" . count($data) . "</>");
                
                if (isset($data[0])) {
                    $firstRow = array_slice($data[0], 0, 3);
                    $this->line("  First Row: <fg=blue>" . implode(', ', $firstRow) . "...</>");
                }
                
                $this->line("");
                $passed++;
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
