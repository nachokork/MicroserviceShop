<?php

namespace App\Controller;

use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

use Symfony\Component\Routing\Attribute\Route;

use Symfony\Component\Security\Core\Exception\AuthenticationException;

use Symfony\Component\Security\Core\User\UserProviderInterface;

class TokenController extends AbstractController

{

    public function __construct(
        protected JWTTokenManagerInterface $jwtManager,
        protected UserProviderInterface $userProvider,
        protected UserPasswordHasherInterface $passwordHasher
    ){}

    #[Route('/api/login', name: 'api_login_check', methods: ['POST'])]
    public function login(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        $username = $data['username'] ?? null;
        $password = $data['password'] ?? null;
        if (null === $username || null === $password) {
            return $this->json(['message' => 'Invalid credentials'], Response::HTTP_UNAUTHORIZED);
        }
        try {
            $user = $this->userProvider->loadUserByIdentifier($username);
            if (!$this->passwordHasher->isPasswordValid($user, $password)) {
                throw new AuthenticationException('Invalid credentials');
            }

            $token = $this->jwtManager->create($user);
            return $this->json(['token' => $token]);
        } catch (\Exception $e) {
            return $this->json(['message' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }

    #[Route('/token/refresh', name: 'jwt_refresh', methods: ['POST'])]

    public function refreshToken(Request $request): Response

    {

        $authHeader = $request->headers->get('Authorization');

        if ($authHeader === null || !str_starts_with($authHeader, 'Bearer ')) {

            return $this->json(['message' => 'Invalid token'], Response::HTTP_UNAUTHORIZED);

        }

        $token = substr($authHeader, 7); // Quitamos "Bearer " del Request

        try {

            //Validar token

            $token = $this->jwtManager->parse($token);

            $user = $this->userProvider->loadUserByIdentifier($token['username']);

            $newToken = $this->jwtManager->create($user);

            return $this->json(['token' => $newToken]);

        } catch (\Exception $e) {

            return $this->json(['message' => 'Token refresh failed'], Response::HTTP_UNAUTHORIZED);

        }

    }

}