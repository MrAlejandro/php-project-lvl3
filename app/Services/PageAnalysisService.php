<?php

namespace App\Services;

use ErrorException;
use DiDom\Document;
use App\OperationResult;

class PageAnalysisService
{
    public static function analyze(string $pageHtml)
    {
        $operationResult = new OperationResult();
        $analysisResult = collect(['h1' => null, 'keywords' => null, 'description' => null]);
        $operationResult->succeed();

        try {
            $document = new Document($pageHtml);

            $analysisResult->put('h1', self::extractH1TagContent($document));

            $metaTags = collect($document->find('meta'));
            $analysisResult->put('keywords', self::extractMetaTagContent($metaTags, 'keywords'));
            $analysisResult->put('description', self::extractMetaTagContent($metaTags, 'description'));
        } catch (ErrorException $e) {
            $operationResult->fail();
        }

        $operationResult->setResult($analysisResult);

        return $operationResult;
    }

    protected static function extractMetaTagContent($metaTags, $metaTagName)
    {
        $metaTag = $metaTags->first(function ($meta) use ($metaTagName) {
            $name = $meta->getAttribute('name');
            return $name && strtolower($name) === $metaTagName;
        });

        $metaTagContent = $metaTag ? $metaTag->getAttribute('content') : null;

        return $metaTagContent;
    }

    protected static function extractH1TagContent($document)
    {
        $h1 = collect($document->find('h1'))->first();
        $h1TagContent = $h1->innerHtml() ?? null;

        return $h1TagContent;
    }
}
