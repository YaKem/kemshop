<?php

namespace App\Services;

use App\Entity\Cart;
use App\Entity\Order;
use App\Entity\CartDetails;
use App\Entity\OrderDetails;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;

class OrderService
{
    private $entityManager;
    private $repoProduct;

    public function __construct(EntityManagerInterface $entityManager, ProductRepository $repo)
    {
        $this->entityManager = $entityManager;
        $this->repoProduct = $repo;
    }

    public function createOrder($cart)
    {
        $order = new Order();

        $order->setReference($cart->getReference())
                ->setCarrierName($cart->getCarrierName())
                ->setCarrierPrice($cart->getCarrierPrice())
                ->setFullName($cart->getFullName())
                ->setDelivery($cart->getDelivery())
                ->setMoreInformations($cart->getMoreInformations())
                ->setQuantity($cart->getQuantity())
                ->setsubTotalHT($cart->getSubTotalHT())
                ->setTaxe($cart->getTaxe())
                ->setsubTotalTTC($cart->getSubTotalTTC())
                ->setUser($cart->getUser())
                ->setCreatedAt($cart->getCreatedAt())
        ;

        $this->entityManager->persist($order);

        $products = $cart->getCartDetails()->getValues();

        foreach($products as $cart_product) {
            $orderDetails = new OrderDetails();

            $orderDetails->setOrders($cart)
                        ->setProductName($cart_product->getProductName())
                        ->setProductPrice(round($cart_product->getProductPrice(), 2))
                        ->setQuantity($cart_product->getQuantity())
                        ->setSubTotalHT(round($cart_product->getSubtotalHT(), 2))
                        ->setSubtTotalTTC(round($cart_product->getSubtotalTTC(), 2))
                        ->setTaxe(round($cart_product->getTaxe(), 2))
            ;

            $this->entityManager->persist($orderDetails);
        }

        $this->entityManager->flush();

        return $order;
    }

    public function saveCart($data, $user)
    {
        /*
        [
            'products' => [],
            'data' => ['count' => '', 'subtotal' => '', 'taxe' => ''],
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
                ->setCarrierPrice(round($carrier->getPrice() / 100, 2))
                ->setFullName($address->getFullName())
                ->setDelivery($address)
                ->setMoreInformations($informations)
                ->setQuantity($data['data']['quantity_cart'])
                ->setSubTotalHT($data['data']['subTotalHT'])
                ->setTaxe($data['data']['taxe'])
                ->setSubTotalTTC(round(($data['data']['subTotalTTC'] + $carrier->getPrice()) / 100, 2))
                ->setUser($user)
                ->setCreatedAt(new \DateTime())
        ;

        $this->entityManager->persist($cart);

        $cart_details_array = [];

        $subtotal = $data['data']['subTotalHT'];

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
    
    public function getLineItems($cart)
    {
        $cartDetails = $cart->getCartDetails();

        // Products
        $line_items = [];
        foreach($cartDetails as $product_item) {
            $product = $this->repoProduct->findOneByName($product_item->getProductName());

            $line_items[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => $product->getPrice(),
                    'product_data' => [
                        'name' => $product->getName(),
                        'images' => [$_ENV['YOUR_DOMAIN'] . '/uploads/products/' . $product->getImage()]
                    ]
                ],
                'quantity' => $product_item->getQuantity()
            ];
        }
      
        // Carrier
        $line_items[] = [
            'price_data' => [
                'currency' => 'eur',
                'unit_amount' => $cart->getCarrierPrice() * 100,
                'product_data' => [
                    'name' => $cart->getCarrierName(),
                    'images' => [$_ENV['YOUR_DOMAIN'] . '/uploads/products/']
                    ]
                ],
                'quantity' => 1
        ];
        
        // Taxe
        $line_items[] = [
            'price_data' => [
                'currency' => 'eur',
                'unit_amount' => $cart->getTaxe() * 100,
                'product_data' => [
                    'name' => 'TVA (20%)',
                    'images' => [$_ENV['YOUR_DOMAIN'] . '/uploads/products/']
                ]
            ],
            'quantity' => 1
        ];
        return $line_items;
    }
}