<?php

namespace App\Repositories;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use App\Models\Domain;

class DomainRepository
{
    const TABLE_NAME = 'domains';

    public static function all(): Collection
    {
        $rawDomains = self::table()->get();
        $domains = $rawDomains->map(function ($rawDomain) {
            return self::rawToDomain($rawDomain);
        });

        return $domains;
    }

    public static function findOrFail(int $id): Domain
    {
        $rawDomain = self::table()->where('id', $id)->findOrFail();
        $domain = self::rawToDomain($rawDomain);

        return $domain;
    }

    public static function findByDomainName(string $domainName): ?Domain
    {
        $rawDomain = self::table()->where('name', $domainName)->first();
        if (empty($rawDomain)) {
            return null;
        }

        $domain = self::rawToDomain($rawDomain);

        return $domain;
    }

    public static function store(Domain $domain): Domain
    {
        $domainId = self::table()->insertGetId(
            ['name' => $domain->name, 'created_at' => $domain->createdAt, 'updated_at' => $domain->updatedAt]
        );
        $domain->id = $domainId;

        return $domain;
    }

    protected static function table(): Builder
    {
        return DB::table(self::TABLE_NAME);
    }

    protected static function rawToDomain($row): Domain
    {
        $domain = Domain::fromStdObject($row);

        return $domain;
    }
}
