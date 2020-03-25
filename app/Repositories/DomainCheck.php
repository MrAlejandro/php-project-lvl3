<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DomainCheck
{
    const TABLE_NAME = 'domain_checks';

    public static function allForDomainNewerFirst($domainId)
    {
        $domainChecks = self::table()
            ->where('domain_id', $domainId)
            ->orderBy('updated_at', 'desc')
            ->get();

        return $domainChecks;
    }

    public static function latestForDomains($domainIds)
    {
        $domainChecks = self::table()
            ->whereIn('id', function ($query) {
                $query->select(DB::raw('MAX(id) AS id'))
                    ->from(self::TABLE_NAME)
                    ->groupBy('domain_id');
            })
            ->whereIn('domain_id', $domainIds)
            ->get();

        return $domainChecks;
    }

    public static function create($domainId, $statusCode = 200, $analysisData = ['description' => null, 'keywords' => null, 'h1' => null])
    {
        $currentTime = Carbon::now();
        $domainCheckId = self::table()->insertGetId(
            [
                'domain_id' => $domainId,
                'status_code' => $statusCode,
                'keywords' => $analysisData['keywords'],
                'description' => $analysisData['description'],
                'h1' => $analysisData['h1'],
                'created_at' => $currentTime,
                'updated_at' => $currentTime,
            ]
        );

        return $domainCheckId;
    }

    protected static function table()
    {
        return DB::table(self::TABLE_NAME);
    }
}
