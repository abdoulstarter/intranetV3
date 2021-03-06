<?php
// Copyright (C) 11 / 2019 | David annebicque | IUT de Troyes - All Rights Reserved
// @file /Users/davidannebicque/htdocs/intranetv3/src/Controller/administration/CohorteController.php
// @author     David Annebicque
// @project intranetv3
// @date 25/11/2019 10:20
// @lastUpdate 23/11/2019 09:14

namespace App\Controller\administration;

use App\Controller\BaseController;
use App\Entity\Scolarite;
use App\Repository\EtudiantRepository;
use App\Repository\ScolariteRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CohorteController
 * @package App\Controller\administration
 * @Route("/administration/cohorte")
 */
class CohorteController extends BaseController
{
    /**
     * @Route("/{annee}", name="administration_cohorte_index")
     * @param EtudiantRepository  $etudiantRepository
     * @param ScolariteRepository $scolariteRepository
     * @param int                 $annee
     *
     * @return Response
     */
    public function index(
        EtudiantRepository $etudiantRepository,
        ScolariteRepository $scolariteRepository,
        $annee = 0
    ): Response {
        if ($annee === 0) {
            $annee = date('Y') - 1;
        }
        //on ne récupère la cohorte que de la departement.
        //$etudiants = $etudiantRepository->findEtudiantCohorte($annee, $this->dataUserSession->getDepartement());
        $parcours = $scolariteRepository->findEtudiantsDepartement($annee, $this->dataUserSession->getDepartement());
        $etudiants = [];

        /** @var Scolarite $parcour */
        foreach ($parcours as $parcour) {
            if ($parcour->getEtudiant() !== null && !array_key_exists($parcour->getEtudiant()->getId(), $etudiants)) {
                $etudiants[$parcour->getEtudiant()->getId()] = $parcour->getEtudiant();
            }
        }

        return $this->render('administration/cohorte/index.html.twig', [
            'parcours'  => $parcours,
            'etudiants' => $etudiants,
            'annee'     => $annee
        ]);
    }

    /**
     * @Route("/export.{_format}", name="administration_cohorte_export", requirements={"_format"="csv|xlsx|pdf"})
     */
    public function export(): void
    {
    }
}
