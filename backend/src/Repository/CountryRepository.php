<?php

namespace App\Repository;

use App\Entity\Country;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Country|null find($id, $lockMode = null, $lockVersion = null)
 * @method Country|null findOneBy(array $criteria, array $orderBy = null)
 * @method Country[]    findAll()
 * @method Country[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CountryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Country::class);
    }

    /**
     * @param string $countryName
     * @param bool $flush
     * @return Country
     * @throws \Doctrine\ORM\ORMException
     */
    public function createCountryIfDoesntExist(string $countryName, bool $flush = true): Country
    {
        $country = $this->findOneBy(['name' => $countryName]);
        if(!$country){
            $country = new Country();
            $country->setName($countryName);
            $this->_em->persist($country);
            if($flush){
                $this->_em->flush();
            }
        }

        return $country;
    }

}
