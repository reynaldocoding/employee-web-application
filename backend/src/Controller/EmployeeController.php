<?php

namespace App\Controller;

use App\Entity\Employee;
use App\Repository\EmployeeRepository;
use App\Service\PositionService;
use App\Service\ResponseService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api', name: 'api_')]
final class EmployeeController extends AbstractController
{
    public function __construct(
        private ResponseService $responseService,
        private EntityManagerInterface $entityManager,
        private EmployeeRepository $employeeRepository,
        private DenormalizerInterface&NormalizerInterface $serializer,
        private ValidatorInterface $validator,
        private PositionService $positionService
    ) {}

    #[Route('/employees', name: 'employee_list', methods: 'get')]
    public function list(Request $request): JsonResponse
    {
        try {
            $name = $request->query->get('name');
            if ($name) {
                $employees = $this->employeeRepository->findByName($name);
            } else {
                $employees = $this->employeeRepository->findAll();
            }

            return $this->responseService->createResponse(
                true,
                $name ? "Empleados filtrados con Nombre '$name'" : 'Se han obtenido todos los Empleados',
                Response::HTTP_OK,
                $this->serializer->normalize($employees) // Normalize the data
            );
        } catch (\Exception $e) {
            return $this->responseService->createResponse(
                false,
                'Error: ' . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    #[Route('/employees', name: 'employee_create', methods: 'post')]
    public function create(Request $request): JsonResponse
    {
        try {
            $employee = new Employee();
            $this->updateEmployeeFromRequest($employee, $request);

            $errors = $this->validator->validate($employee);
            if (count($errors) > 0) {
                $errorsArray = [];
                foreach ($errors as $error) {
                    $errorsArray[] = $error->getMessage();
                }
                return $this->responseService->createResponse(
                    false,
                    'Errores de validación',
                    Response::HTTP_BAD_REQUEST,
                    $errorsArray
                );
            }

            $this->entityManager->persist($employee);
            $this->entityManager->flush();

            return $this->responseService->createResponse(
                true,
                'Empleado creado',
                Response::HTTP_CREATED,
                ['id' => $employee->getId()]
            );
        } catch (\Exception $e) {
            return $this->responseService->createResponse(
                false,
                'Error: ' . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    #[Route('/employees/{id}', name: 'employee_update', methods: 'put')]
    public function update(int $id, Request $request): JsonResponse
    {
        try {
            $employee = $this->entityManager->getRepository(Employee::class)
                ->findById($id);
            if (!$employee) {
                return $this->responseService->createResponse(
                    false,
                    'Empleado no encontrado',
                    Response::HTTP_NOT_FOUND
                );
            }

            $this->updateEmployeeFromRequest($employee, $request);

            $errors = $this->validator->validate($employee);
            if (count($errors) > 0) {
                $errorsArray = [];
                foreach ($errors as $error) {
                    $errorsArray[] = $error->getMessage();
                }
                return $this->responseService->createResponse(
                    false,
                    'Errores de validación',
                    Response::HTTP_BAD_REQUEST,
                    $errorsArray
                );
            }

            $this->entityManager->flush();

            return $this->responseService->createResponse(
                true,
                'Empleado actualizado',
                Response::HTTP_OK,
                ['id' => $employee->getId()]
            );
        } catch (\Exception $e) {
            return $this->responseService->createResponse(
                false,
                'Error: ' . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    #[Route('/employees/{id}', name: 'employee_delete', methods: 'delete')]
    public function delete(int $id): JsonResponse
    {
        try {
            $employee = $this->entityManager->getRepository(Employee::class)
                ->findById($id);
            if (!$employee) {
                return $this->responseService->createResponse(
                    false,
                    'Empleado no encontrado',
                    Response::HTTP_NOT_FOUND
                );
            }

            $this->entityManager->remove($employee);
            $this->entityManager->flush();

            return $this->responseService->createResponse(
                true,
                'Empleado dado de baja',
                Response::HTTP_OK,
                ['id' => $id]
            );
        } catch (\Exception $e) {
            return $this->responseService->createResponse(
                false,
                'Error: ' . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    #[Route('/employees/{id}', name: 'employee_get', methods: 'get')]
    public function getEmployeeById(int $id): JsonResponse
    {
        try {
            $employee = $this->entityManager->getRepository(Employee::class)
                ->findById($id);
            if (!$employee) {
                return $this->responseService->createResponse(
                    false,
                    'Empleado no encontrado',
                    Response::HTTP_NOT_FOUND
                );
            }

            return $this->responseService->createResponse(
                true,
                'Datos del Empleado',
                Response::HTTP_OK,
                $this->serializer->normalize($employee) // Normalize the data
            );
        } catch (\Exception $e) {
            return $this->responseService->createResponse(
                false,
                'Error: ' . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    private function updateEmployeeFromRequest(Employee $employee, Request $request)
    {
        $data = json_decode($request->getContent(), true);
        if (isset($data['name'])) {
            $employee->setName($data['name']);
        }
        if (isset($data['lastNames'])) {
            $employee->setLastNames($data['lastNames']);
        }
        if (isset($data['position'])) {
            $employee->setPosition($data['position']);
        }
        if (isset($data['birthdate'])) {
            $employee->setBirthdate(new \DateTime($data['birthdate']));
        }
        $employee->setUpdatedAt(new \DateTimeImmutable());
    }

    #[Route('/positions', name: 'positions_list', methods: 'get')]
    public function getPositions()
    {
        return $this->positionService->getPositions();
    }
}
