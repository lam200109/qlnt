<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\DBAL\Connection;

class DanhsachnhasanxuatController extends AbstractController
{
    #[Route('/danhsachnhasanxuat', name: 'app_danhsachnhasanxuat')]
    public function index(Connection $connection): Response
    {
        $sql = "SELECT * FROM Distributors";

        $result = $connection->executeQuery($sql)->fetchAllAssociative();

        return $this->render('danhsachnhasanxuat/index.html.twig', [
            'controller_name' => 'DanhsachnhasanxuatController',
            'result' => $result,
        ]);
    }
}
