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
    public function sendEmail(Request $request, MailerInterface $mailer): Response
    {
         // Lấy dữ liệu từ form
         $recipientEmail = $request->request->get('recipient_email');
         $emailContent = $request->request->get('email_content');
 
         // Kiểm tra xem có địa chỉ email và nội dung email hay không
         if ($recipientEmail && $emailContent) {
             // Tạo một đối tượng Email
             $email = (new Email())
                 ->from($_ENV['MAILER_FROM'])
                 ->to($recipientEmail)
                 ->subject('Subject of the email')
                 ->html($emailContent);
 
             // Gửi email
             $mailer->send($email);
 
             // Thêm thông báo flash hoặc xử lý khác nếu cần
             $this->addFlash('success', 'Email has been sent successfully!');
         } else {
             // Thông báo lỗi nếu địa chỉ email hoặc nội dung email không được nhập
             $this->addFlash('error', 'Please enter both email address and email content.');
         }
 
         return $this->redirectToRoute('email');
     }
}
