<?php

namespace App\Controller\Stripe;

use App\Entity\Order;
use App\Repository\ProductRepository;
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
    public function index(?Order $order, CartService $cartService, EntityManagerInterface $entityManager, ProductRepository $prodRepo): Response
    {
        if(!$order || $order->getUser() !== $this->getUser()) {
            return $this->redirectToRoute('home');
        }

        if(!$order->getIsPaid()) {
            // Order paid
            $order->setIsPaid(true);
            $entityManager->flush();

            // Update stock quantity
            $cart = $cartService->get();

            foreach($cart as $id => $quantity) {
                $product = $prodRepo->findOneById($id);
                $product->setQuantity($product->getQuantity() - $quantity);
            }
            $entityManager->flush();

            // Remove cart
            $cartService->remove();
            // Send email to user
        }

        return $this->render('stripe/stripe_success_payment/index.html.twig', compact('order'));
    }
}
