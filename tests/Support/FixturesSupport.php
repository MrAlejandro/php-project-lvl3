<?php

namespace Tests\Support;

trait FixturesSupport
{
    public function getExpectedDomainCheckAttributesFor(string $domainName)
    {
        $fileName = $domainName . '.php';
        $filePath = join(DIRECTORY_SEPARATOR, [__DIR__, '..', 'fixtures', 'HttpResponses', $fileName]);
        $attributes = [];

        if (file_exists($filePath)) {
            [,,, $attributes] = require($filePath);
        }

        return $attributes;
    }
}
