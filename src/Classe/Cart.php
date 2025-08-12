<?php

namespace App\Classe;

use Symfony\Component\HttpFoundation\RequestStack;

class Cart {

    public function __construct(private RequestStack $requestStack) {

    }

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


        // add +1
        

        // cart session

        $session->set("cart", $cart);
    }

    public function getCart() {
        return $this->requestStack->getSession()->get("cart");
    }

}