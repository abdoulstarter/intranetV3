<?php
// Copyright (C) 11 / 2019 | David annebicque | IUT de Troyes - All Rights Reserved
// @file /Users/davidannebicque/htdocs/intranetv3/src/Controller/superAdministration/DiplomeController.php
// @author     David Annebicque
// @project intranetv3
// @date 25/11/2019 10:20
// @lastUpdate 23/11/2019 09:14

namespace App\Controller\superAdministration;

use App\Controller\BaseController;
use App\Entity\Constantes;
use App\Entity\Departement;
use App\Entity\Diplome;
use App\Form\DiplomeType;
use Exception;
use RuntimeException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/super-administration/structure/diplome")
 */
class DiplomeController extends BaseController
{
    /**
     * @Route("/new/{departement}", name="sa_diplome_new", methods="GET|POST")
     * @param Request     $request
     *
     * @param Departement $departement
     *
     * @return Response
     */
    public function create(Request $request, Departement $departement): Response
    {
        $diplome = new Diplome($departement);
        $form = $this->createForm(DiplomeType::class, $diplome, [
            'attr' => [
                'data-provide' => 'validation'
            ]
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($diplome);
            $this->entityManager->flush();
            $this->addFlashBag(Constantes::FLASHBAG_SUCCESS, 'diplome.add.success.flash');

            return $this->redirectToRoute('sa_structure_index', ['departement' => $departement->getId()]);
        }

        return $this->render('structure/diplome/new.html.twig', [
            'diplome'     => $diplome,
            'departement' => $departement,
            'form'        => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="sa_diplome_show", methods="GET")
     * @param Diplome $diplome
     *
     * @return Response
     */
    public function show(Diplome $diplome): Response
    {
        return $this->render('structure/diplome/show.html.twig', ['diplome' => $diplome]);
    }

    /**
     * @param Request $request
     * @param Diplome $diplome
     * @Route("/{id}/edit", name="sa_diplome_edit", methods="GET|POST")
     *
     * @return Response
     * @throws Exception
     */
    public function edit(Request $request, Diplome $diplome): Response
    {
        if ($diplome->getDepartement() !== null) {
            $form = $this->createForm(DiplomeType::class, $diplome, [
                'attr' => [
                    'data-provide' => 'validation'
                ]
            ]);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $this->entityManager->flush();
                $this->addFlashBag(Constantes::FLASHBAG_SUCCESS, 'diplome.edit.success.flash');

                return $this->redirectToRoute('sa_structure_index',
                    ['departement' => $diplome->getDepartement()->getId()]);
            }

            return $this->render('structure/diplome/edit.html.twig', [
                'diplome' => $diplome,
                'form'    => $form->createView(),
            ]);
        }

        throw new RuntimeException('Le diplôme n\'est pas attaché à un département');
    }

    /**
     * @Route("/{id}/duplicate", name="sa_diplome_duplicate", methods="GET|POST")
     * @param Diplome $diplome
     *
     * @return Response
     */
    public function duplicate(Diplome $diplome): Response
    {
        $newDiplome = clone $diplome;

        $this->entityManager->persist($newDiplome);
        $this->entityManager->flush();
        $this->addFlashBag(Constantes::FLASHBAG_SUCCESS, 'diplome.duplicate.success.flash');

        return $this->redirectToRoute('sa_diplome_edit', ['id' => $newDiplome->getId()]);
    }

    /**
     * @Route("/{id}", name="sa_diplome_delete", methods="DELETE")
     * @param Request $request
     * @param Diplome $diplome
     *
     * @return Response
     */
    public function delete(Request $request, Diplome $diplome): Response
    {
        $id = $diplome->getId();
        if ($this->isCsrfTokenValid('delete' . $id, $request->request->get('_token'))) {
            if (count($diplome->getAnnees()) === 0) {
                $this->entityManager->remove($diplome);
                $this->entityManager->flush();
                $this->addFlashBag(
                    Constantes::FLASHBAG_SUCCESS,
                    'diplome.delete.success.flash'
                );

                return $this->json($id, Response::HTTP_OK);
            }

            $this->addFlashBag(Constantes::FLASHBAG_ERROR, 'diplome.delete.error.non-vide.flash');

            return $this->json(false, Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $this->addFlashBag(Constantes::FLASHBAG_ERROR, 'diplome.delete.error.flash');

        return $this->json(false, Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * @param Diplome $diplome
     * @param bool    $etat
     *
     * @return RedirectResponse
     * @Route("/activate/{diplome}/{etat}", methods={"GET"}, name="sa_diplome_activate")
     * @IsGranted("ROLE_SUPER_ADMIN")
     *
     */
    public function activate(Diplome $diplome, bool $etat): RedirectResponse
    {
        $diplome->setActif($etat);
        $this->entityManager->flush();
        $this->addFlashBag(Constantes::FLASHBAG_SUCCESS, 'diplome.activate.' . $etat . '.flash');

        return $this->redirectToRoute('super_admin_homepage');

    }
}
