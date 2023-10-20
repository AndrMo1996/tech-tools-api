<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\Jira\JiraAdapter;
use App\Services\Jira\Modules\WorkHours;
use http\Client\Response;
use Illuminate\Http\Request;

class JiraController extends Controller
{
    public function getWorkhours(Request $request){
        $result = [];
        $adapter = new WorkHours($request->fromDate, $request->toDate);
        $result = $adapter->getWorkhours();

        return response()->json($result, 200);
    }

    public function createSubtask(Request $request){
        $subtaskBody = [
            "fields" => [
                "project" => [
                    "key" => 'TRUJAY'
                ],
                "parent" => [
                    "key" => 'TRUJAY-' . $request->taskId
                ],
                "summary" => $request->entity,
                "description" => "Check " . $request->entity,
                "duedate" => date('Y-m-d'),
                "issuetype" => [
                    "name" => "Technical task"
                ],
                "components" => [
                    [
                        "name" => "Data2CRM.Migration"
                    ]
                ],
            ]
        ];


        $assignee = [
            "accountId" => "61e7f20298cd6100705107fc"
        ];

        $adapter = new JiraAdapter();
        $subtask = $adapter->createSubtask($subtaskBody);
        if (isset($subtask['status']) && $subtask['status'] === 'error'){
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong'
            ], 500);
        }

        $adapter->assigneeSubtask($subtask['id'], $assignee);
        return response()->json($subtask, 201);
    }
}
