<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\HttpRequestMockSupport;
use Tests\Support\FixturesSupport;
use App\Models\Domain;
use Tests\TestCase;

class DomainCheckControllerTest extends TestCase
{
    use RefreshDatabase;
    use FixturesSupport;
    use HttpRequestMockSupport;

    public function testStore()
    {
        $domainName = 'google.com';
        $domain = static::$factory->create(Domain::class, ['name' => $domainName]);
        $route = route('domains.checks.store', $domain);

        $this->mockHttpRequest();
        $response = $this->post($route);
        $response->assertSessionHasNoErrors();

        $redirectionRoute = route('domains.show', $domain);
        $response->assertRedirect($redirectionRoute);

        $expectedAttributes = $this
            ->getExpectedDomainCheckAttributesFor($domainName)
            ->merge(['domain_id' => $domain->id])
            ->toArray();

        $this->assertDatabaseHas('domain_checks', $expectedAttributes);
    }

    public function testStoreDoesNotCreateRecordForNonExistentDomain()
    {
        $domain = static::$factory->create(Domain::class, ['name' => 'nonexistent.com']);
        $route = route('domains.checks.store', $domain);

        $this->mockHttpRequest();
        $response = $this->post($route);

        $redirectionRoute = route('domains.show', $domain);
        $response->assertRedirect($redirectionRoute);

        $this->assertDatabaseMissing('domain_checks', [
            'domain_id' => $domain->id,
        ]);
    }

    public function testStoreCreatesRecordOnClientError()
    {
        $domainName = 'too-many-requests.com';
        $domain = static::$factory->create(Domain::class, ['name' => $domainName]);
        $route = route('domains.checks.store', $domain);

        $this->mockHttpRequest();
        $response = $this->withoutExceptionHandling()->post($route);

        $redirectionRoute = route('domains.show', $domain);
        $response->assertRedirect($redirectionRoute);

        $expectedAttributes = $this
            ->getExpectedDomainCheckAttributesFor($domainName)
            ->only('status_code')
            ->merge(['domain_id' => $domain->id])
            ->toArray();

        $this->assertDatabaseHas('domain_checks', $expectedAttributes);
    }
}
