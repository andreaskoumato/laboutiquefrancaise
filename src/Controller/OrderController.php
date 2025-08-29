<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Order;
use App\Entity\OrderDetail;
use App\Form\OrderType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
            'action' => $this->generateUrl('app_order_summary'),
        ]);
        

        return $this->render('order/index.html.twig', [
            'deliveryForm'=> $form->createView(),
        ]);
    }

    // 2ere etape du tunnel d'achat
    // sommaire de la commande
    // insertion en base de donnée
    // Préparation du paiement via Stripe

    #[Route('/commande/recapitulatif', name: 'app_order_summary')]
    public function add(Request $request, Cart $cart, EntityManagerInterface $em): Response
    {

        if($request->getMethod() != 'POST') {
            return $this->redirectToRoute('app_cart');
        }

        $products = $cart->getCart();
        $form = $this->createForm(OrderType::class, null, [
            'addresses' => $this->getUser()->getAddresses(),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $addressObj = $form->get('addresses')->getData();

            $address = $addressObj->getFirstname() . ' ' . $addressObj->getLastname() . '<br />'; 
            $address .= $addressObj->getAddress() . '<br />';
            $address .= $addressObj->getPostal() . ' ' . $addressObj->getCity() . ' ';
            $address .= $addressObj->getCountry() . '<br />';
            $address .= $addressObj->getPhone();

            //dd($cart);
            $order = new Order();
            $order->setUser($this->getUser());
            $order->setCreatedAt(new \DateTime());
            $order->setState(1);
            $order->setCarrierName($form->get('carriers')->getData()->getName());
            $order->setCarrierPrice($form->get('carriers')->getData()->getPrice());
            $order->setDelivery($address);
            
            foreach ($products as $product) {
                $orderDetail = new OrderDetail();
                $orderDetail->setProductName($product['object']->getName());
                $orderDetail->setProductPrice($product['object']->getPrice());
                $orderDetail->setProductIllustration($product['object']->getIllustration());
                $orderDetail->setProductTva($product['object']->getTva());
                $orderDetail->setProductQuantity($product['qty']);
                
                $order->addOrderDetail($orderDetail);
            }

            $em->persist($order);
            $em->flush();


        }

        return $this->render('order/summary.html.twig', [
            'choices' => $form->getData(),
            'cart' => $cart,
            'totalWt' => $cart->getTotalWt(),
            'order' => $order
        ]);
    }
}
