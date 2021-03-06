<?php

namespace App\Controller\Stripe;

use Stripe\Stripe;
use App\Entity\Cart;
use Stripe\Checkout\Session;
use App\Services\OrderService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StripeCheckoutSessionController extends AbstractController
{
    /**
     * @Route("/create-checkout-session/{reference}", name="create_checkout_session")
     */
    public function index(?Cart $cart, OrderService $orderService, EntityManagerInterface $entityManager): Response
    {   
        if(!$cart) {
            return $this->redirectToRoute('home');
        }

        $order = $orderService->createOrder($cart);

        Stripe::setApiKey('sk_test_51KDQWGKDe6DxA131ymIUppa9j86fu1OsjetEFEqPZ6xHsTKWvH7fhQ6mdNKh67cbEOR8VAvNoHBDzxVs7YtCZJve00Dt0FK4Eg');
        
        $session = Session::create([
            'customer_email' => $this->getUser()->getEmail(),
            'payment_method_types' => ['card'],
            'line_items' => [$orderService->getLineItems($cart)],
            'mode' => 'payment',
              'success_url' => $_ENV['YOUR_DOMAIN'] . '/stripe-payment-success/{CHECKOUT_SESSION_ID}',
              'cancel_url' => $_ENV['YOUR_DOMAIN'] . '/stripe-payment-cancel/{CHECKOUT_SESSION_ID}',
        ]);

        $order->setStripeCheckoutSessionId($session->id);
        $entityManager->flush();
        
        return $this->json(['id' => $session->id]);
    }
}
