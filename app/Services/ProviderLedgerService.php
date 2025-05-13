<?php 

namespace App\Services;

use App\Models\ProviderLedger;
use Illuminate\Support\Facades\Log;

class ProviderLedgerService
{
    public static function log($providerId, $type, $ref, $debit, $credit)
    {
        Log::info("ProviderLedgerService called", [
            'provider_id' => $providerId,
            'type' => $type,
            'reference' => $ref,
            'debit' => $debit,
            'credit' => $credit
        ]);

        try {
            $last = ProviderLedger::where('provider_id', $providerId)->latest()->first();
            $lastBalance = $last ? $last->balance : 0;
            $balance = $lastBalance + $debit - $credit;

            $entry = ProviderLedger::create([
                'provider_id' => $providerId,
                'type' => $type,
                'reference' => $ref,
                'date' => now(),
                'debit' => $debit,
                'credit' => $credit,
                'balance' => $balance
            ]);

            Log::info("Provider ledger entry created", $entry->toArray());
            return $entry;

        } catch (\Exception $e) {
            Log::error("ProviderLedgerService error: " . $e->getMessage());
        }
    }
}
