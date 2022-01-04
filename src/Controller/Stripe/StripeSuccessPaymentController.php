<?php

namespace App\Controller\Stripe;

use App\Entity\Order;
use App\Services\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StripeSuccessPaymentController extends AbstractController
{
    /**
     * @Route("/stripe-payment-success/{StripeCheckoutSessionId}", name="stripe_payment_success")
     */
    public function index(?Order $order, CartService $cartService, EntityManagerInterface $entityManager): Response
    {
        if(!$order || $order->getUser() !== $this->getUser()) {
            return $this->redirectToRoute('home');
        }

        if(!$order->getIsPaid()) {
            // Order paid
            $order->setIsPaid(true);
            $entityManager->flush();
            // Remove cart
            $cartService->remove();
            // Send email to user
        }

        return $this->render('stripe/stripe_success_payment/index.html.twig', compact('order'));
    }
}
