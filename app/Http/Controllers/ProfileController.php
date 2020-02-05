<?php

namespace App\Http\Controllers;

use App\Core\ElasticClient;
use Illuminate\Http\Request;

class ProfileController extends Controller
{


    //
    public function index(Request $request)
    {

        $query = $this->buildQuery($request);
        try {
            $response = (new ElasticClient)->search($query);
            return (new \App\Core\Mappers\SearchResponseMapper($response))->buildPayload();
        } catch (\Exception $ex) {
            return ['success' => false, "errors" => [$ex->getMessage()]];
        }
    }

    public function count(Request $request)
    {
        $query = $this->buildQuery($request, false);
        $response = (new ElasticClient)->count($query);
        if ($response['count']) {
            return ['success' => true, 'count' => $response['count']];
        }

        return ['success' => false];
    }

    private function platformValue($platformName)
    {
        switch ($platformName) {
            case 'youtube':
                return 'yt';
            case 'instagram':
                return 'ig';
            case 'tiktok':
                return 'tt';
            default:
                return $platformName;
        }
    }


    private function buildQuery(Request $request, $pagination = true)
    {
        $pageSize = env('PAGE_SIZE', 100);

        $query = [];
        if ($request->get('q')) {
            $query = [
                'query' => [
                    'bool' => [
                        'filter' => [
                            'multi_match' => [
                                'type' => 'phrase',
                                'query' => '"' . $request->get('q') . '"',
                            ]
                        ]
                    ]
                ]
            ];
        }

        if ($pagination) {
            $query['sort'] =
                [
                    "followers" => 'desc'
                ];
            $query['size'] = $pageSize;
        }

        if ($request->get('platforms')) {
            $platforms = explode('-', $request->get('platforms'));
            $shoulds = [];
            foreach ($platforms as $p) {
                $p = $this->platformValue($p);
                if (empty($p)) {
                    continue;
                }

                $shoulds[] = [
                    'match_phrase' => [
                        'platform' => $p
                    ]
                ];
            }

            if (count($shoulds) > 0) {
                $query['query']['bool']['must']['bool'] = [
                    'should' => $shoulds,
                    'minimum_should_match' => 1
                ];
            }
        }

        if ($request->get('page_no')) {
            $pageNo = $request->get('page_no') > 4 ? 4 : $request->get('page_no');
            $query['from'] = $pageNo * $pageSize;
        }
        return $query;
    }
}