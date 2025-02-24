<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\ResponseService;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[Route('/api', name: 'api_')]
final class AuthController extends AbstractController
{
    public function __construct(
        private ResponseService $responseService,
        private TokenStorageInterface $tokenStorage,
        private JWTTokenManagerInterface $jwtManager
    ) {}

    #[Route('/register', name: 'register', methods: 'post')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator
    ): JsonResponse {
        try {
            $content = $request->getContent();
            if (empty($content)) {
                return $this->responseService->createResponse(false, 'El cuerpo de la solicitud está vacío', Response::HTTP_BAD_REQUEST);
            }

            $data = json_decode($content, true);

            // Input data validation
            $constraint = new Assert\Collection([
                'email' => [new Assert\NotBlank(), new Assert\Email(), new Assert\Length(['max' => 180])],
                'password' => [new Assert\NotBlank(), new Assert\Length(['min' => 6])],
            ]);

            $violations = $validator->validate($data, $constraint);
            if (count($violations) > 0) {
                $errors = [];
                foreach ($violations as $violation) {
                    $errors[] = $violation->getMessage();
                }
                return $this->responseService->createResponse(false, 'Errores de validación', Response::HTTP_BAD_REQUEST, $errors);
            }

            // Check if the user already exists
            $existingUser = $entityManager
                ->getRepository(User::class)
                ->findOneBy(['email' => $data['email']]);
            if ($existingUser) {
                return $this->responseService->createResponse(false, 'El correo electrónico ya está en uso', Response::HTTP_CONFLICT);
            }

            $user = new User();
            $user->setEmail($data['email']);
            $hashedPassword = $passwordHasher->hashPassword($user, $data['password']);
            $user->setPassword($hashedPassword);

            $entityManager->persist($user);
            $entityManager->flush();

            $token = $this->jwtManager->create($user);

            return $this->responseService->createResponse(true, 'Usuario registrado exitosamente', Response::HTTP_CREATED, ['token' => $token]);
        } catch (\Exception $e) {
            return $this->responseService->createResponse(false, 'Error: ' . $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/me', name: 'me', methods: 'get')]
    public function me()
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->responseService->createResponse(false, 'Usuario no encontrado', Response::HTTP_BAD_REQUEST);
        }

        return $this->responseService->createResponse(true, 'Datos de usuario obtenidos', Response::HTTP_OK, [
            'email' => $user->getUserIdentifier(),
        ]);
    }

    #[Route('/logout', name: 'logout', methods: 'post')]
    public function logout()
    {
        $this->tokenStorage->setToken(null);
        return $this->responseService->createResponse(true, 'Se ha cerrado la sesión correctamente', Response::HTTP_OK);
    }
}
