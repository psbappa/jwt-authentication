<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestController
{
    #[Route('/api/test', name: 'test')]
    public function index(): Response
    {
        return new Response('We are here');
    }
}