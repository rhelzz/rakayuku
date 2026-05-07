<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckExportData extends Command
{
    protected $signature = 'check:export-data';
    protected $description = 'Check data count in database vs exported data';

    public function handle()
    {
        $this->info("\n=== CHECKING DATA COUNT ===\n");

        $models = [
            'Customer' => \App\Models\Customer::class,
            'Material' => \App\Models\Material::class,
            'Order' => \App\Models\Order::class,
            'Purchase' => \App\Models\Purchase::class,
            'StockMovement' => \App\Models\StockMovement::class,
            'Payment' => \App\Models\Payment::class,
        ];

        $data = [];
        foreach ($models as $name => $class) {
            $count = $class::count();
            $data[$name] = $count;
            $this->line("<fg=cyan>{$name}</> Records: <fg=yellow>{$count}</>");
        }

        $this->info("\n=== EXPORT CLASSES CHECK ===\n");

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
                $allRows = $export->array();
                $dataRows = array_slice($allRows, 2);
                $actualData = 0;
                foreach ($dataRows as $row) {
                    if (!empty(array_filter($row))) {
                        $actualData++;
                    }
                }
                
                $this->line("<fg=cyan>{$name}</> Total Rows: <fg=yellow>" . count($allRows) . "</>, Data Rows: <fg=yellow>{$actualData}</>");
            } catch (\Exception $e) {
                $this->line("<fg=red>{$name}</> ERROR: {$e->getMessage()}");
            }
        }

        $this->info("\n=== COMPLETE ===\n");
    }
}
