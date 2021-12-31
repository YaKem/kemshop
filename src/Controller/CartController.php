<?php

namespace App\Controller;

use App\Services\CartService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CartController extends AbstractController
{
    private $cart;

    public function __construct(CartService $cart)
    {
        $this->cart = $cart;
    }

    /**
     * @Route("/cart", name="cart")
     */
    public function index(): Response
    {
        $cart = $this->cart->getFull();

        if(!isset($cart['products'])) {
            return $this->redirectToRoute('home');
        }

        return $this->render('cart/index.html.twig', [
            'cart' => $cart
        ]);
    }

    /**
     * @Route("cart/add/{id}", name="cart_add")
     */
    public function addToCart($id): Response
    {
        $this->cart->add($id);

        return $this->redirectToRoute('cart');
    }

    /**
     * @Route("cart/decrease/{id}", name="cart_decrease")
     */
    public function decreaseFromCart($id): Response
    {
        $this->cart->decrease($id);

        return $this->redirectToRoute('cart');
    }

    /**
     * @Route("cart/delete/{id}", name="cart_delete")
     */
    public function deleteFromCart($id): Response
    {
        $this->cart->delete($id);

        return $this->redirectToRoute('cart');
    }
}
