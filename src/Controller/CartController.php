<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CartController extends AbstractController
{
    #[Route('/mon-panier', name: 'app_cart')]
    public function index(Cart $cart): Response
    {
        return $this->render('cart/index.html.twig', [
            'cart'=> $cart->getCart()
        ]);
    }

    #[Route('/cart/add/{id}', name: 'app_cart_add')]
    public function add($id, Cart $cart, ProductRepository $repo): Response
    {
        $product = $repo->findOneById($id);
        $cart->add($product);

        $this->addFlash('success','Produit correctement ajouté à votre panier.');
        return $this->redirectToRoute('app_product', ['slug'=> $product->getSlug()]);
    }
}
