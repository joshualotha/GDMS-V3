<?php

namespace App\Console\Commands;

use App\Services\DepreciationService;
use Illuminate\Console\Command;

class RunDepreciation extends Command
{
    protected $signature = 'depreciation:run
                            {--catch-up : Catch up all missed depreciation from purchase date instead of running just the current month}
                            {--asset= : Run depreciation for a specific asset ID only}';

    protected $description = 'Run monthly depreciation or catch up missed depreciation for all active assets';

    public function handle(DepreciationService $depreciationService): int
    {
        $assetId = $this->option('asset') ? (int) $this->option('asset') : null;
        $catchUp = $this->option('catch-up');

        if ($catchUp) {
            $this->info('Running depreciation catch-up...');
            $results = $depreciationService->catchUpDepreciation($assetId);
        } else {
            $this->info('Running monthly depreciation...');
            $results = $depreciationService->runReducingBalance($assetId);
        }

        if (empty($results)) {
            $this->warn('No assets were processed. All assets may already be up to date.');
            return Command::SUCCESS;
        }

        $this->newLine();
        $this->table(
            ['Asset #', 'Name', $catchUp ? 'Months Caught Up' : 'Depreciation', 'New Book Value'],
            array_map(function ($r) use ($catchUp) {
                return [
                    $r['asset_number'] ?? 'N/A',
                    $r['name'] ?? 'N/A',
                    $catchUp
                        ? ($r['months_caught_up'] ?? 0) . ' months'
                        : number_format($r['depreciation'] ?? 0, 2),
                    number_format($r['new_book_value'] ?? $r['final_book_value'] ?? 0, 2),
                ];
            }, $results)
        );

        $count = count($results);
        $total = $catchUp
            ? array_sum(array_column($results, 'total_depreciation'))
            : array_sum(array_column($results, 'depreciation'));

        $this->newLine();
        $this->info("Processed {$count} asset(s). Total depreciation: " . number_format($total, 2));

        return Command::SUCCESS;
    }
}
