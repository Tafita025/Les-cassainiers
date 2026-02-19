<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PapiPaymentService
{
    public function createPaymentLink(array $data): array
    {
        /*
         * $data attendu :
         * [
         *   'amount'      => 150000,          ← montant en MGA (entier)
         *   'client_name' => 'Jean Dupont',   ← nom du client
         *   'reference'   => 'CASAINIERS-XXX',← référence unique de la commande
         *   'email'       => 'jean@mail.com', ← email du client
         * ]
         */

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Token'        => config('papi.api_key'),
        ])
        ->withoutVerifying()  
        ->post(config('papi.api_url') . '/dashboard/api/payment-links', [
            'amount'          => (float) $data['amount'],
            'clientName'      => $data['client_name'],
            'reference'       => $data['reference'],
            'description'     => 'Commande ' . $data['reference'] . ' — Les Casainiers',
            'successUrl'      => config('papi.success_url') . '?ref=' . $data['reference'],
            'failureUrl'      => config('papi.failure_url') . '?ref=' . $data['reference'],
            'notificationUrl' => config('papi.callback_url'),
            'payerEmail'      => $data['email'],
            'validDuration'   => 30,   // 30 minutes pour payer
            'isTestMode'      => app()->environment('local', 'staging'),
        ]);

        // Log pour debug en développement
        Log::info('Papi request payload:', [
            'reference' => $data['reference'],
            'amount'    => $data['amount'],
        ]);
        Log::info('Papi response:', $response->json() ?? []);

        if ($response->failed()) {
            throw new \Exception(
                'Erreur Papi (' . $response->status() . ') : ' . $response->body()
            );
        }

        return $response->json();
    }
}