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
     *
     * @param Request $request
     * @param PersonManager $personManager
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function import(Request $request, PersonManager $personManager)
    {
        $parser = new \PhpGedcom\Parser();
        $gedcom = $parser->parse('../import/billy.ged');
        $importCount = 0;
        $updateCount = 0;
        //importation des personnes
        foreach ($gedcom->getIndi() as $individual) {
            //get individual infos
            $lastName = $individual->getName()[0]->getSurn();
            $lastName = !empty($lastName) ? $lastName : 'Inconnu';
            $firstName = $individual->getName()[0]->getGivn();
            $firstName = !empty($firstName) ? $firstName : 'Inconnu';
            $birthDate = $this->getBirthDate($individual);
            $deathDate = $this->getDeathDate($individual);

            //create ou update person
            $person = $personManager->getOrCreateByPid($individual->getId());
            if(empty($person->getId()))
            {
                $importCount++;
            } else {
                $updateCount++;
            }
            $person->setPid($individual->getId());
            $person->setGender($individual->getSex());
            $person->setFirstName($firstName);
            $person->setLastName($lastName);
            if(!empty($birthDate)){
                $person->setBirthDate(new \DateTime($birthDate));
            }
            if(!empty($deathDate)){
                $person->setDeathDate(new \DateTime($deathDate));
            }
            $personManager->save($person);
        }
        return $this->render('admin/import.html.twig', [
            'importcount' => $importCount,
            'updatecount' => $updateCount,
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

    /**
     * @param Indi $indi
     * @return null
     */
    private function getDeathDate(Indi $indi)
    {
        $eventDeath = $indi->getEven('DEAT');
        if(empty($eventDeath))
        {
            return null;
        }
        $deathDate = $eventDeath->getDate();
        if(\DateTime::createFromFormat('d M Y', $deathDate) !== false)
        {
            return $deathDate;
        }
        return null;
    }
}
