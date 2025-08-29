<?php

namespace App\Controller\Account;

use App\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{

    #[Route('/compte', name: 'app_account')]
    public function index(OrderRepository $repo): Response
    {
        $orders = $repo->findBy([
            'user' => $this->getUser(),
            'state' => [ 2, 3]
        ]);


        return $this->render('account/index.html.twig', [
            'orders'=> $orders
        ]);
    }

}
