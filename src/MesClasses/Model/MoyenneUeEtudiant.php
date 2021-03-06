<?php
// Copyright (C) 11 / 2019 | David annebicque | IUT de Troyes - All Rights Reserved
// @file /Users/davidannebicque/htdocs/intranetv3/src/MesClasses/Model/MoyenneUeEtudiant.php
// @author     David Annebicque
// @project intranetv3
// @date 25/11/2019 10:20
// @lastUpdate 23/11/2019 09:14

/**
 * Created by PhpStorm.
 * User: davidannebicque
 * Date: 24/12/2018
 * Time: 15:51
 */

namespace App\MesClasses\Model;


use App\Entity\Etudiant;
use App\Entity\Matiere;
use App\Entity\Ue;

class MoyenneUeEtudiant
{
    /** @var Ue */
    private $ue;

    /** @var Etudiant */
    private $etudiant;

    /** @var double */
    private $moyenne = -0.01;

    /** @var double */
    private $moyennePenalisee = -0.01;

    private $matieres;

    /** @var MoyenneMatiereEtudiant[] */
    private $moyenneMatieres;

    /**
     * MoyenneUeEtudiant constructor.
     *
     * @param Etudiant                 $etudiant
     * @param Ue                       $ue
     * @param array                    $matieres
     * @param MoyenneMatiereEtudiant[] $moyenneMatieres
     */
    public function __construct(Etudiant $etudiant, Ue $ue, array $matieres, array $moyenneMatieres)
    {
        $this->ue = $ue;
        $this->etudiant = $etudiant;
        $this->matieres = $matieres;
        $this->moyenneMatieres = $moyenneMatieres;

        $this->calcul();
    }

    private function calcul(): void
    {
        $totue = 0;
        $totueP = 0;
        $totcoeff = 0;

        /** @var Matiere $matiere */
        foreach ($this->matieres as $matiere) {
            if ($matiere->getUe() !== null &&
                $matiere->isPac() === false &&
                $this->moyenneMatieres[$matiere->getId()]->isPasOption() === false &&
                $matiere->getUe()->getId() === $this->ue->getId()) {
                $totue += $this->moyenneMatieres[$matiere->getId()]->getMoyenne() * $matiere->getCoefficient();
                /* penalite */
                $totueP += $this->moyenneMatieres[$matiere->getId()]->getMoyennePenalisee() * $matiere->getCoefficient();
                /*fin penalite */
                $totcoeff += $matiere->getCoefficient();
            }
        }

        /** @var UE $ue */
        $totcoeff !== 0 ? $this->moyenne = $totue / $totcoeff : $this->moyenne = 0;
        $totcoeff !== 0 ? $this->moyennePenalisee = $totueP / $totcoeff : $this->moyennePenalisee = 0;
    }

    public function isPoleFaible(): bool
    {
        if ($this->ue->getSemestre() !== null && $this->ue->getSemestre()->isOptPenaliteAbsence()) {
            if ($this->moyennePenalisee < 8) {
                return true;
            }
        } else if ($this->moyenne < 8) {
            return true;
        }

        return false;
    }

    /**
     * @return float
     */
    public function getMoyenne(): float
    {
        return $this->moyenne;
    }

    /**
     * @return float
     */
    public function getMoyennePenalisee(): float
    {
        return $this->moyennePenalisee;
    }

    /**
     * @return mixed
     */
    public function getStyleMoyenne()
    {

        if ($this->moyenne <= 8) {
            return 'badge badge-danger';
        }

        if ($this->moyenne <= 10) {
            return 'badge badge-warning';
        }

        return '';
    }

    /**
     * @return mixed
     */
    public function getStyleMoyennePenalisee()
    {

        if ($this->moyennePenalisee <= 8) {
            return 'badge badge-danger';
        }

        if ($this->moyenne <= 10) {
            return 'badge badge-warning';
        }

        return '';
    }


}
