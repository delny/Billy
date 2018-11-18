<?php
namespace App\Controller;

use App\Manager\PersonManager;
use PhpGedcom\Record\Indi;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AdminController
 * @package App\Controller
 */
class AdminController extends Controller 
{
    /**
     * @Route("/admin/import", name="app_admin_import")
     */
    public function import(Request $request, PersonManager $personManager)
    {
        $parser = new \PhpGedcom\Parser();
        $gedcom = $parser->parse('../import/billy.ged');
        $id = 1;
        //importation des personnes
        foreach ($gedcom->getIndi() as $individual) {
            if($id == 4)
            {
                dump($individual);
            }
            // TODO : update if already exist
            $lastName = $individual->getName()[0]->getSurn();
            $lastName = !empty($lastName) ? $lastName : 'Inconnu';
            $firstName = $individual->getName()[0]->getGivn();
            $firstName = !empty($firstName) ? $firstName : 'Inconnu';
            $birthDate = $this->getBirthDate($individual);

            //create person
            $person = $personManager->create();
            $person->setFirstName($firstName);
            $person->setLastName($lastName);
            if(!empty($birthDate)){
                $person->setBirthDate(new \DateTime($birthDate));
            }
            $personManager->save($person);
            $id++;
        }
        return $this->render('admin/import.html.twig', [
            'count' => $id,
        ]);
    }

    /**
     * @param Indi $indi
     * @return null|string
     */
    private function getBirthDate(Indi $indi)
    {
        $eventBirth = $indi->getEven('BIRT');
        if(empty($eventBirth))
        {
            return null;
        }
        $birthDate = $eventBirth->getDate();
        if(\DateTime::createFromFormat('d M Y', $birthDate) !== false)
        {
            return $birthDate;
        }
        return null;
    }
}
