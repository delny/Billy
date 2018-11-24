<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FamilyRepository")
 */
class Family
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $fid;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Person", mappedBy="parentsFamily")
     */
    private $children;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Person")
     */
    private $father;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Person")
     */
    private $mother;

    public function __construct()
    {
        $this->children = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFid(): ?string
    {
        return $this->fid;
    }

    public function setFid(string $fid): self
    {
        $this->fid = $fid;

        return $this;
    }

    /**
     * @return Collection|Person[]
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function addChild(Person $child): self
    {
        if (!$this->children->contains($child)) {
            $this->children[] = $child;
            $child->setParentsFamily($this);
        }

        return $this;
    }

    public function removeChild(Person $child): self
    {
        if ($this->children->contains($child)) {
            $this->children->removeElement($child);
            // set the owning side to null (unless already changed)
            if ($child->getParentsFamily() === $this) {
                $child->setParentsFamily(null);
            }
        }

        return $this;
    }

    public function getFather(): ?Person
    {
        return $this->father;
    }

    public function setFather(?Person $father): self
    {
        $this->father = $father;

        return $this;
    }

    public function getMother(): ?Person
    {
        return $this->mother;
    }

    public function setMother(?Person $mother): self
    {
        $this->mother = $mother;

        return $this;
    }
}
