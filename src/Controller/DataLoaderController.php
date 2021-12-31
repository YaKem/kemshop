<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DataLoaderController extends AbstractController
{
    /**
     * @Route("/data", name="data_loader")
     */
    public function index(EntityManagerInterface $entityManager): Response
    {
        $file_product = dirname(dirname(__DIR__)) . "\product.json";
        $file_category = dirname(dirname(__DIR__)) . "\category.json";

        $data_products = json_decode(file_get_contents($file_product));
        $data_categories = json_decode(file_get_contents($file_category));

        $data_categories = $data_categories[2]->data;
        $data_products = $data_products[2]->data;

        $categories = [];

        foreach($data_categories as $data_category) {
            $category = new Category();
            $category->setName($data_category->name)
                    ->setImage($data_category->image);
            $entityManager->persist($category);
            $categories[] = $category;
        }
        // dd($categories);

        $products = [];

        foreach($data_products as $data_product) {
            $product = new Product();
            $product->setName($data_product->name)
                    ->setDescription($data_product->description)
                    ->setPrice($data_product->price)
                    ->setIsBestSeller($data_product->is_best_seller)
                    ->setIsNewArrival($data_product->is_new_arrival)
                    ->setIsFeatured($data_product->is_featured)
                    ->setIsSpecialOffer($data_product->is_special_offer)
                    ->setImage($data_product->image)
                    ->setQuantity($data_product->quantity)
                    ->setTags($data_product->tags)
                    ->setSlug($data_product->slug)
                    ->setCreatedAt(new \DateTime());
            $entityManager->persist($product);
            $products[] = $product;
        }

        // $entityManager->flush();

        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/DataLoaderController.php',
        ]);
    }
}
