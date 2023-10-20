<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\TruJay\TruJayAdapter;
use http\Client\Response;
use Illuminate\Http\Request;

class TruJayController extends Controller
{
    public function getEntities(Request $request){
        $result = [];

        $params = [
            'page' => $request->page,
            'pageSize' => $request->pageSize,
            'filter' => $request->filter
        ];

        $adapter = new TruJayAdapter();
        if (isset($request->applicationKey) && !empty($request->applicationKey)) {
            $adapter->setApplicationKey($request->applicationKey);
            $result = $adapter->request('list', $params);
        } else{
            return response()->json(['error' => 'Empty application key'], 400);
        }

        return response()->json($result, 200);
    }

    public function getEntityCount(Request $request, $entity){
        $result = [];

        $params = [
            'page' => $request->page,
            'pageSize' => $request->pageSize,
            'filter' => $request->filter
        ];

        $adapter = new TruJayAdapter();
        if (isset($request->applicationKey) && !empty($request->applicationKey)) {
            $adapter->setApplicationKey($request->applicationKey);
            $result = $adapter->request($entity . '/count', $params);
        } else{
            return response()->json(['error' => 'Empty application key'], 400);
        }

        return response()->json($result, 200);
    }

    public function getEntityCustomFields(Request $request, $entity){
        $result = [];
        $count = 0;
        $adapter = new TruJayAdapter();
        if (isset($request->applicationKey) && !empty($request->applicationKey)) {
            $adapter->setApplicationKey($request->applicationKey);
            $response = $adapter->request($entity . '/describe');
        } else{
            return response()->json(['error' => 'Empty application key'], 400);
        }

        if (isset($response['schema']['fetchAll']['properties'])){
            foreach ($response['schema']['fetchAll']['properties'] as $key => $field){
                if (isset($field['extra']['role']) && $field['extra']['role'] === 'custom'){
                    $result['fields'][] = [
                        'key' => $key,
                        'title' => $field['title']
                    ];
                    $count++;
                }
            }
            $result['count'] = $count;
        }

        return response()->json($result, 200);
    }

}
