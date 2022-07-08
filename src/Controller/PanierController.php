<?php

namespace App\Controller;

use App\Repository\ProduitRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/panier", name="panier_")
 */
class PanierController extends AbstractController
{
    /**
     * @Route("/", name="show")
     */
    public function show(SessionInterface $session, ProduitRepository $repo): Response
    {
        $panier = $session->get("panier", []);

        $dataPanier = [];
        $total = 0;

        /*
         - pour chaque ligne de mon tableau panier dans la session, je recupere le produit qui correspond à l'id qui est en clé et la quantité en valeur.
         - dans le tableau dataPanier je rajoute à chaque tour de boucle un nouveau tableau qui contient une clé "produit" avec comme valeur le produit récuperé, et une autre "quantite" qui contint la quantite du produit en question
        - puis à chaque tour de boucle je calcule le prix total du produit (prix du produit * quantité) et je l'ajoute à ma variable $total 
        */
        foreach ($panier as $id => $quantite ) {
            
            $produit = $repo->find($id);
            $dataPanier[]=
            [
                "produit" => $produit,
                "quantite" => $quantite
            ];

            $total += $produit->getPrix() * $quantite;

        }

        // dd($dataPanier);

        return $this->render('panier/index.html.twig', [
            'dataPanier' => $dataPanier,
            'total' => $total

        ]);

    }


    /**
     * @Route("/add/{id<\d+>}", name="add")
     */
    public function add($id, SessionInterface $session)
    {
        // on récupere ou on crée le panier dans la sesion
        $panier = $session->get('panier', []);

        // on verifie si l'id existe déja, dans ce cas on incremente sinon on le crée
         if ( empty( $panier[$id] ))
         {
            $panier[$id] = 1;
         }else{
            $panier[$id]++;
         }

         //on sauvegarde dans la session
         $session->set("panier", $panier);

        //  dd($session->get("panier"));

         return $this->redirectToRoute("panier_show");

    }



}
