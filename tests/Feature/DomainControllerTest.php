<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\DomainCheck;
use App\Models\Domain;
use Tests\TestCase;

class DomainControllerTest extends TestCase
{
    use RefreshDatabase;

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
        $route = route('domains.show', $domain);

        $response = $this->withoutExceptionHandling()->get($route);
        $response->assertStatus(200);
    }

    public function testStore()
    {
        $route = route('domains.store');
        $params = ['page_url' => 'http://google.com/some-extra-path'];

        $response = $this->post($route, $params);
        $response->assertSessionHasNoErrors();
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

        $redirectionRoute = route('domains.show', $domain);
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
}
