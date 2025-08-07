<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegisterUserTest extends WebTestCase
{
    public function testSomething(): void
    {

        /*
        * 1. Créer un faux client (navigateur) de pointer vers une URL
        * 2. Remplier les champs de mon formulaire d'inscription
        * 3. Est-ce que tu peux vérifier si dans ma page j'ai le message (alerte) suivante : Votre compte est correctement créé
        */

        //1

        $client = static::createClient();
        $client->request("GET","/inscription");

        // 2 (fistname, lastname, email, password, confirmation)

        $client->submitForm("Valider", [

            "register_user[email]" => "andreas@mossosouk.com",
            "register_user[plainPassword][first]" => "Azerty32$",
            "register_user[plainPassword][second]" => "Azerty32$",
            "register_user[firstname]"  => "Andreas",
            "register_user[lastname]" => "KOUMATO",
        ]);

        // follow
        $this->assertResponseRedirects("/connexion");
        $client->followRedirect();

        // 3 

        $this->assertSelectorExists('div:contains("Votre compte est correctement créé, veuillez vous connecter!")') ;
        

    }
}
