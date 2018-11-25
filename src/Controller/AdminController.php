<?php
namespace App\Controller;

use App\Entity\Person;
use App\Manager\FamilyManager;
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
     * @var PersonManager
     */
    private $pm;

    /**
     * @var FamilyManager
     */
    private $fm;

    /**
     * AdminController constructor.
     * @param PersonManager $personManager
     * @param FamilyManager $familyManager
     */
    public function __construct(PersonManager $personManager, FamilyManager $familyManager)
    {
        $this->pm = $personManager;
        $this->fm = $familyManager;
    }

    /**
     * @Route("/admin/import", name="app_admin_import")
     *
     * @param Request $request
     * @param PersonManager $personManager
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function import(Request $request)
    {
        $parser = new \PhpGedcom\Parser();
        $gedcom = $parser->parse('../import/billy.ged');
        $stats = [
            'person' => ['import' => 0, 'update' => 0],
            'family' => ['import' => 0, 'update' => 0],
        ];
        //importation des personnes
        foreach ($gedcom->getIndi() as $individual) {
            //get individual infos
            $lastName = $individual->getName()[0]->getSurn();
            $lastName = !empty($lastName) ? $lastName : 'Inconnu';
            $firstName = $individual->getName()[0]->getGivn();
            $firstName = !empty($firstName) ? $firstName : 'Inconnu';
            $birthDate = $this->getBirthDate($individual);
            $birthPlace = $this->getBirthPlace($individual);
            $deathDate = $this->getDeathDate($individual);
            $deathPlace = $this->getDeathPlace($individual);

            //create ou update person
            $person = $this->pm->getOrCreateByPid($individual->getId());
            if (empty($person->getId())) {
                $stats['person']['import']++;
            } else {
                $stats['person']['update']++;
            }
            $person->setPid($individual->getId());
            $person->setGender($individual->getSex());
            $person->setFirstName($firstName);
            $person->setLastName($lastName);
            if (!empty($birthDate)) {
                $person->setBirthDate(new \DateTime($birthDate));
            }
            if (!empty($birthPlace)) {
                $person->setBirthPlace($birthPlace);
            }
            if (!empty($deathDate)) {
                $person->setDeathDate(new \DateTime($deathDate));
            }
            if (!empty($deathPlace)) {
                $person->setDeathPlace($deathPlace);
            }
            $this->pm->save($person);
        }

        //importation des familles
        foreach ($gedcom->getFam() as $fam) {
            //create or update family
            $family = $this->fm->getOrCreateByFid($fam->getId());
            if (empty($family->getId())) {
                $stats['family']['import']++;
            } else {
                $stats['family']['update']++;
            }
            $family->setFid($fam->getId());
            //add father.
            if (!empty($fam->getHusb())) {
                $father = $this->pm->getByPid($fam->getHusb());
                if ($father instanceof Person) {
                    $family->setFather($father);
                }
            }
            //add mother.
            if (!empty($fam->getWife())) {
                $mother = $this->pm->getByPid($fam->getWife());
                if ($mother instanceof Person) {
                    $family->setMother($mother);
                }
            }
            //ad childrens.
            $childrens = $fam->getChil();
            if (!empty($childrens)) {
                foreach ($childrens as $children) {
                    $childrenPerson = $this->pm->getByPid($children);
                    if ($childrenPerson instanceof Person) {
                        $family->addChild($childrenPerson);
                    }
                }
            }
            $this->fm->save($family);
        }
        return $this->render('admin/import.html.twig', [
            'stats' => $stats,
        ]);
    }

    /**
     * @param Indi $indi
     * @return null|string
     */
    private function getBirthDate(Indi $indi)
    {
        $eventBirth = $indi->getEven('BIRT');
        if (empty($eventBirth)) {
            return null;
        }
        $birthDate = $eventBirth->getDate();
        if (\DateTime::createFromFormat('d M Y', $birthDate) !== false) {
            return $birthDate;
        }
        return null;
    }

    /**
     * @param Indi $indi
     * @return null
     */
    private function getBirthPlace(Indi $indi)
    {
        $eventBirth = $indi->getEven('BIRT');
        if (empty($eventBirth)) {
            return null;
        }
        $birthPlace = $eventBirth->getPlac();
        if (!empty($birthPlace) && !empty($birthPlace->getPlac())) {
            return $birthPlace->getPlac();
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
        if (empty($eventDeath)) {
            return null;
        }
        $deathDate = $eventDeath->getDate();
        if (\DateTime::createFromFormat('d M Y', $deathDate) !== false) {
            return $deathDate;
        }
        return null;
    }

    /**
     * @param Indi $indi
     * @return null
     */
    private function getDeathPlace(Indi $indi)
    {
        $eventDeath = $indi->getEven('DEAT');
        if (empty($eventDeath)) {
            return null;
        }
        $deathPlace = $eventDeath->getPlac();
        if (!empty($deathPlace) && !empty($deathPlace->getPlac())) {
            return $deathPlace->getPlac();
        }
        return null;
    }
}
