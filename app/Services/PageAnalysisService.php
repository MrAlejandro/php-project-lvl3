<?php

namespace App\Services;

use App\Repositories\DomainCheck;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;

class PageAnalysisService
{
    public static function analyze($domain)
    {
        $client = resolve('HttpClient');

        try {
            $response = $client->get($domain->name);
            $statusCode = $response->getStatusCode();
        } catch (ClientException $e) {
            $statusCode = $e->getResponse()->getStatusCode();
        } catch (ConnectException $e) {
            return false;
        }

        return DomainCheck::create($domain->id, $statusCode);
    }
}
