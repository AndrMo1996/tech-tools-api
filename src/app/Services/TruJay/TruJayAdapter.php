<?php

namespace App\Services\TruJay;

use Illuminate\Support\Facades\Http;

class TruJayAdapter
{
    private $applicationKey;

    public function request(string $path, array $params = []): array{
        $response = Http::withHeaders([
            'content-type'              => 'application/json',
            'X-API2CRM-USER-KEY'        => config('services.trujay.userKey'),
            'X-API2CRM-DATA-ENABLE'     => "true",
            'X-API2CRM-APPLICATION-KEY' => $this->applicationKey,
        ])->get(config('services.trujay.baseApiUrl') . $path, $params);

        if(isset($response->headers()['X-API2CRM-DATA-STATUS']) && $response->headers()['X-API2CRM-DATA-STATUS'][0] === 'failed'){
            return [
                'cacheStatus' => 'failed'
            ];
        }

        if(isset($response->headers()['X-API2CRM-DATA-STATUS']) && $response->headers()['X-API2CRM-DATA-STATUS'][0] !== 'done'){
            return [
                'cacheStatus' => 'in process'
            ];
        }

        return $response->json();
    }

    public function setApplicationKey(string $applicationKey){
        $this->applicationKey = $applicationKey;
    }


}
