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
        $domains = self::table()->get();

        return $domains;
    }

    public static function findOrFail(int $id): Domain
    {
        $rawDomain = self::table()->where('id', $id)->findOrFail();
        $domain = self::rowToDomain($rawDomain);

        return $domain;
    }

    public static function findByDomainName(string $domainName): ?Domain
    {
        $rawDomain = self::table()->where('name', $domainName)->first();
        if (empty($rawDomain)) {
            return null;
        }

        $domain = self::rowToDomain($rawDomain);

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

    public static function create(string $domainName)
    {
        $currentTime = Carbon::now();
        $domainId = self::table()->insertGetId(
            ['name' => $domainName, 'created_at' => $currentTime, 'updated_at' => $currentTime]
        );

        return $domainId;
    }

    protected static function table()
    {
        return DB::table(self::TABLE_NAME);
    }

    protected static function rowToDomain($row)
    {
        $domain = Domain::fromStdObject($row);

        return $domain;
    }
}
