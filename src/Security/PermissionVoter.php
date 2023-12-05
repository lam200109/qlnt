<?php

// src/Security/PermissionVoter.php

namespace App\Security;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class PermissionVoter extends Voter implements VoterInterface
{
    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, ['BanThuocAccess', 'QuanLyKhachHangAccess', 'BaoCaoTaiChinhAccess', 'NhapHangAccess', 'AdminAccess']);
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        switch ($attribute) {
            case 'BanThuocAccess':
                return $this->checkBanThuocAccess($token->getUser());
                break;
            case 'QuanLyKhachHangAccess':
                return $this->checkQuanLyKhachHangAccess($token->getUser());
                break;
            case 'BaoCaoTaiChinhAccess':
                return $this->checkBaoCaoTaiChinhAccess($token->getUser());
                break;
            case 'NhapHangAccess':
                return $this->checkNhapHangAccess($token->getUser());
                break;
            case 'AdminAccess':
                return $this->checkAdminAccess($token->getUser());
                break;
        }

        return false;
    }

    private function checkBanThuocAccess($user): bool
    {
        return $user->hasPermission('BanThuocAccess');
    }

    private function checkQuanLyKhachHangAccess($user): bool
    {
        return $user->hasPermission('QuanLyKhachHangAccess');
    }

    private function checkBaoCaoTaiChinhAccess($user): bool
    {
        return $user->hasPermission('BaoCaoTaiChinhAccess');
    }

    private function checkNhapHangAccess($user): bool
    {
        return $user->hasPermission('NhapHangAccess');
    }
    private function checkAdminAccess($user): bool
{
    return $user->hasPermission('AdminAccess');
}

}













