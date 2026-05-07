<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class PreviewExports extends Command
{
    protected $signature = 'export:preview';
    protected $description = 'Preview all export data structure';

    public function handle()
    {
        $this->info("\n=== EXPORT DATA PREVIEW ===\n");

        $exports = [
            'CustomerExport' => \App\Exports\CustomerExport::class,
            'MaterialExport' => \App\Exports\MaterialExport::class,
            'OrderExport' => \App\Exports\OrderExport::class,
            'PurchaseExport' => \App\Exports\PurchaseExport::class,
            'StockMovementExport' => \App\Exports\StockMovementExport::class,
            'PaymentExport' => \App\Exports\PaymentExport::class,
        ];

        foreach ($exports as $name => $class) {
            try {
                $export = new $class();
                $data = $export->array();
                
                $this->line("<fg=cyan>========== {$name} ==========</>");
                
                if (isset($data[0])) {
                    $this->line("📋 Title: " . implode(' | ', $data[0]));
                }
                
                if (isset($data[1])) {
                    $this->line("📌 Headers: " . implode(' | ', array_slice($data[1], 0, 5)) . (count($data[1]) > 5 ? '...' : ''));
                }
                
                $dataRowCount = 0;
                for ($i = 2; $i < count($data) - 2; $i++) {
                    if (!empty(array_filter($data[$i]))) {
                        $dataRowCount++;
                    }
                }
                
                $this->line("📊 Data Rows: <fg=yellow>{$dataRowCount}</>");
                $this->line("📈 Total Rows: <fg=yellow>" . count($data) . "</>");
                $this->line("");
                
            } catch (\Exception $e) {
                $this->line("<fg=red>{$name} ERROR: {$e->getMessage()}</>");
            }
        }

        $this->info("=== PREVIEW COMPLETE ===\n");
    }
}
