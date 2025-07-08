<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Order;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class OrderValidateController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/order/success/{stripeSessionId}', name: 'app_order_validate')]
    public function index(Cart $cart, $stripeSessionId): Response
    {
        $order = $this->entityManager->getRepository(Order::class)->findOneByStripeSessionId($stripeSessionId);

        if (!$order || $order->getUser() != $this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        if (!$order->isIsPaid()) {
            // Vider la session cart
            $cart->remove();

            // Modification du statut isPaid de la commande à 1
            $order->setIsPaid(1);
            $this->entityManager->flush();

            // Envoyer un email au client pour notification de la commande
            // $emailService->sendOrderConfirmationEmail($order->getUser());
        }
        
        // Afficher quelques informations de la commande au client

        return $this->render('order_/index.html.twig',[
            'order' => $order
        ]);
    }
}
