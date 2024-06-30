<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Categories;
use App\Repository\ProductsRepository;
use App\Repository\CategoriesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    private $productRepository;
    private $categoryRepository;
    private $entityManager;

    public function __construct(
        ProductsRepository $productRepository,
        CategoriesRepository $categoryRepository,
        EntityManagerInterface $doctrine)
    {
        $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;
        $this->entityManager = $doctrine;
    }

    #[Route('/', name: 'home')]
    public function index(): Response
    {
        $products = $this->productRepository->findAll();
        $categories = $this->categoryRepository->findAll();
        return $this->render('home/index.html.twig', [
            'products' => $products,
            'categories' => $categories,
            'photo_url' => 'http://127.0.0.1:8000/uploads/'
        ]);
    }

    #[Route('/product/{category}', name: 'product_category')]
    public function categoryProducts(Categories $category): Response
    {
        $categories = $this->categoryRepository->findAll();
        return $this->render('home/index.html.twig', [
            'products' => $category->getProducts(),
            'categories' => $categories,
            'photo_url' => 'http://127.0.0.1:8000/uploads/'
        ]);
    }
}