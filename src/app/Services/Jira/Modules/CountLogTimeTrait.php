<?php

namespace App\Services\Jira\Modules;

use Illuminate\Support\Facades\Http;

trait CountLogTimeTrait
{
    public function logWorkTime(string $id, array $workLog) : array {
        if($workLog['total'] > $workLog['maxResults']){
            return $this->countWorkTime($this->getWorkLog($id));
        } else {
            return $this->countWorkTime($workLog['worklogs']);
        }
    }

    public function countWorkTime(array $worklogs){
        $total = [];
        foreach ($worklogs as $log) {
            if (strtotime($log['started']) >= strtotime($this->fromDate) && strtotime($log['started']) <= strtotime($this->toDate)) {
                if(isset($total[$log['author']['displayName']])) {
                    $total[$log['author']['displayName']] += $log['timeSpentSeconds'];
                } else{
                    $total[$log['author']['displayName']] = $log['timeSpentSeconds'];
                }
            }
        }
        return $total;
    }

    public function getWorkLog($id): array {
        $response = Http::withBasicAuth(config('services.jira.username'), config('services.jira.token'))
            ->get(config('services.jira.baseApiUrlV3') . 'issue/' . $id . '/worklog')
            ->json();
        return $response['worklogs'];
    }
}
