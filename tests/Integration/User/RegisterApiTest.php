<?php

declare(strict_types=1);

namespace App\Tests\Integration\User;

use App\Tests\Integration\IntegrationTestCase;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Response;

class RegisterApiTest extends IntegrationTestCase
{
    public function testRegisterUserSuccess(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/auth/register', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'user_id' => Uuid::uuid4()->toString(),
            'email' => 'testuser@example.com',
            'password' => 'SecurePass123'
        ]));

        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
    }

    public function testRegisterUserValidationFails(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/auth/register', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'user_id' => 'invalid-uuid',
            'email' => '',
            'password' => ''
        ]));

        $expectedErrorMessage = [
            'success' => false,
            'error' => [
                'errors' => [
                    'userId' => [
                        'auth.register.user_id.not_blank'
                    ],
                    'email' => [
                        'auth.register.email.not_blank'
                    ],
                    'password' => [
                        'auth.register.password.not_blank',
                        'auth.register.password.length.min'
                    ]
                ]
            ]
        ];

        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertEquals(json_encode($expectedErrorMessage), $response->getContent());
    }
}
