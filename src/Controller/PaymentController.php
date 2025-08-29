<?php

namespace App\Controller;

use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PaymentController extends AbstractController
{
    #[Route('/commande/paiement/{id_order}', name: 'app_payment')]
    public function index($id_order, OrderRepository $repo, EntityManagerInterface $em): Response
    {
        Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']) ;
        $YOUR_DOMAIN = $_ENV['DOMAIN'] ;

        //$order = $repo->findOneById($id_order);
        $order = $repo->findOneBy([
            'id'   => $id_order,
            'user' => $this->getUser()
        ]);
        
        if (!$order) {
            return $this->redirectToRoute('app_home') ;
        }

        $productsStripe = [];

        foreach ($order->getOrderDetails() as $product)
        {
            $productsStripe[] = [
                'price_data' => [
                    'currency' => 'EUR',
                    'unit_amount' => number_format($product->getProductPriceWt() * 100, 0, '', ''),
                    'product_data' => [
                        'name' => $product->getProductName(),
                        'images' => [
                            $YOUR_DOMAIN . '/uploads/' . $product->getProductIllustration() 
                        ] 
                    ]
                ],
                'quantity' => $product->getProductQuantity(),
            ];
        }

        $productsStripe[] = [
                'price_data' => [
                    'currency' => 'EUR',
                    'unit_amount' => number_format($order->getCarrierPrice() * 100, 0, '',''),
                    'product_data' => [
                        'name' => 'Transporteur : ' . $order->getCarrierName(),
                    ]
                ],
                'quantity' => 1
            ];
        

        $checkout_session = Session::create([
            'customer_email' => $this->getUser()->getEmail(),
            'line_items' => [[
                $productsStripe
            ]], 
            'mode' => 'payment',
            'success_url' => $YOUR_DOMAIN . '/commande/merci/{CHECKOUT_SESSION_ID}',
            'cancel_url' => $YOUR_DOMAIN . '/mon-panier/annulation',
        ]);
        
        $order->setStripeSessionId($checkout_session->id);
        $em->flush();

        return $this->redirect($checkout_session->url);
    }

    #[Route('/commande/merci/{stripe_session_id}', name: 'app_payment_success')]
    public function success($stripe_session_id, OrderRepository $repo, EntityManagerInterface $em): Response
    {
        $order = $repo->findOneBy([
            'stripe_session_id' => $stripe_session_id,
            'user'=> $this->getUser(),
        ]);

        if (!$order) {
            return $this->redirectToRoute('app_home');
        }

        if($order->getState() == 1) $order->setState(2);

        $em->flush();

        return $this->render('payment/success.html.twig', [
            'order'=> $order,
        ]);

    }
}
