<?php

namespace App\Repositories;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use App\Models\DomainCheck;
use App\Models\Domain;

class DomainCheckRepository
{
    const TABLE_NAME = 'domain_checks';

    public static function allForDomainNewerFirst(Domain $domain): Collection
    {
        $rawDomainChecks = self::table()
            ->where('domain_id', $domain->id)
            ->orderBy('updated_at', 'desc')
            ->get();

        $domainChecks = self::rawCollectionToDomainChecks($rawDomainChecks);

        return $domainChecks;
    }

    public static function latestForDomains(Collection $domains): Collection
    {
        $domainIds = $domains->pluck('id')->toArray();
        $rawDomainChecks = self::table()
            ->whereIn('id', function ($query) {
                $query->select(DB::raw('MAX(id) AS id'))
                    ->from(self::TABLE_NAME)
                    ->groupBy('domain_id');
            })
            ->whereIn('domain_id', $domainIds)
            ->get();

        $domainChecks = self::rawCollectionToDomainChecks($rawDomainChecks);

        return $domainChecks;
    }

    public static function store(DomainCheck $domainCheck): DomainCheck
    {
        $id = self::table()->insertGetId(
            [
                'domain_id' => $domainCheck->domainId,
                'status_code' => $domainCheck->statusCode,
                'keywords' => $domainCheck->keywords,
                'description' => $domainCheck->description,
                'h1' => $domainCheck->h1,
                'created_at' => $domainCheck->createdAt,
                'updated_at' => $domainCheck->updatedAt,
            ]
        );
        $domainCheck->id = $id;

        return $domainCheck;
    }

    protected static function table(): Builder
    {
        return DB::table(self::TABLE_NAME);
    }

    protected static function rawCollectionToDomainChecks($rawDomainChecks): Collection
    {
        $domainChecks = $rawDomainChecks->map(function ($rawDomainCheck) {
            return self::rawToDomainCheck($rawDomainCheck);
        });

        return $domainChecks;
    }

    protected static function rawToDomainCheck($raw): DomainCheck
    {
        $domainCheck = DomainCheck::fromStdObject($raw);

        return $domainCheck;
    }
}
