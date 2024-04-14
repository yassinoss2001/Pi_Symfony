<?php

namespace App\Repository;

use App\Entity\Restaurant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Twilio\Rest\Client;
/**
 * @extends ServiceEntityRepository<Restaurant>
 *
 * @method Restaurant|null find($id, $lockMode = null, $lockVersion = null)
 * @method Restaurant|null findOneBy(array $criteria, array $orderBy = null)
 * @method Restaurant[]    findAll()
 * @method Restaurant[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RestaurantRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Restaurant::class);
    }
    // Méthode de recherche par requête
    public function searchByQuery(string $query): array
    {
        return $this->createQueryBuilder('r')
            ->where('r.nom LIKE :query')
            ->setParameter('query', '%'.$query.'%')
            ->getQuery()
            ->getResult();
    }

    public function save(Restaurant $restaurant, $flush = true)
    {
        $this->_em->persist($restaurant);
        if ($flush) {
            $this->_em->flush();
        }
    } 



    public function findByPlace(string $place): array
{
    return $this->createQueryBuilder('r')
        ->where('r.place = :place')
        ->setParameter('place', $place)
        ->getQuery()
        ->getResult();
}
    public  function sms()
    {
        // Your Account SID and Auth Token from twilio.com/console
        $sid = 'ACc530ea679fa136df8d9d6295e473504d';
        $auth_token = 'fb360275c4c55b287929e890754b9125';
        // In production, these should be environment variables. E.g.:
        // $auth_token = $_ENV["TWILIO_AUTH_TOKEN"]
        // A Twilio number you own with SMS capabilities
        $twilio_number = "+19124552157";

        $client = new Client($sid, $auth_token);
        $client->messages->create(
            // the number you'd like to send the message to
            '+21654512887',
            [
                // A Twilio phone number you purchased at twilio.com/console
                'from' => '+19124552157',
                // the body of the text message you'd like to send
                'body' => 'votre RESTAURANT a été ajouter , merci de nous contacter pour plus de détails!'
            ]
        );
    }

//    /**
//     * @return Restaurant[] Returns an array of Restaurant objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Restaurant
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
