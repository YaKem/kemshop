<?php

namespace App\Controller;

use App\Entity\Address;
use App\Form\AddressType;
use App\Repository\AddressRepository;
use App\Services\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/address")
 */
class AddressController extends AbstractController
{
    private $session;

    public function __construct(RequestStack $requestStack)
    {
        $this->session = $requestStack->getSession();
    }

    /**
     * @Route("/", name="address_index", methods={"GET"})
     */
    public function index(AddressRepository $addressRepository): Response
    {
        return $this->render('address/index.html.twig', [
            'addresses' => $addressRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="address_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager, CartService $cart): Response
    {
        $address = new Address();
        $form = $this->createForm(AddressType::class, $address);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $address->setUser($user);
            $entityManager->persist($address);
            $entityManager->flush();

            // If user have cart, he came from cart, so we redirect him to cart
            if($cart->getFull()) {
                return $this->redirectToRoute('checkout');
            }
            
            $this->addFlash('address_message', 'Your address has been saved');
            return $this->redirectToRoute('account', [], Response::HTTP_SEE_OTHER);
        }
        
        return $this->renderForm('address/new.html.twig', [
            'address' => $address,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="address_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Address $address, EntityManagerInterface $entityManager, CartService $cart): Response
    {
        $form = $this->createForm(AddressType::class, $address);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            if($this->session->get('checkout_data')) {
                $data = $this->session->get('checkout_data');
                $data['address'] = $address;

                $this->session->set('checkout_data', $data);

                return $this->redirectToRoute('checkout_confirm');
            }

            $this->addFlash('address_message', 'Your address has been edited');
            return $this->redirectToRoute('account', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('address/edit.html.twig', [
            'address' => $address,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="address_delete", methods={"POST"})
     */
    public function delete(Request $request, Address $address, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$address->getId(), $request->request->get('_token'))) {
            $entityManager->remove($address);
            $entityManager->flush();
            $this->addFlash('address_message', 'Your address has been deleted');
        }

        return $this->redirectToRoute('account', [], Response::HTTP_SEE_OTHER);
    }
}
