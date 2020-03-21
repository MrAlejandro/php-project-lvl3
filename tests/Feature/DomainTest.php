<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Repositories\Domain;
use Tests\TestCase;

class DomainTest extends TestCase
{
    use RefreshDatabase;

    public function testIndex()
    {
        $route = route('domains.index');

        $response = $this->get($route);
        $response->assertStatus(200);
    }

    public function testShow()
    {
        $domainId = Domain::create('google.com');
        $route = route('domains.show', ['domain' => $domainId]);

        $response = $this->get($route);
        $response->assertStatus(200);
    }

    public function testStore()
    {
        $route = route('domains.store');
        $url = 'http://google.com';
        $params = ['domain' => ['url' => $url]];

        $response = $this->withoutExceptionHandling()->post($route, $params);
        $response->assertStatus(302);

        $domainName = 'google.com';
        $this->assertDatabaseHas('domains', [
            'name' => $domainName,
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
}
