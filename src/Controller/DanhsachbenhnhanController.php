<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface; // Sử dụng Doctrine ORM
use App\Entity\Benhnhan; // Import Entity của bạn
use Symfony\Component\HttpFoundation\Request;

class DanhsachbenhnhanController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/danhsachbenhnhan', name: 'app_danhsachbenhnhan')]
    public function index(Request $request): Response
    {
        // Lấy danh sách tất cả các bệnh nhân có role = 2
        $repository = $this->entityManager->getRepository(Benhnhan::class);
        $allPatients = $repository->findBy(['Role' => 3]);

        $searchedPatients = [];
        $HoTen = $request->request->get('HoTen');

        if ($HoTen && $request->request->has('submit')) {
            // Tìm kiếm bệnh nhân theo HoTen nếu có giá trị được gửi từ form
            $searchedPatients = $repository->createQueryBuilder('p')
                ->where('p.Role = :Role')
                ->andWhere('p.HoTen LIKE :HoTen')
                ->setParameters(['Role' => 3, 'HoTen' => '%' . $HoTen . '%'])
                ->getQuery()
                ->getResult();
                
        }
        return $this->render('danhsachbenhnhan/index.html.twig', [
            'allPatients' => $allPatients,
            'searchedPatients' => $searchedPatients,
        ]);
    }
}