<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\FixturesSupport;
use App\Models\DomainCheck;
use App\Models\Domain;
use Tests\TestCase;

class DomainControllerTest extends TestCase
{
    use RefreshDatabase;
    use FixturesSupport;

    public function testIndex()
    {
        $domain = static::$factory->create(Domain::class, ['name' => 'google.com']);
        static::$factory->create(DomainCheck::class, ['domainId' => $domain->id]);
        $route = route('domains.index');

        $response = $this->get($route);
        $response->assertStatus(200);
    }

    public function testShow()
    {
        $domain = static::$factory->create(Domain::class, ['name' => 'google.com']);
        static::$factory->create(DomainCheck::class, ['domainId' => $domain->id]);
        $route = route('domains.show', ['domain' => $domain->id]);

        $response = $this->get($route);
        $response->assertStatus(200);
    }

    public function testStore()
    {
        $route = route('domains.store');
        $params = ['page_url' => 'http://google.com/some-extra-path'];

        $response = $this->post($route, $params);
        $response->assertStatus(302);

        $this->assertDatabaseHas('domains', [
            'name' => 'google.com',
        ]);
    }

    public function testStoreRedirectsToExistingDomainIfNameNotUnique()
    {
        $domain = static::$factory->create(Domain::class, ['name' => 'google.com']);
        $params = ['page_url' => 'http://google.com'];
        $route = route('domains.store');

        $response = $this->post($route, $params);

        $redirectionRoute = route('domains.show', ['domain' => $domain->id]);
        $response->assertRedirect($redirectionRoute);
    }

    public function testStoreDoesNotCreateRecordWithEmptyUrl()
    {
        $route = route('domains.store');
        $params = ['page_url' => ''];

        $response = $this->post($route, $params);
        $response->assertStatus(302);

        $this->assertDatabaseMissing('domains', [
            'name' => '',
        ]);
    }

    public function testStoreDoesNotCreateRecordWithInvalidUrl()
    {
        $invalidUrl = 'invalid-url';
        $params = ['page_url' => $invalidUrl];
        $route = route('domains.store');

        $response = $this->post($route, $params);
        $response->assertStatus(302);

        $this->assertDatabaseMissing('domains', [
            'name' => $invalidUrl,
        ]);
    }

    public function testCheck()
    {
        $domainName = 'google.com';
        $domain = static::$factory->create(Domain::class, ['name' => $domainName]);
        $route = route('domains.check', ['id' => $domain->id]);

        $response = $this->post($route);

        $redirectionRoute = route('domains.show', ['domain' => $domain->id]);
        $response->assertRedirect($redirectionRoute);

        $expectedDomainAttributes = $this->getExpectedDomainCheckAttributesFor($domainName);

        $this->assertDatabaseHas('domain_checks', [
            'domain_id' => $domain->id,
            'status_code' => $expectedDomainAttributes['statusCode'],
            'h1' => $expectedDomainAttributes['h1'],
            'keywords' => $expectedDomainAttributes['keywords'],
            'description' => $expectedDomainAttributes['description'],
        ]);
    }

    public function testCheckDoesNotCreateRecordForNonExistentDomain()
    {
        $domain = static::$factory->create(Domain::class, ['name' => 'nonexistent.com']);
        $route = route('domains.check', ['id' => $domain->id]);

        $response = $this->post($route);

        $redirectionRoute = route('domains.show', ['domain' => $domain->id]);
        $response->assertRedirect($redirectionRoute);

        $this->assertDatabaseMissing('domain_checks', [
            'domain_id' => $domain->id,
        ]);
    }

    public function testCheckCreatesRecordOnClientError()
    {
        $domainName = 'too-many-requests.com';
        $domain = static::$factory->create(Domain::class, ['name' => $domainName]);
        $route = route('domains.check', ['id' => $domain->id]);
        $response = $this->withoutExceptionHandling()->post($route);

        $redirectionRoute = route('domains.show', ['domain' => $domain->id]);
        $response->assertRedirect($redirectionRoute);

        $expectedDomainAttributes = $this->getExpectedDomainCheckAttributesFor($domainName);

        $this->assertDatabaseHas('domain_checks', [
            'domain_id' => $domain->id,
            'status_code' => $expectedDomainAttributes['statusCode'],
        ]);
    }
}
