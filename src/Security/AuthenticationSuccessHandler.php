<?php
// src/Security/AuthenticationSuccessHandler.php
// src/Security/AuthenticationSuccessHandler.php
namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\RedirectResponse; // Import thêm này

class AuthenticationSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): Response
    {
        $user = $token->getUser();

        // Kiểm tra nếu user là một đối tượng User
        if ($user instanceof \App\Entity\User) {
            // Kiểm tra điều kiện trong entity User
            if ($user->isCustomer()) {
                // Nếu là khách hàng, chuyển hướng đến 'trang_chu_khach_hang'
                return new RedirectResponse($this->router->generate('trang_chu'));
            } else {
                // Nếu không phải là khách hàng, chuyển hướng đến 'trang_chu'
                return new RedirectResponse($this->router->generate('trang_chu_khach_hang'));
            }
        }

        // Nếu không phải đối tượng User, mặc định chuyển hướng đến 'trang_chu'
        return new RedirectResponse($this->router->generate('trang_chu_khach_hang'));
    }
}
