<?php
// Copyright (C) 11 / 2019 | David annebicque | IUT de Troyes - All Rights Reserved
// @file /Users/davidannebicque/htdocs/intranetv3/src/Entity/IndisponibilitePersonnel.php
// @author     David Annebicque
// @project intranetv3
// @date 25/11/2019 10:20
// @lastUpdate 23/11/2019 09:14

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\IndisponibilitePersonnelRepository")
 */
class IndisponibilitePersonnel
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\CreneauCours", inversedBy="indisponibilitePersonnels")
     */
    private $creneau;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Calendrier", inversedBy="indisponibilitePersonnels")
     */
    private $semaine;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Personnel", inversedBy="indisponibilitePersonnels")
     */
    private $personnel;

    /**
     * @ORM\Column(type="integer")
     */
    private $priorite;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreneau(): ?CreneauCours
    {
        return $this->creneau;
    }

    public function setCreneau(?CreneauCours $creneau): self
    {
        $this->creneau = $creneau;

        return $this;
    }

    public function getSemaine(): ?Calendrier
    {
        return $this->semaine;
    }

    public function setSemaine(?Calendrier $semaine): self
    {
        $this->semaine = $semaine;

        return $this;
    }

    public function getPersonnel(): ?Personnel
    {
        return $this->personnel;
    }

    public function setPersonnel(?Personnel $personnel): self
    {
        $this->personnel = $personnel;

        return $this;
    }

    public function getPriorite(): ?int
    {
        return $this->priorite;
    }

    public function setPriorite(int $priorite): self
    {
        $this->priorite = $priorite;

        return $this;
    }
}
