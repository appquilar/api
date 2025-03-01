<?php

declare(strict_types=1);

namespace App\Tests\Integration\User;

use App\Tests\Integration\IntegrationTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Uuid;

class RegisterApiTest extends IntegrationTestCase
{
    public function testRegisterUserSuccess(): void
    {
        $response = $this->request('POST', '/api/auth/register', [
            'user_id' => Uuid::v4()->toString(),
            'email' => 'testuser@example.com',
            'password' => 'SecurePass123'
        ]);
        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
    }

    public function testRegisterUserValidationFails(): void
    {
        $response = $this->request('POST', '/api/auth/register', [
            'user_id' => 'invalid-uuid',
            'email' => '',
            'password' => ''
        ]);

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

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertEquals(json_encode($expectedErrorMessage), $response->getContent());
    }

    public function testRegisteredUserAlreadyExistentEmail(): void
    {
        $this->givenAnUserWithEmail('testuser@example.com');

        $response = $this->request('POST', '/api/auth/register', [
            'user_id' => Uuid::v4()->toString(),
            'email' => 'testuser@example.com',
            'password' => 'SecurePass123'
        ]);

        $expectedErrorMessage = [
            'success' => false,
            'error' => [
                'errors' => [
                    'email' => [
                        'auth.register.email.unique'
                    ]
                ]
            ]
        ];

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertEquals(json_encode($expectedErrorMessage), $response->getContent());
    }
}
