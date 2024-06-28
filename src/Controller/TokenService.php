<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\RefreshToken;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenManagerInterface;

class TokenService
{
    private JWTTokenManagerInterface $JWTTokenManager;
    private TokenStorageInterface $tokenStorage;
    private RefreshTokenManagerInterface $refreshTokenManager;

    public function __construct(JWTTokenManagerInterface $JWTTokenManager, TokenStorageInterface $tokenStorage, RefreshTokenManagerInterface $refreshTokenManager)
    {
        $this->JWTTokenManager = $JWTTokenManager;
        $this->refreshTokenManager = $refreshTokenManager;
    }

    public function createToken(?User $user)
    {
            $user->setIdentification("11122233344");
            $user->setName("Kadir");
            $user->setSurname("YILDIRIM");

            $token = $this->JWTTokenManager->create($user);
            
            $refreshToken = bin2hex(random_bytes(64));

            $today = Date('Y-m-d H:i:s');
            $today = Date('Y-m-d H:i:s',strtotime("+1 months", strtotime($today)));
            $date = new \DateTime($today);



            return [
                'user' => $user,
                'token' => $token,
                'refresh_token' => $refreshToken,
                'has_subscription' => null
            ];
    }

    public function createRefreshToken(User $user)
    {
        $refreshToken = bin2hex(random_bytes(64));

        $today = Date('Y-m-d H:i:s');
        $today = Date('Y-m-d H:i:s',strtotime($_ENV['REFRESH_TOKEN_TTL'] + " hours", strtotime($today)));
        $date = new \DateTime($today);

        $removeRefreshToken = $this->refreshTokenManager->getLastFromUsername($user->getUserIdentifier());

        if($removeRefreshToken !== null)
            $this->refreshTokenManager->delete($removeRefreshToken);

        $refreshTokenModel = new RefreshToken();
        $refreshTokenModel->setRefreshToken($refreshToken);
        $refreshTokenModel->setUsername($user->getUserIdentifier());
        $refreshTokenModel->setValid($date);
        $this->refreshTokenManager->save($refreshTokenModel);
    }
}