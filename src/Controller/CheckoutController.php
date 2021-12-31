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
    private $session;

    public function __construct(CartService $cartService, RequestStack $requestStack)
    {
        $this->cartService = $cartService;
        $this->session = $requestStack->getSession();
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

        if($this->session->get('checkout_data')) {
            return $this->redirectToRoute('checkout_confirm');
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
        
        if($form->isSubmitted() && $form->isValid() || $this->session->get('checkout_data')) {
            
            if($this->session->get('checkout_data')) {
                $data = $this->session->get('checkout_data');
            } else {
                $data = $form->getData();
                $this->session->set('checkout_data', $data);
            }
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

    /**
     * @Route("/checkout/edit", name="checkout_edit")
     */
    public function checkoutEdit()
    {
        $this->session->set('checkout_data', []);

        return $this->redirectToRoute('checkout');
    }
}