<?php

namespace Tests\Support;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;

use function GuzzleHttp\Promise\rejection_for;
use function GuzzleHttp\Promise\promise_for;

class MockHandler
{
    public function __invoke(RequestInterface $request, array $options = []): PromiseInterface
    {
        $requestPath = $request->getUri()->getPath();
        $responseFileName = $requestPath . '.php';
        $responseFilePath = join(DIRECTORY_SEPARATOR, [__DIR__, '..', 'Fixtures', 'HttpResponses', $responseFileName]);

        if (file_exists($responseFilePath)) {
            list($statusCode, $headers, $body) = require($responseFilePath);
            $response = new Response($statusCode, $headers, $body);

            $promise = $this->isSuccessfulStatusCode($statusCode)
                ? promise_for($response)
                : rejection_for($response);

            return $promise->then(
                function (ResponseInterface $response) {
                    return $response;
                },
                function (ResponseInterface $response) use ($request) {
                    $error = new ClientException('Client error', $request, $response);
                    return rejection_for($error);
                }
            );
        }

        $error = new ConnectException('Connect error', $request);
        return rejection_for($error);
    }

    private function isSuccessfulStatusCode(int $code)
    {
        return $code >= 200 && $code < 300;
    }
}
