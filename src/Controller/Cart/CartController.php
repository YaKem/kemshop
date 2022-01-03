<?php

namespace App\Controller\Cart;

use App\Services\CartService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CartController extends AbstractController
{
    private $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * @Route("/cart", name="cart")
     */
    public function index(): Response
    {
        $cart = $this->cartService->getFull();

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
        $this->cartService->add($id);

        return $this->redirectToRoute('cart');
    }

    /**
     * @Route("cart/decrease/{id}", name="cart_decrease")
     */
    public function decreaseFromCart($id): Response
    {
        $this->cartService->decrease($id);

        return $this->redirectToRoute('cart');
    }

    /**
     * @Route("cart/delete/{id}", name="cart_delete")
     */
    public function deleteFromCart($id): Response
    {
        $this->cartService->delete($id);

        return $this->redirectToRoute('cart');
    }
}
