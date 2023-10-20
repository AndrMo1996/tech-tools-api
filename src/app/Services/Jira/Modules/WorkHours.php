<?php

namespace App\Services\Jira\Modules;

use App\Services\Jira\JiraAdapter;
use Illuminate\Support\Facades\Http;

class WorkHours extends JiraAdapter
{
    use CountLogTimeTrait;

    const PATH = 'search';
    const TASK_TYPES = '"Upsell - T&M","Migration - Guided","Migration Package","Migration T&M - CSM","Migration T&M - Tech","SaaS","Wizard Customization","Admin Program","HS Asset Migration","HS Consulting","HS Onboarding","HS PSO","HS SOLD Package","Incubate3","Scoping","Tech Support","Technical task","Non-billable subtask","Non-billable time","Trujay Time Off"';

    const BILLABLE_TASKS = [
        "Upsell - T&M",
        "Migration - Guided",
        "Migration Package",
        "Migration T&M - CSM",
        "Migration T&M - Tech",
        "SaaS",
        "Wizard Customization",
        "Admin Program",
        "HS Asset Migration",
        "HS Consulting",
        "HS Onboarding",
        "HS PSO",
        "HS SOLD Package",
        "Incubate3"
    ];

    const NON_BILLABLE_TASKS = [
        "Scoping",
        "Tech Support"
    ];

    const FIELDS = [
        "id",
        "worklog",
        "assignee",
        "reporter",
        "summary",
        "timetracking",
        "issuetype",
        "status"
    ];

    public function getWorkhours(): array
    {
//        $filter = "project = " . config('services.jira.jiraProject') . " AND assignee in (membersOf(\"Data2CRM Tech Engineers\")) AND (status != Closed AND status != Resolved OR resolved >= $this->fromDate) AND type in (" . static::TASK_TYPES . ") ORDER BY created ASC";
        $filter = "project = " . config('services.jira.jiraProject') . " AND assignee in (membersOf(\"Data2CRM Tech Engineers\")) AND worklogDate >= \"$this->fromDate\" AND worklogDate <= \"$this->toDate\" AND type in (" . static::TASK_TYPES . ") ORDER BY created ASC";


        $response = parent::getAllRecords($filter);

        return $this->formatData($response);
    }

    private function formatData(array $data): array{
        $resultData = [
            'billable' => [],
            'non_billable' => []
        ];
        $totalWorkHours = [
            'billable' => [],
            'non_billable' => []
        ];

        foreach ($data as $record) {

            $isClosed = false;
            if(isset($record['fields']['status']) && ($record['fields']['status']['name'] === 'Closed' || $record['fields']['status']['name'] === 'Resolved')){
                $isClosed = true;
            }

            $timeSpent = $this->logWorkTime($record['id'], $record['fields']['worklog']);

            foreach ($timeSpent as $assignee => $time) {

                $time = round(($time / 3600), 2);
                if(isset($record['fields']['timetracking']['originalEstimateSeconds'])) {
                    $originalTime = round(($record['fields']['timetracking']['originalEstimateSeconds'] / 3600), 2);
                } else{
                    $originalTime = 0;
                }

                if ($time > 0) {
                    $result = [
                        'summary' => $record['fields']['summary'],
                        'type' => $record['fields']['issuetype']['name'],
                        'closed' => $isClosed,
                        'reporter' => $record['fields']['reporter']['displayName'],
                        'assignee' => $assignee,
                        'estimate' => $originalTime ?? 0,
                        'logHours' => $time,
                        'taskUrl' => config('services.jira.baseUrl') . $record['key']
                    ];

                    if (in_array($record['fields']['issuetype']['name'], self::BILLABLE_TASKS)){
                        $resultData['billable'][] = $result;
                        if(isset($totalWorkHours['billable'][$assignee])) {
                            $totalWorkHours['billable'][$assignee] = round($totalWorkHours['billable'][$assignee] + $time, 2);
                        } else{
                            $totalWorkHours['billable'][$assignee] = $time;
                        }
                    } else{
                        $resultData['non_billable'][] = $result;
                        if(isset($totalWorkHours['non_billable'][$assignee])) {
                            $totalWorkHours['non_billable'][$assignee] = round($totalWorkHours['non_billable'][$assignee] + $time, 2);
                        } else{
                            $totalWorkHours['non_billable'][$assignee] = $time;
                        }
                    }
                }
            }
        }

        $resultData['total_billable'] = [$totalWorkHours['billable']];
        $resultData['total_non_billable'] = [$totalWorkHours['non_billable']];
        return $resultData;
    }
}
