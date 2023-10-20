<?php

namespace App\Services\Jira;

use Illuminate\Support\Facades\Http;

class JiraAdapter
{
    const PATH = '';
    const FIELDS = [];
    const EXPAND = [];
    const TASK_TYPES = '';

    public function __construct(string $fromDate = null, string $toDate = null)
    {
        if(empty($fromDate) || empty($toDate)){
            $this->fromDate = date('Y-m') . '-01';
            $this->toDate = date('Y-m', strtotime('+1 month')) . '-01';
        } else{
            $this->fromDate = $fromDate;
            $this->toDate = $toDate;
        }
    }

    public function getAllRecords($filter){
        $startAt = 0;
        $maxResult = 100;
        $data = [];

        do{
            $response = $this->fetchALL($startAt, $maxResult, $filter);
            if (!empty($response['issues'])){
                $data = array_merge($data, $response['issues']);
            }
            $startAt += $maxResult;
        } while(count($response['issues']) === $maxResult);
        return $data;
    }

    public function fetchALL(int $startAt, int $maxResult, string $jqlFilter, array $fields = []): array{
            $response = Http::withBasicAuth(config('services.jira.username'), config('services.jira.token'))->withHeaders([
                'content-type'  => 'application/json',
            ])->post(config('services.jira.baseApiUrlV3') . static::PATH, [
                "jql" => $jqlFilter,
                "startAt" => $startAt,
                "maxResults" => $maxResult,
                "fields" => static::FIELDS,
                "expand" => static::EXPAND
            ])->json();
        return $response;
    }

    public function createSubtask(array $body){

        $response = Http::withBasicAuth(config('services.jira.username'), config('services.jira.token'))->withHeaders([
            'content-type'  => 'application/json',
        ])->post(config('services.jira.baseApiUrlV2') . 'issue', $body);

        if ($response->status() === 201){
            return $response->json();
        }

        return [
            'status' => 'error',
            'message' => 'Something went wrong'
        ];
    }

    public function assigneeSubtask(string $taskId, array $body){

        $response = Http::withBasicAuth(config('services.jira.username'), config('services.jira.token'))->withHeaders([
            'content-type'  => 'application/json',
        ])->put(config('services.jira.baseApiUrlV3') . 'issue/' . $taskId . '/assignee', $body);
        if ($response->status() === 204){
            return [
                'status' => 'success',
                'message' => 'Something went wrong'
            ];
        }
        return [
            'status' => 'error',
            'message' => 'Something went wrong'
        ];
    }

}
