<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\JsonResponse;

class ResponseService
{
    /**
     * Creates a JSON response with a unified structure.
     *
     * @param bool $success Indicates successful or unsuccessful operation.
     * @param string $message Message about the result of the operation.
     * @param Mixed $data Data to be sent in the response.
     * @param int $statusCode HTTP status code of the response.
     * @return JsonResponse JSON response
     */
    public function createResponse(bool $success, string $message, int $statusCode = 200, $data = null): JsonResponse
    {
        $response = [
            'success' => $success, // Indicates whether the operation was successful
            'message' => $message, // Descriptive message about the result of the operation
            'data' => $data, // Additional data (can be null if there is no additional data)
        ];

        // Returns a JSON response with the specified HTTP status code
        return new JsonResponse($response, $statusCode);
    }
}
