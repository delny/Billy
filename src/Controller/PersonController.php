<?php

namespace App\Controller;


use App\Manager\PersonManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class PersonController extends Controller
{

    /**
     * @Route("/person/list/{letter}", name="app_person_list", defaults={"letter":"A"})
     *
     * @param Request $request
     * @param string $letter
     * @param PersonManager $personManager
     * @return string
     */
    public function listAction(Request $request, string $letter, PersonManager $personManager)
    {
        return $this->render('pages/list.html.twig', [
            'persons' => $personManager->findByLetter($letter),
        ]);
    }
}