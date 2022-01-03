<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Dotenv\Dotenv;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(ProductRepository $repoProduct): Response
    {
        return $this->render('home/index.html.twig', [
            'products' => $repoProduct->findAll(),
            'productBestSeller' => $repoProduct->findByIsBestSeller(1),
            'productSpecialOffer' => $repoProduct->findByIsSpecialOffer(1),
            'productNewArrival' => $repoProduct->findByIsNewArrival(1),
            'productFeatured' => $repoProduct->findByIsFeatured(1)
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
}
