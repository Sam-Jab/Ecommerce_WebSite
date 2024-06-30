<?php

namespace App\Controller;

use App\Entity\Orders;
use App\Entity\Products;
use App\Repository\OrdersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class OrderController extends AbstractController
{
    private $orderRepository;
    private $entityManager;

    public function __construct(
        OrdersRepository $orderRepository,
        EntityManagerInterface $doctrine)
    {
        $this->orderRepository = $orderRepository;
        $this->entityManager = $doctrine;
    }
       
    #[Route('/orders', name: 'orders_list')]
    public function index(): Response
    {
        $orders = $this->orderRepository->findAll();
        return $this->render('order/index.html.twig', [
            'orders' => $orders
        ]);
    }
    
    #[Route('/user/orders', name: 'user_order_list')]
    public function userOrders(): Response
    {
        if(!$this->getUser()){
            return $this->redirectToRoute('app_login');
        }
        return $this->render('order/user.html.twig', [
            'user' => $this->getUser(),
        ]);
    }

    #[Route('/store/order/{product}', name: 'order_store')]
    public function store(Products $product): Response
    {
        if(!$this->getUser()){
            return $this->redirectToRoute('app_login');
        }

        $orderExists = $this->orderRepository->findOneBy([
            'user' => $this->getUser(),
            'pname' => $product->getName()
        ]);

        if($orderExists){
            $this->addFlash(
                'warning',
                'You have already ordered this product'
            );
            return $this->redirectToRoute('user_order_list');
        }

        $order = new Orders();
        $order->setPname($product->getName());
        $order->setPrice($product->getPrice());
        $order->setStatus('En attend');
        $order->setUser($this->getUser());
        $this->entityManager->persist($order);

        // actually executes the queries (i.e. the INSERT query)
        $this->entityManager->flush();
        $this->addFlash(
            'success',
            'Your order is sent '
        );
        return $this->redirectToRoute('user_order_list');
    }

    #[Route('/update/order/{order}/{status}', name: 'order_status_update')]
    public function updateOrderStatus(Orders $order,$status): Response
    {
        $order->setStatus($status);
        $this->entityManager->persist($order);

        // actually executes the queries (i.e. the INSERT query)
        $this->entityManager->flush();
        $this->addFlash(
            'success',
            'The Order\'s status is updated'
        );
        return $this->redirectToRoute('orders_list');
    }

    #[Route('/update/order/{order}', name: 'order_delete')]
    public function deleteOrder(Orders $order): Response
    {
        $this->entityManager->remove($order);

        $this->entityManager->flush();
        $this->addFlash(
            'success',
            'Your order was deleted'
        );
        return $this->redirectToRoute('orders_list');
    }
}