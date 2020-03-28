<?php

namespace App\Services;

use DiDom\Document;
use App\Repositories\DomainCheckRepository;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;

class PageAnalysisService
{
    public static function analyze($domain)
    {
        $client = resolve('HttpClient');
        $analysisData = ['keywords' => null, 'description' => null, 'h1' => null];

        try {
            $response = $client->get($domain->name);
            $statusCode = $response->getStatusCode();
            $body = $response->getBody();

            $document = new Document((string) $body);
            $metaTags = collect($document->find('meta'));
            $keywords = $metaTags->first(function ($meta) {
                $name = $meta->getAttribute('name');
                return $name && strtolower($name) === 'keywords';
            });
            $analysisData['keywords'] = $keywords ? $keywords->getAttribute('content') : null;

            $description = $metaTags->first(function ($meta) {
                $name = $meta->getAttribute('name');
                return $name && strtolower($name) === 'description';
            });
            $analysisData['description'] = $description ? $description->getAttribute('content') : null;

            $h1 = collect($document->find('h1'))->first();
            $analysisData['h1'] = $h1 ? $h1->innerHtml() : null;
        } catch (ClientException $e) {
            $statusCode = $e->getResponse()->getStatusCode();
        } catch (ConnectException $e) {
            return false;
        }

        return DomainCheckRepository::create($domain->id, $statusCode, $analysisData);
    }
}
