<?php

namespace App\Services;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class CartService
{
    private $session;
    private $entityManager;
    private const TVA = 0.2;
    
    public function __construct(EntityManagerInterface $entityManager, RequestStack $requestStack)
    {
        $this->entityManager = $entityManager;
        $this->session = $requestStack->getSession();
    }
    
    // Get the cart from the session
    public function get()
    {
        return $this->session->get('cart', []);
    }

    // Save the cart in the session
    public function update($cart)
    {
        $this->session->set('cart', $cart);
        return $this->session->set('cartFull', $this->getFull());       
    }

    // Add one quantity to item
    public function add($id)
    {
        $cart = $this->get();

        if(isset($cart[$id])) {
            $cart[$id]++;
        } else {
            $cart[$id] = 1;
        }
        
        return $this->update($cart);
    }
    
    // Remove one quantity from item
    public function decrease($id)
    {
        $cart = $this->get();

        if(isset($cart[$id])) {
            if($cart[$id] > 1) {
                $cart[$id]--;
            } else {
                unset($cart[$id]);
            }
        }
        return $this->update($cart);
    }

    // Delete the entire item
    public function delete($id)
    {
        $cart = $this->get();

        unset($cart[$id]);

        return $this->update($cart);
    }

    // Delete the entire cart
    public function remove()
    {
        return $this->update([]);     
    }

    // Get the full cart with products
    public function getFull()
    {
        $cartComplete = [];
        $cartQuantity = 0;
        $cartSubtotal = 0;

        if(!empty($this->get())) {
            foreach($this->get() as $id => $quantity) {
                $product_object = $this->entityManager->getRepository(Product::class)->findOneById($id);

                if(!$product_object) {
                    $this->delete($id);
                    continue;
                }
  
                $cartQuantity += $quantity;
                $cartSubtotal += $quantity * $product_object->getPrice() / 100;

                $cartComplete['products'][] = [
                    'product' => $product_object,
                    'quantity' => $quantity,
                ];
            }

            $cartComplete['data'] = [
                'quantity_cart' => $cartQuantity,
                'subTotalHT' => round($cartSubtotal, 2),
                'taxe' => round($cartSubtotal * self::TVA, 2),
                'subTotalTTC' => round($cartSubtotal * (1 + self::TVA), 2)
            ];
        }

        return $cartComplete;
    }
}