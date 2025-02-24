<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Service to handle retrieving job positions from an external API.
 */
class PositionService
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private string $apiPositionsUrl,
        private ResponseService $responseService
    ) {}

    /**
     * Gets the list of positions from the external API.
     *
     * @return array List of job positions obtained.
     */
    public function getPositions(): JsonResponse
    {
        try {
            // Make a GET request to the external API to get the positions
            $response = $this->httpClient->request('GET', $this->apiPositionsUrl);

            if (Response::HTTP_OK !== $response->getStatusCode()) {
                throw new \Exception('El código de estado de respuesta es diferente al esperado.');
            }

            return $this->responseService->createResponse(
                true,
                'Puestos de Trabajo obtenidos',
                Response::HTTP_OK,
                $response->toArray()
            );
        } catch (TransportExceptionInterface $e) {
            return $this->responseService->createResponse(
                false,
                'Error: ' . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        } catch (\Exception $e) {
            return $this->responseService->createResponse(
                false,
                'Error: ' . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
