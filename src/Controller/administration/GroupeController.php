<?php
// Copyright (C) 11 / 2019 | David annebicque | IUT de Troyes - All Rights Reserved
// @file /Users/davidannebicque/htdocs/intranetv3/src/Controller/administration/GroupeController.php
// @author     David Annebicque
// @project intranetv3
// @date 25/11/2019 10:20
// @lastUpdate 23/11/2019 09:14

namespace App\Controller\administration;

use App\Controller\BaseController;
use App\Entity\Constantes;
use App\Entity\Groupe;
use App\Entity\Semestre;
use App\MesClasses\MyExport;
use App\Repository\GroupeRepository;
use App\Repository\ParcourRepository;
use App\Repository\TypeGroupeRepository;
use PhpOffice\PhpSpreadsheet\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class GroupeController
 * @package App\Controller\administration
 * @Route("/administration/groupe")
 */
class GroupeController extends BaseController
{
    /**
     * @Route("/", name="administration_groupe_index")
     * @param GroupeRepository $groupeRepository
     *
     * @return Response
     */
    public function index(GroupeRepository $groupeRepository): Response
    {
        $groupes = $groupeRepository->findByDepartement($this->dataUserSession->getDepartement());

        return $this->render('administration/groupe/index.html.twig', [
            'groupes' => $groupes
        ]);
    }

    /**
     * @Route("/refresh/{parent}", name="administration_groupe_refresh", methods="GET", options={"expose"=true})
     * @param GroupeRepository $groupeRepository
     * @param Groupe           $parent
     *
     * @return Response
     */
    public function refreshListe(GroupeRepository $groupeRepository, Groupe $parent): Response
    {
        if ($parent->getTypeGroupe() !== null) {
            $semestre = $parent->getTypeGroupe()->getSemestre();
            if ($semestre !== null) {
                $groupes = $groupeRepository->findBySemestre($semestre);

                return $this->render('administration/groupe/_liste.html.twig', [
                    'groupes'  => $groupes,
                    'semestre' => $semestre
                ]);
            }

            return $this->render('bundles/TwigBundle/Exception/error500.html.twig');
        }

        return $this->render('bundles/TwigBundle/Exception/error500.html.twig');
    }

    /**
     * @Route("/new", name="administration_groupe_new", methods="POST", options={"expose"=true})
     * @param TypeGroupeRepository $typeGroupeRepository
     * @param ParcourRepository    $parcourRepository
     * @param GroupeRepository     $groupeRepository
     * @param Request              $request
     *
     *
     * @return Response
     */
    public function create(
        TypeGroupeRepository $typeGroupeRepository,
        ParcourRepository $parcourRepository,
        GroupeRepository $groupeRepository,
        Request $request
    ): Response {
        $typeGroupe = $typeGroupeRepository->find($request->request->get('type'));
        if ($typeGroupe) {
            $groupe = new Groupe($typeGroupe);
            if (!empty($request->request->get('parent'))) {
                $parent = $groupeRepository->find($request->request->get('parent'));
                if ($parent) {
                    $groupe->setParent($parent);
                }
            }
            if (!empty($request->request->get('groupe_parcours_2'))) {
                $parcour = $parcourRepository->find($request->request->get('groupe_parcours'));
                if ($parcour) {
                    $groupe->setParcours($parcour);
                }
            }
            $groupe->setOrdre($request->request->get('ordre'));
            $groupe->setLibelle($request->request->get('libelle'));
            $groupe->setCodeApogee($request->request->get('code'));

            $this->entityManager->persist($groupe);
            $this->entityManager->flush();

            return $this->json(true, Response::HTTP_OK);
        }

        return $this->json(false, Response::HTTP_INTERNAL_SERVER_ERROR);

    }

    /**
     * @Route("/{id}", name="administration_groupe_show", methods="GET")
     * @param Groupe $groupe
     *
     * @return Response
     */
    public function show(Groupe $groupe): Response
    {
        return $this->render('administration/groupe/show.html.twig', ['groupe' => $groupe]);
    }

    /**
     * @Route("/{semestre}/export.{_format}", name="administration_groupe_export", methods="GET",
     *                                        requirements={"_format"="csv|xlsx|pdf"})
     * @param MyExport         $myExport
     * @param GroupeRepository $groupeRepository
     * @param                  $_format
     *
     * @param Semestre         $semestre
     *
     * @return Response
     * @throws Exception
     */
    public function export(
        MyExport $myExport,
        GroupeRepository $groupeRepository,
        $_format,
        Semestre $semestre
    ): Response {
        $groupes = $groupeRepository->findBySemestre($semestre);
        return $myExport->genereFichierGenerique(
            $_format,
            $groupes,
            'groupes',
            ['type_groupe_administration', 'groupe_administration', 'semestre'],
            [
                'libelle',
                'codeApogee',
                'parent'     => ['libelle'],
                'typeGroupe' => ['libelle', 'type', 'codeApogee', 'semestre' => ['libelle']]
            ]
        );
    }

    /**
     * @Route("/{id}/duplicate", name="administration_groupe_duplicate", methods="GET|POST")
     * @param Groupe $groupe
     *
     * @return Response
     */
    public function duplicate(Groupe $groupe): Response
    {
        $newGroupe = clone $groupe;

        $this->entityManager->persist($newGroupe);
        $this->entityManager->flush();
        $this->addFlashBag(Constantes::FLASHBAG_SUCCESS, 'groupe.duplicate.success.flash');

        return $this->redirectToRoute('administration_groupe_edit', ['id' => $newGroupe->getId()]);
    }

    /**
     * @Route("/{id}", name="administration_groupe_delete", methods="DELETE")
     * @param Request $request
     * @param Groupe  $groupe
     *
     * @return Response
     */
    public function delete(Request $request, Groupe $groupe): Response
    {

        $id = $groupe->getId();
        if ($this->isCsrfTokenValid('delete' . $id, $request->request->get('_token'))) {
            $this->entityManager->remove($groupe);
            $this->entityManager->flush();
            $this->addFlashBag(Constantes::FLASHBAG_SUCCESS, 'groupe.delete.success.flash');

            return $this->json($id, Response::HTTP_OK);
        }

        $this->addFlashBag(Constantes::FLASHBAG_SUCCESS, 'groupe.delete.error.flash');

        return $this->json(false, Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
