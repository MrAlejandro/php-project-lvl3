<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Repositories\DomainCheck;
use App\Repositories\Domain;
use Tests\TestCase;

class DomainTest extends TestCase
{
    use RefreshDatabase;

    public function testIndex()
    {
        Domain::create('yandex.ru');
        $domainId = Domain::create('google.com');
        DomainCheck::create($domainId);
        $route = route('domains.index');

        $response = $this->withoutExceptionHandling()->get($route);
        $response->assertStatus(200);
    }

    public function testShow()
    {
        $domainId = Domain::create('google.com');
        DomainCheck::create($domainId);
        $route = route('domains.show', ['domain' => $domainId]);

        $response = $this->get($route);
        $response->assertStatus(200);
    }

    public function testStore()
    {
        $route = route('domains.store');
        $url = 'http://google.com';
        $params = ['domain' => ['url' => $url]];

        $response = $this->post($route, $params);
        $response->assertStatus(302);

        $domainName = 'google.com';
        $this->assertDatabaseHas('domains', [
            'name' => $domainName,
        ]);
    }

    public function testCheck()
    {
        $domainName = 'google.com';
        $domainId = Domain::create($domainName);
        $route = route('domains.check', ['id' => $domainId]);
        $response = $this->withoutExceptionHandling()->post($route);

        $redirectionRoute = route('domains.show', ['domain' => $domainId]);
        $response->assertRedirect($redirectionRoute);

        $this->assertDatabaseHas('domain_checks', [
            'domain_id' => $domainId,
        ]);
    }

    public function testStoreRedirectsToExistingDomainIfNameNotUnique()
    {
        $domainName = 'google.com';
        $domainId = Domain::create($domainName);

        $url = 'http://google.com';
        $params = ['domain' => ['url' => $url]];

        $route = route('domains.store');
        $response = $this->post($route, $params);

        $redirectionRoute = route('domains.show', ['domain' => $domainId]);
        $response->assertRedirect($redirectionRoute);
    }

    public function testStoreDoesNotCreateRecordWithEmptyName()
    {
        $route = route('domains.store');
        $params = ['domain' => ['url' => '']];

        $response = $this->post($route, $params);
        $response->assertStatus(302);

        $this->assertDatabaseMissing('domains', [
            'name' => '',
        ]);
    }

    public function testStoreDoesNotCreateRecordWithInvalidName()
    {
        $route = route('domains.store');
        $params = ['domain' => ['url' => '']];

        $response = $this->post($route, $params);
        $response->assertStatus(302);

        $this->assertDatabaseMissing('domains', [
            'name' => '',
        ]);
    }
}
