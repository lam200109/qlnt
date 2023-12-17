<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;

class EmailController extends AbstractController
{

    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }


    #[Route('/email', name: 'app_email')]
    public function index(Connection $connection, Request $request): Response
    {


        $sql = "SELECT * FROM Users";
        $users = $connection->executeQuery($sql)->fetchAllAssociative();



        return $this->render('email/index.html.twig', [
            'result' => $users,
        ]);
    }


    #[Route('/send-email', name: 'send_email', methods: ['POST'])]
    public function sendEmail(Request $request): Response
    {
        // Lấy dữ liệu từ form
        $recipientEmail = $request->request->get('recipient_email');
        $content = $request->request->get('email_content');

        // Tạo một đối tượng Email
        $email = (new Email())
            ->from('lam200109@gmail.com')
            ->to($recipientEmail)
            ->subject('Test email')
            ->text($content);

        // Gửi email
        $this->mailer->send($email);

        // Chuyển hướng hoặc hiển thị thông báo thành công
        $this->addFlash('success', 'Email sent successfully!');
        return $this->redirectToRoute('email');
    }
}
