<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends AbstractController
{
    private RedisService $redisService;
    public function __construct(RedisService $redisService)
    {
        $this->redisService = $redisService;
    }


    public function index()
    {
        $redisKeys = $this->redisService->get('auth_permission_1');

        $redisKeys = $this->redisService->set('deneme', $redisKeys, 10);

        $redisKeys = $this->redisService->get('auth_permission_1');

        foreach (json_decode($redisKeys, true) as $item) {
            print_r($item);
            echo "<br>";
        }

        $this->redisService->flushAll();

        return new Response();
    }

    public function loginUser()
    {
        
    }
}