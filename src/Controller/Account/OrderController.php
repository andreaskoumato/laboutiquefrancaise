<?php

namespace App\Controller\Account;

use App\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class OrderController extends AbstractController
{
    #[Route('/compte/commade/{id_order}', name: 'app_account_view_order')]
    public function index($id_order, OrderRepository $repo): Response
    {
        $order = $repo->findOneBy([
            'id'=> $id_order,
            'user' => $this->getUser(),
        ]);

        if(!$order) return $this->redirectToRoute('app_home');
        
        return $this->render('account/order/index.html.twig', [
            'order' => $order,
        ]);
    }
}
