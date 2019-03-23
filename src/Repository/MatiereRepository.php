<?php

namespace App\Repository;

use App\Entity\Annee;
use App\Entity\Diplome;
use App\Entity\Departement;
use App\Entity\Matiere;
use App\Entity\Personnel;
use App\Entity\Semestre;
use App\Entity\Ue;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Matiere|null find($id, $lockMode = null, $lockVersion = null)
 * @method Matiere|null findOneBy(array $criteria, array $orderBy = null)
 * @method Matiere[]    findAll()
 * @method Matiere[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MatiereRepository extends ServiceEntityRepository
{
    /**
     * MatiereRepository constructor.
     *
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Matiere::class);
    }

    /**
     * @param Semestre $semestre
     *
     * @return mixed
     */
    public function findBySemestre(Semestre $semestre)
    {
        return $this->findBySemestreBuilder($semestre)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param $departement
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function findByDepartementBuilder(Departement $departement): \Doctrine\ORM\QueryBuilder
    {
        return $this->createQueryBuilder('m')
            ->innerJoin(Ue::class, 'u', 'WITH', 'u.id = m.ue')
            ->innerJoin(Semestre::class, 's', 'WITH', 's.id = u.semestre')
            ->innerJoin(Annee::class, 'a', 'WITH', 'a.id = s.annee')
            ->innerJoin(Diplome::class, 'd', 'WITH', 'd.id = a.diplome')
            ->where('d.departement = :departement')
            ->setParameter('departement', $departement->getId())
            ->orderBy('m.codeMatiere', 'ASC')
            ->orderBy('m.libelle', 'ASC');
    }

    /**
     * @param Semestre $semestre
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function findBySemestreBuilder(Semestre $semestre): \Doctrine\ORM\QueryBuilder
    {
        return $this->createQueryBuilder('m')
            ->innerJoin(Ue::class, 'u', 'WITH', 'u.id = m.ue')
            ->where('u.semestre = :semestre')
            ->setParameter('semestre', $semestre->getId())
            ->orderBy('m.ue', 'ASC')
            ->orderBy('m.codeMatiere', 'ASC');
    }

    public function findByDepartement(Departement $departement)
    {
        return $this->findByDepartementBuilder($departement)->getQuery()->getResult();
    }

    /**
     * @param Diplome $diplome
     *
     * @return array
     */
    public function tableauMatieresApogees(Diplome $diplome): array
    {
        $query = $this->createQueryBuilder('m')
            ->innerJoin(Ue::class, 'u', 'with', 'u.id=m.UE')
            ->innerJoin(Semestre::class, 's', 'with', 's.id=u.semestre')
            ->innerJoin(Annee::class, 'a', 'with', 'a.id=s.annee')
            ->where('a.diplome= :diplome')
            ->setParameter('diplome', $diplome->getId())
            ->getQuery()
            ->getResult();

        $t = [];

        /** @var  $q Matiere */
        foreach ($query as $q) {
            $t[$q->getCodeApogee()] = $q;
        }

        return $t;
    }

}
