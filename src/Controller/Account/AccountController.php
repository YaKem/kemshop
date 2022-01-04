<?php

namespace App\Controller\Account;

use App\Entity\Order;
use App\Repository\OrderRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
     * @Route("/account")
     */
class AccountController extends AbstractController
{
    /**
     * @Route("/", name="account")
     */
    public function index(OrderRepository $orderRepo): Response
    {
        $orders = $orderRepo->findBy(['isPaid' => true, 'user' => $this->getUser()], ['id' => 'DESC']);

        return $this->render('account/index.html.twig', compact('orders'));
    }

    /**
     * @Route("/order/{id}", name="account_order_show")
     */
    public function show(Order $order): Response
    {
        if(!$order || $order->getUser() !== $this->getUser()) {
            return $this->redirectToRoute('home');
        }

        if(!$order->getIsPaid()) {
            return $this->redirectToRoute('account');
        }

        return $this->render('account/show_order.html.twig', compact('order'));
    }
}
