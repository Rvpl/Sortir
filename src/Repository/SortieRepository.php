<?php

namespace App\Repository;

use App\Entity\Etat;
use App\Entity\Participant;
use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Sortie $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Sortie $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }
    public function recherche(Sortie $sortie){
        $qb = $this->createQueryBuilder('s')
            ->where('s.nom LIKE :nom')
            ->andWhere('s.dateHeureDebut = :dateDebut')
            ->andWhere('s.dateLimiteInscription = :dateLim')
            ->andWhere('s.campus = :campus')
            ->andWhere('DATE_DIFF(CURRENT_DATE(),s.dateLimiteInscription) > 30')
            ->setParameter('nom','%'.$sortie->getNom().'%')
            ->setParameter('dateDebut',$sortie->getDateHeureDebut())
            ->setParameter('dateLim',$sortie->getDateLimiteInscription())
            ->setParameter('campus',$sortie->getCampus()->getId());
            return $qb->getQuery()
                ->getResult();
    }

    public function rechercheOrganisateur(Sortie $sortie,Participant $user){
        $qb = $this->createQueryBuilder('s')
            ->where('s.nom LIKE :nom')
            ->andWhere('s.dateHeureDebut = :dateDebut')
            ->andWhere('s.dateLimiteInscription = :dateLim')
            ->andWhere('s.campus = :campus')
            ->andWhere('s.organisateur = :orga')
            ->andWhere('DATE_DIFF(CURRENT_DATE(),s.dateLimiteInscription) < 30')
            ->setParameter('nom','%'.$sortie->getNom().'%')
            ->setParameter('dateDebut',$sortie->getDateHeureDebut())
            ->setParameter('dateLim',$sortie->getDateLimiteInscription())
            ->setParameter('campus',$sortie->getCampus()->getId())
            ->setParameter('orga',$user->getId());
        return $qb->getQuery()
            ->getResult();
    }

    public function modifEtat(Sortie $sortie,bool $flush = true){
        $sortie->setEtat($sortie->getEtat());
        if($flush){
            $this->_em->flush();
        }
    }

    public function ajoutInscrit(Sortie $sortie, Participant $user,Etat $etat,bool $flush = true){
        $sortie->addInscrit($user);
        if($sortie->getInscrits()->count() == $sortie->getNbInscriptionMax()){
            $sortie->setEtat($etat);
        }
        if($flush){
            $this->_em->flush();
        }
    }

    public function removeInscrit(Sortie $sortie, Participant $user,Etat $etat,bool $flush = true)
    {
        if($sortie->getInscrits()->count() == $sortie->getNbInscriptionMax()){
            $sortie->setEtat($etat);
        }
        $sortie->removeInscrit($user);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function modifEtatAnn(Sortie $sortieAnn,Etat $etat,bool $flush = true){
      $sortieAnn->setEtat($etat);
      if($flush){
          $this->_em->flush();
      }
    }

    public function findAllFiltre(){
        $qb = $this->createQueryBuilder('s')
            ->where('DATE_DIFF(CURRENT_DATE(),s.dateLimiteInscription) < 30');
        return $qb->getQuery()
            ->getResult();
    }


    // /**
    //  * @return Sortie[] Returns an array of Sortie objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Sortie
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
