<?php

namespace App\Controller;

use App\Form\OrderType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class OrderController extends AbstractController
{

    // 1ere etape du tunnel d'achat
    // choix de l'adresse de livraison et du transporteur

    #[Route('/commande/livraison', name: 'app_order')]
    public function index(): Response
    {

        $addresses = $this->getUser()->getAddresses();

        if(count($addresses) == 0) {
            return $this->redirectToRoute('app_account_address_form');
        }
        
        $form = $this->createForm(OrderType::class, null, [
            'addresses' => $this->getUser()->getAddresses(),
        ]);

        return $this->render('order/index.html.twig', [
            'deliveryForm'=> $form->createView(),
        ]);
    }
}
