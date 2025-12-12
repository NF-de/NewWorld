<?php

namespace App\Controller;

use App\Form\FormulaireEntrepriseType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class FormulaireEntrepriseController extends AbstractController
{
    #[Route('/formentreprise', name: 'app_formulaire_entreprise')]
    public function index(Request $request): Response
    {

        $form = $this->createForm(FormulaireEntrepriseType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entreprise = $form->getData();
        }

        return $this->render('formulaire_entreprise/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
