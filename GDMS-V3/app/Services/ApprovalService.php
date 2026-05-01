<?php

namespace App\Services;

use App\Models\Approval;
use App\Models\Sale;
use App\Models\CashSubmission;

class ApprovalService
{
    public function approveSale(Sale $sale): void
    {
        $sale->update(['status' => 'approved']);
        
        Approval::create([
            'approvable_type' => Sale::class,
            'approvable_id' => $sale->id,
            'action' => 'approved',
        ]);
    }

    public function querySale(Sale $sale, string $note): void
    {
        $sale->update(['status' => 'queried']);
        
        Approval::create([
            'approvable_type' => Sale::class,
            'approvable_id' => $sale->id,
            'action' => 'queried',
            'note' => $note,
        ]);
    }

    public function approveCash(CashSubmission $submission): void
    {
        $submission->update(['status' => 'approved']);
        
        Approval::create([
            'approvable_type' => CashSubmission::class,
            'approvable_id' => $submission->id,
            'action' => 'approved',
        ]);
    }

    public function queryCash(CashSubmission $submission, string $note): void
    {
        $submission->update(['status' => 'queried']);
        
        Approval::create([
            'approvable_type' => CashSubmission::class,
            'approvable_id' => $submission->id,
            'action' => 'queried',
            'note' => $note,
        ]);
    }
}