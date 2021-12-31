<?php

namespace App\Controller;

use App\Form\CheckoutType;
use App\Services\CartService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;

class CheckoutController extends AbstractController
{
    private $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * @Route("/checkout", name="checkout")
     */
    public function index(): Response
    {
        $user = $this->getUser();
        $cart = $this->cartService->getFull();

        if(!isset($cart['products'])) {
            return $this->redirectToRoute('home');
        }

        if(!$user->getAddresses()->getValues()) {
            $this->addFlash('notice', 'Please add an address to your account to continue your order');
            return $this->redirectToRoute('address_new');
        }

        $form = $this->createForm(CheckoutType::class, null, ['user' => $user]);

        return $this->render('checkout/index.html.twig', [
            'cart' => $cart,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/checkout/confirm", name="checkout_confirm")
     */
    public function confirm(Request $request): Response
    {
        $user = $this->getUser();
        $cart = $this->cartService->getFull();

        if(!isset($cart['products'])) {
            return $this->redirectToRoute('home');
        }

        if(!$user->getAddresses()->getValues()) {
            $this->addFlash('notice', 'Please add an address to your account to continue your order');
            return $this->redirectToRoute('address_new');
        }

        $form = $this->createForm(CheckoutType::class, null, ['user' => $user]);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            return $this->render('checkout/confirm.html.twig', [
                'cart' => $cart,
                'form' => $form->createView(),
                'address' => $data['address'],
                'carrier' => $data['carrier'],
                'informations' => $data['informations']
            ]);
        }

        return $this->redirectToRoute('checkout');
    }
}
