<?php
// Copyright (C) 11 / 2019 | David annebicque | IUT de Troyes - All Rights Reserved
// @file /Users/davidannebicque/htdocs/intranetv3/src/MesClasses/Edt/MyEdtExport.php
// @author     David Annebicque
// @project intranetv3
// @date 25/11/2019 10:20
// @lastUpdate 23/11/2019 09:14

namespace App\MesClasses\Edt;


use App\Entity\Etudiant;
use App\MesClasses\MyIcal;
use App\Repository\CalendrierRepository;
use App\Repository\CelcatEventsRepository;
use App\Repository\EdtPlanningRepository;

class MyEdtExport
{
    /** @var EdtPlanningRepository */
    protected $edtPlanningRepository;

    /** @var CelcatEventsRepository */
    protected $celcatEventsRepository;

    /** @var CalendrierRepository */
    protected $calendrierRepository;

    /** @var MyIcal */
    protected $myIcal;

    private $calendrier;

    /**
     * MyEdtExport constructor.
     *
     * @param EdtPlanningRepository  $edtPlanningRepository
     * @param CelcatEventsRepository $celcatEventsRepository
     * @param CalendrierRepository   $calendrierRepository
     * @param MyIcal                 $myIcal
     */
    public function __construct(
        EdtPlanningRepository $edtPlanningRepository,
        CelcatEventsRepository $celcatEventsRepository,
        CalendrierRepository $calendrierRepository,
        MyIcal $myIcal
    ) {
        $this->edtPlanningRepository = $edtPlanningRepository;
        $this->celcatEventsRepository = $celcatEventsRepository;
        $this->calendrierRepository = $calendrierRepository;
        $this->myIcal = $myIcal;
    }


    public function export($user, $_format, $type)
    {
        $this->calendrier = $this->calendrierRepository->findCalendrierArray();
        $edt = [];
        if ($type === 'Personnel') {
            $temp = $this->edtPlanningRepository->getByPersonnelArray($user);
            foreach ($temp as $row) {
                $edt[] = $row;
            }
            $temp = $this->celcatEventsRepository->getByPersonnelArray($user);
            foreach ($temp as $row) {
                $edt[] = $row;
            }
        } else {
            /** @var Etudiant $user */
            $nbSemaines = $user->getSemestre()->getAnnee()->getDiplome()->getOptSemainesVisibles();
            $emaineActuelle = $this->calendrierRepository->findOneBy(['semaineReelle'      => date('W'),
                                                                      'anneeUniversitaire' => $user->getAnneeUniversitaire()->getId()
            ]);
            $max = $emaineActuelle + $nbSemaines;

            if ($user->getDepartement()->isOptUpdateCelcat()) {
                for ($i = date('W'); $i < $max; $i++) {
                    $edt = $this->celcatEventsRepository->getByEtudiantArray($user, $i);
                }
            } else {
                for ($i = date('W'); $i < $max; $i++) {
                    $edt = $this->edtPlanningRepository->getByEtudiantArray($user, $nbSemaines);

                }
            }
        }

        switch ($_format) {
            case 'ics':
                return $this->genereIcal($edt);
                break;
        }
    }

    private function genereIcal($edt)
    {
        foreach ($edt as $pl) {
            //todo: surement possible d'exploiter la date depuis EdtPlanning...
            $this->myIcal->setDtstart($this->calendrier[$pl['semaine']]['lundi'], $pl['jour'], $pl['debut']);
            $this->myIcal->setDtend($this->calendrier[$pl['semaine']]['lundi'], $pl['jour'], $pl['fin']);
            $this->myIcal->setDescription($pl['commentaire']);
            $this->myIcal->setSummary($pl['ical']);
            $this->myIcal->setLocation($pl['salle']);
            $this->myIcal->addEvent();
        }

        $handle = fopen('php://memory', 'rb+');
        fwrite($handle, $this->myIcal->getIcal());

        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        return $content;
    }
}
