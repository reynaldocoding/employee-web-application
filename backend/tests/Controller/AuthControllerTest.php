<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class AuthControllerTest extends WebTestCase
{
    public function testSuccessfulRegistration()
    {
        $client = static::createClient();

        $client->request('POST', '/api/register', [], [], [], json_encode([
            'email' => 'usuario@email.com',
            'password' => '12345678',
        ]));

        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);
        $this->assertTrue($content['success']);
        $this->assertEquals('Usuario registrado exitosamente', $content['message']);
        $this->assertArrayHasKey('token', $content['data']);
    }

    public function testRegistrationWithMissingData()
    {
        $client = static::createClient();

        $client->request('POST', '/api/register', [], [], [], json_encode([
            'email' => '',
            'password' => '12345678',
        ]));

        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);
        $this->assertFalse($content['success']);
        $this->assertEquals('Errores de validación', $content['message']);
    }

    public function testRegistrationWithDuplicateEmail()
    {
        $client = static::createClient();

        // First, register a user
        $client->request('POST', '/api/register', [], [], [], json_encode([
            'email' => 'usuario@email.com',
            'password' => '12345678',
        ]));

        // Try to register the same user again
        $client->request('POST', '/api/register', [], [], [], json_encode([
            'email' => 'usuario@email.com',
            'password' => '12345678',
        ]));

        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_CONFLICT, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);
        $this->assertFalse($content['success']);
        $this->assertEquals('El correo electrónico ya está en uso', $content['message']);
    }

    public function testGetUserData()
    {
        $client = static::createClient();

        // API 1: Register a user first
        $client->request('POST', '/api/register', [], [], [], json_encode([
            'email' => 'usuario@email.com',
            'password' => '12345678',
        ]));

        $responseRegister = $client->getResponse();
        $this->assertEquals(Response::HTTP_CREATED, $responseRegister->getStatusCode());
        $contentRegister = json_decode($responseRegister->getContent(), true);
        $tokenJwt = $contentRegister['data']['token'];

        // API 2: Set the Authorization header with the Bearer token
        $client->request('GET', '/api/me', [], [], [
            'HTTP_Authorization' => 'Bearer ' . $tokenJwt,
        ]);

        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);
        $this->assertTrue($content['success']);
        $this->assertEquals('Datos de usuario obtenidos', $content['message']);
        $this->assertArrayHasKey('email', $content['data']);
    }
}
