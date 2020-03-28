<?php

namespace App\Services;

use DiDom\Document;
use App\OperationResult;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;

class PageFetchingService
{
    public static function fetch(string $url)
    {
        $client = resolve('HttpClient');
        $fetchResult = collect(['statusCode' => null, 'body' => '']);
        $operationResult = new OperationResult();
        $operationResult->succeed();

        try {
            $response = $client->get($url);

            $fetchResult->put('statusCode', $response->getStatusCode());
            $fetchResult->put('body', (string) $response->getBody());
        } catch (ClientException $e) {
            $fetchResult->put('statusCode', $e->getResponse()->getStatusCode());
        } catch (ConnectException $e) {
            $operationResult->fail();
        }

        $operationResult->setResult($fetchResult);

        return $operationResult;
    }
}
