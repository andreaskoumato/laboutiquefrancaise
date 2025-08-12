<?php

namespace App\Classe;

use Symfony\Component\HttpFoundation\RequestStack;

class Cart {

    public function __construct(private RequestStack $requestStack) {

    }

    // add() fonction permettant l'ajout d'un produit dans le panier
    public function add($product) {
        // call session

        $session = $this->requestStack->getSession();
        $cart = $session->get("cart");

        if(isset($cart[$product->getId()])) {

            $cart[$product->getId()] = [
                    'object' => $product,
                    'qty' =>  $cart[$product->getId()]['qty'] + 1
            ];

        } else {

            $cart[$product->getId()] = [
                    'object' => $product,
                    'qty' =>  1
            ];

        }

        $session->set("cart", $cart);
    }

    // decrease() fonction permettant la suppression d'un produit dans le panier

    public function decrease($id) {

        $session = $this->requestStack->getSession();
        $cart = $session->get("cart");

        if($cart[$id]["qty"] > 1) {
            $cart[$id]["qty"] = $cart[$id]["qty"] - 1;
        } else {
            unset($cart[$id]);
        }

        $session->set("cart", $cart);
    }

    // fullQuantity() fonction retournant le nombre total de produits dans le panier


    public function fullQuantity() {

        $cart = $this->getCart();
        $quantity = 0;

        if(!isset($cart)) { return $quantity ; }

        foreach($cart as $product) {
            $quantity += $product['qty'];
        }
        
        return $quantity;
    }

    // getTotalWt() fonction retournant le prix total des produits dans le panier

    public function getTotalWt() {

        $cart = $this->getCart();
        $price = 0;

        if(!isset($cart)) { return $price ; }

        foreach($cart as $product) {
            $price += $product['object']->getPriceWt() * $product['qty'];
        }

        return $price;
    }

    // getCart() fonction retournant le panier

    public function getCart() {
        return $this->requestStack->getSession()->get("cart");
    }

    // remove() fonction permettant de supprimer totalement le panier

    public function remove() {
        return $this->requestStack->getSession()->remove("cart");
    }

}