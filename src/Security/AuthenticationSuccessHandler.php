<?php
// src/Security/AuthenticationSuccessHandler.php
// src/Security/AuthenticationSuccessHandler.php
namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

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
        if ($user instanceof \App\Entity\Customer) {
            $customerId = $user->getCustomerID(); // Sử dụng getter của CustomerID
            $request->getSession()->set('customer_id', $customerId);
            if ($user->isCustomer()) {
                return new RedirectResponse($this->router->generate('trang_chu_khach_hang'));
            } else {
                // Nếu không phải là khách hàng, chuyển hướng đến 'dang_nhap'
                return new RedirectResponse($this->router->generate('dang_nhap'));
            }
        }

        // Lưu thông tin khách hàng vào session
        // Chuyển hướng đến 'trang_chu_khach_hang'
        return new RedirectResponse($this->router->generate('trang_chu'));
    }
}
