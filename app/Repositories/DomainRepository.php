<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use App\Models\Domain;
use Carbon\Carbon;

class DomainRepository
{
    const TABLE_NAME = 'domains';

    public static function all()
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

    public static function delete(Domain $domain)
    {
        return self::table()->where('id', $domain->id)->delete();
    }

    protected static function table()
    {
        return DB::table(self::TABLE_NAME);
    }

    protected static function rawToDomain($row)
    {
        $domain = Domain::fromStdObject($row);

        return $domain;
    }
}
