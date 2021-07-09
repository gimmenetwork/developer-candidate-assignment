<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;

class AuthService
{
    public function __construct(
        private RequestStack $requestStack
    )
    {
    }

    public function login(){
        $session = $this->requestStack->getSession();
        $session->set('isLogin', 1);
    }

    public function logout(){
        $session = $this->requestStack->getSession();
        $session->clear();
    }

}
