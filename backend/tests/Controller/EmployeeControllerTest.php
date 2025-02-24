<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class EmployeeControllerTest extends WebTestCase
{
    private $tokenJWT;
    private $employeeId;
    private $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();
        $this->initTokenJWTEmployee();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * Initialize the JWT token and employee with initial value
     */
    private function initTokenJWTEmployee()
    {
        if (is_null($this->tokenJWT) || is_null($this->employeeId)) {
            // API: Register
            $this->client->request('POST', '/api/register', [], [], [], json_encode([
                'email' => 'test@example.com',
                'password' => 'password123',
            ]));

            $response = $this->client->getResponse();
            $content = json_decode($response->getContent(), true);

            $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
            $this->assertTrue($content['success']);

            $this->tokenJWT = $content['data']['token'];

            // API: Employees
            $this->client->request('POST', '/api/employees', [], [], [
                'HTTP_Authorization' => 'Bearer ' . $this->tokenJWT,
            ], json_encode([
                'name' => 'Nombre Empleado',
                'lastNames' => 'Apellido Empleado',
                'position' => 'Developer',
                'birthdate' => '2000-01-01',
            ]));

            $response = $this->client->getResponse();
            $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());

            $content = json_decode($response->getContent(), true);
            $this->assertTrue($content['success']);

            $this->employeeId = $content['data']['id'];
        }
    }

    public function testListEmployees()
    {
        $this->client->request('GET', '/api/employees', [], [], [
            'HTTP_Authorization' => 'Bearer ' . $this->tokenJWT,
        ]);

        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);
        $this->assertTrue($content['success']);
        $this->assertEquals('Se han obtenido todos los Empleados', $content['message']);
        $this->assertCount(1, $content['data']);
    }

    public function testListEmployeesFilteredByName()
    {
        $this->client->request('GET', '/api/employees', ['name' => 'Nombre Empleado',], [], [
            'HTTP_Authorization' => 'Bearer ' . $this->tokenJWT,
        ]);

        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);
        $this->assertTrue($content['success']);
        $this->assertEquals('Empleados filtrados con Nombre \'Nombre Empleado\'', $content['message']);
        $this->assertCount(1, $content['data']);
        $this->assertEquals('Nombre Empleado', $content['data'][0]['name']);
    }

    public function testCreateEmployee(): int
    {
        $this->client->request('POST', '/api/employees', [], [], [
            'HTTP_Authorization' => 'Bearer ' . $this->tokenJWT,
        ], json_encode([
            'name' => 'Jane',
            'lastNames' => 'Doe',
            'position' => 'Manager',
            'birthdate' => '1985-05-05',
        ]));

        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);
        $this->assertTrue($content['success']);
        $this->assertEquals('Empleado creado', $content['message']);
        $this->assertArrayHasKey('id', $content['data']);

        return $content['data']['id'];
    }

    public function testUpdateEmployee()
    {
        $this->client->request('PUT', '/api/employees/' . $this->employeeId, [], [], [
            'HTTP_Authorization' => 'Bearer ' . $this->tokenJWT,
        ], json_encode([
            'name' => 'Nombre Actualizado',
            'lastNames' => 'Apellido Empleado',
            'position' => 'Senior Developer',
            'birthdate' => '2000-01-01',
        ]));

        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);
        $this->assertTrue($content['success']);
        $this->assertEquals('Empleado actualizado', $content['message']);
        $this->assertArrayHasKey('id', $content['data']);
    }

    public function testDeleteEmployee()
    {
        $this->client->request('DELETE', '/api/employees/' . $this->employeeId, [], [], [
            'HTTP_Authorization' => 'Bearer ' . $this->tokenJWT,
        ]);

        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);
        $this->assertTrue($content['success']);
        $this->assertEquals('Empleado dado de baja', $content['message']);
        $this->assertArrayHasKey('id', $content['data']);
    }

    public function testGetEmployeeById()
    {
        $this->client->request('GET', '/api/employees/' . $this->employeeId, [], [], [
            'HTTP_Authorization' => 'Bearer ' . $this->tokenJWT,
        ]);

        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);
        $this->assertTrue($content['success']);
        $this->assertEquals('Datos del Empleado', $content['message']);
        $this->assertArrayHasKey('id', $content['data']);
    }

    public function testGetPositions()
    {
        $this->client->request('GET', '/api/positions', [], [], [
            'HTTP_Authorization' => 'Bearer ' . $this->tokenJWT,
        ]);

        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);
        $this->assertTrue($content['success']);
        $this->assertNotEmpty($content['data']);
    }
}
