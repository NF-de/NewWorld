<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class FormulaireEntrepriseController extends AbstractController
{
    #[Route('/formentreprise', name: 'app_formulaire_entreprise')]
    public function index(): Response
    {
        return $this->render('formulaire_entreprise/index.html.twig', [
            'controller_name' => 'FormulaireEntrepriseController',
        ]);
    }
}
