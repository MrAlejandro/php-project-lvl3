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
        static::$factory->create(Domain::class, ['name' => 'yandex.ru']);
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
        $url = 'http://google.com/some-extra-data';
        $params = ['page_url' => $url];

        $response = $this->post($route, $params);
        $response->assertStatus(302);

        $this->assertDatabaseHas('domains', [
            'name' => 'google.com',
        ]);
    }

    public function testStoreRedirectsToExistingDomainIfNameNotUnique()
    {
        $domainName = 'google.com';
        $domain = static::$factory->create(Domain::class, ['name' => $domainName]);

        $url = 'http://google.com';
        $params = ['page_url' => $url];

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
        $route = route('domains.store');
        $invalidUrl = 'invalid-url';
        $params = ['page_url' => $invalidUrl];

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

        $this->assertDatabaseHas('domain_checks', [
            'domain_id' => $domain->id,
            'status_code' => 200,
            'h1' => 'Thank You For Helping Us!',
            'keywords' => 'HTML,CSS,JavaScript,SQL,PHP,jQuery,XML,DOM,Bootstrap,Python,Java,Web development,W3C,tutorials,programming,training,learning,quiz,primer,lessons,references,examples,exercises,source code,colors,demos,tips',
            'description' => 'Well organized and easy to understand Web building tutorials with lots of examples of how to use HTML, CSS, JavaScript, SQL, PHP, Python, Bootstrap, Java and XML.',
        ]);
    }

    public function testCheckDoesNotCreateRecordForNonExistentDomain()
    {
        $domainName = 'nonexistent.com';
        $domain = static::$factory->create(Domain::class, ['name' => $domainName]);
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

        $expectedStatusCode = 429;
        $this->assertDatabaseHas('domain_checks', [
            'domain_id' => $domain->id,
            'status_code' => $expectedStatusCode,
        ]);
    }
}
