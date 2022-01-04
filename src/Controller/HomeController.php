<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\SearchType;
use Symfony\Component\Dotenv\Dotenv;
use App\Repository\ProductRepository;
use App\Repository\HomeSliderRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(ProductRepository $repoProduct, HomeSliderRepository $repoSlider): Response
    {
        return $this->render('home/index.html.twig', [
            'products' => $repoProduct->findAll(),
            'productBestSeller' => $repoProduct->findByIsBestSeller(1),
            'productSpecialOffer' => $repoProduct->findByIsSpecialOffer(1),
            'productNewArrival' => $repoProduct->findByIsNewArrival(1),
            'productFeatured' => $repoProduct->findByIsFeatured(1),
            'slider' => $repoSlider->findByIsDisplayed(true)
        ]);
    }

    /**
     * @Route("/product/{slug}", name="product_show")
     */
    public function show(?Product $product): Response
    {
        if(!$product) {
            return $this->redirectToRoute('home');
        }
        
        return $this->render('home/product_detail.html.twig', [
            'product' => $product
        ]);
    }

    /**
     * @Route("/shop", name="shop")
     */
    public function shop(ProductRepository $repoProduct, Request $request): Response
    {
        $form = $this->createForm(SearchType::class, null);
        $search = new Search();
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

        }

        return $this->render('home/shop.html.twig', [
            'products' => $repoProduct->findAll(),
            'form' => $form->createView()
        ]);
    }

}
