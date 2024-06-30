<?php

namespace App\Controller;

use App\Entity\Products;
use App\Form\ProductType;
use App\Repository\ProductsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
class ProductController extends AbstractController
{
    private $productRepository ; 
    private $entityManager ; 
    public function __construct(ProductsRepository $productRepository , 
    EntityManagerInterface $doctrine  ){
        $this->productRepository = $productRepository ; 
        $this->entityManager = $doctrine ; 
    }
    #[Route('/product', name: 'product_list')]
    /**
     * @IsGranted("ROLE_ADMIN", statusCode=404, message="Page not found")
     */
    public function index(): Response
    {
        $products = $this->productRepository->findAll() ;  
        return $this->render('product/index.html.twig', [
            'products' => $products ,
        ]);
    }


    #[Route('/store/product', name: 'product_store')]
     /**
     * @IsGranted("ROLE_ADMIN", statusCode=404, message="Page not found")
     */
    public function store(Request $request): Response
    {
        $products = new Products() ; 
        $form = $this->createForm(ProductType::class,$products); 
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) { 
            $product = $form->getData();    
            if($request->files->get('product')['image']){
                $image = $request->files->get('product')['image'] ; 
                $image_name  = time().'_'.$image->getClientOriginalName() ; 
                $image->move($this->getParameter('image_directory') , $image_name);
                $product->setImage($image_name) ;  
            }   
            $this->entityManager->persist($product);
            $this->entityManager->flush() ; 
            $this->addFlash(
                'success', 'Your Product was Saved '
            ) ; 
            return $this->redirectToRoute('product_list') ; 
        }
        return $this->render('product/create.html.twig', [
            'form' => $form ,
        ]);
    }


    #[Route('/product/detail/{id}', name: 'product_show')]
     /**
     * @IsGranted("ROLE_ADMIN", statusCode=404, message="Page not found")
     */
    public function show(Products $product): Response
    {
        return $this->render('product/show.html.twig', [
            'product' => $product ,
            'photo_url' => 'http://127.0.0.1:8001/uploads/'
        ]);
    }



    #[Route('/product/edit/{id}', name: 'product_edit')]
     /**
     * @IsGranted("ROLE_ADMIN", statusCode=404, message="Page not found")
     */
    public function editProduct( Request $request ,Products $products ): Response
    {
            $form = $this->createForm(ProductType::class,$products); 
            $form->handleRequest($request);
    
            if ($form->isSubmitted() && $form->isValid()) { 
                $product = $form->getData();    
                if($request->files->get('product')['image']){
                    $image = $request->files->get('product')['image'] ; 
                    $image_name  = time().'_'.$image->getClientOriginalName() ; 
                    $image->move($this->getParameter('image_directory') , $image_name);
                    $product->setImage($image_name) ;  
                }   
                $this->entityManager->persist($product);
                $this->entityManager->flush() ; 
                $this->addFlash(
                    'success', 'Your Product was Edited '
                ) ; 
                return $this->redirectToRoute('product_list') ; 
            }
            return $this->render('product/edit.html.twig', [
                'form' => $form ,
            ]);
        }



        #[Route('/product/delete/{id}', name: 'product_delete')]
        public function delete(Products $products): Response
        {
            $filesystem = new Filesystem(); 
            $imagePath = './uploads/'.$products->getImage() ; 
            if($filesystem->exists($imagePath) && $imagePath != '../../uploads/noimg.jpg'){
                $filesystem->remove($imagePath) ; 
            } 
            $this->entityManager->remove($products) ; 
            $this->entityManager->flush() ; 
            $this->addFlash(
                'success', 'Your Product was Daleted '
            ) ; 

            return $this->redirectToRoute('product_list') ; 
        }
    
}
