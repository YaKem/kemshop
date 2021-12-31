<?php

namespace App\Services;

use App\Entity\Cart;
use App\Entity\CartDetails;
use Doctrine\ORM\EntityManagerInterface;

class OrderService
{
    protected $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function createOrder($cart)
    {

    }

    public function saveCart($data, $user)
    {
        /*
        [
            'products' => [],
            'data' => [],
            'checkout' => [
                'address' => 'address',
                'carrier' => 'carrier'
            ]
        ]
        */

        $cart = new Cart();
        $reference = $this->generateReference();
        $address = $data['checkout']['address'];
        $carrier = $data['checkout']['carrier'];
        $informations = $data['checkout']['informations'];

        $cart->setReference($reference)
                ->setCarrierName($carrier->getName())
                ->setCarrierPrice($carrier->getPrice())
                ->setFullName($address->getFullName())
                ->setDelivery($address)
                ->setMoreInformations($informations)
                ->setQuantity($data['data']['quantity_cart'])
                ->setsubTotalHT($data['data']['subTotalHT'])
                ->setTaxe($data['data']['taxe'])
                ->setsubTotalTTC((($data['data']['subTotalTTC'] + $carrier->getPrice()) / 100)|number_format(2, ',', '.'))
                ->setUser($user)
                ->setCreatedAt(new \DateTime())
        ;

        $entityManager->persist($cart);

        $cart_details_array = [];

        foreach($data['products'] as $products) {
            $cartDetails = new CartDetails();

            $cartDetails->setCarts($cart)
                        ->setProductName($products['product']->getName())
                        ->setProductPrice($products['product']->getPrice())
                        ->setQuantity($products['quantity'])
                        ->setSubTotalHT($subtotal)
                        ->setSubtTotalTTC($subtotal * 1.2)
                        ->setTaxe($subtotal * 0.2)
            ;

            $this->entityManager->persist($cartDetails);
            $cart_details_array[] = $cartDetails;
        }

        $this->entityManager->flush();

        return $reference;
    }

    public function generateReference()
    {
        $date = new \DateTime();
        return $date->format('dmY') . uniqid();
    }

}