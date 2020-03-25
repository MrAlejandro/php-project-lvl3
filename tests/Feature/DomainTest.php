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

        $response = $this->get($route);
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
            'status_code' => 200,
            'h1' => 'Thank You For Helping Us!',
            'keywords' => 'HTML,CSS,JavaScript,SQL,PHP,jQuery,XML,DOM,Bootstrap,Python,Java,Web development,W3C,tutorials,programming,training,learning,quiz,primer,lessons,references,examples,exercises,source code,colors,demos,tips',
            'description' => 'Well organized and easy to understand Web building tutorials with lots of examples of how to use HTML, CSS, JavaScript, SQL, PHP, Python, Bootstrap, Java and XML.',
        ]);
    }

    public function testCheckDoesNotCreateRecordForNonExistentDomain()
    {
        $domainName = 'nonexistent.com';
        $domainId = Domain::create($domainName);
        $route = route('domains.check', ['id' => $domainId]);
        $response = $this->post($route);

        $redirectionRoute = route('domains.show', ['domain' => $domainId]);
        $response->assertRedirect($redirectionRoute);

        $this->assertDatabaseMissing('domain_checks', [
            'domain_id' => $domainId,
        ]);
    }

    public function testCheckCreatesRecordOnClientError()
    {
        $domainName = 'too-many-requests.com';
        $domainId = Domain::create($domainName);
        $route = route('domains.check', ['id' => $domainId]);
        $response = $this->post($route);

        $redirectionRoute = route('domains.show', ['domain' => $domainId]);
        $response->assertRedirect($redirectionRoute);

        $expectedStatusCode = 429;
        $this->assertDatabaseHas('domain_checks', [
            'domain_id' => $domainId,
            'status_code' => $expectedStatusCode,
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
