<?php

namespace App\DataFixtures;

use App\Entity\Equipement;
use App\Entity\Hebergement;
use App\Entity\Image;
use App\Entity\Rental;
use App\Entity\Saison;
use App\Entity\Tarif;
use App\Entity\Type;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    //propriété pour encoder le mdp 
    private $encoder;

    public function __construct(UserPasswordHasherInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager): void
    {
        $this->loadUsers($manager);
        $this->loadTypes($manager);
        $this->loadEquipements($manager);
        $this->loadSaisons($manager);
        $this->loadHebergements($manager);
        $this->loadTarifs($manager);
        $this->loadRentals($manager);

        $manager->flush();
    }

    /**
     * méthode pour générer des utilisateurs
     * @param ObjectManager $manager
     * @return void
     */
    public function loadUsers(ObjectManager $manager): void
    {
        //on crée un tableau avec les infos des users
        $array_user = [
            [
                'email' => 'admin@admin.com', 
                'password'=>'admin',
                'roles' => ['ROLE_ADMIN'],
                'username' => 'administrateur'
            ],
            [
                'email' => 'user@user.com',
                'password' => 'user',
                'roles' => ['ROLE_USER'],
                'username' => 'utilisateur'
            ]
        ];

        //on va boucler sur le tableau pour créer les users
        foreach($array_user as $key => $value)
        {
            //on instancie un user
            $user = new User(); 
            $user->setEmail($value['email']);
            $user->setPassword($this->encoder->hashPassword($user, $value['password']));
            $user->setRoles($value['roles']);
            $user->setUsername($value['username']);
            //on persiste les données
            $manager->persist($user);
            //on définit une référence pour pouvoir faire nos relations
            $this->addReference('user_'.$key + 1, $user);
        }
    }

    /**
     * méthode pour générer les types
     * @param ObjectManager $manager
     * @return void
     */
    public function loadTypes(ObjectManager $manager): void
    {
        $array_types = ['Mobil-home','Tente meublée', 'Emplacement nus'];

        //on boucle sur le tableau pour créer les types
        foreach($array_types as $key => $value){
            //on instancie une type
            $type = new Type();
            $type->setLabel($value);
            //on persiste les données
            $manager->persist($type);
            //on définit une référence pour pouvoir faire nos relations
            $this->addReference('type_'.$key + 1, $type);
        }
    }

    /**
     * méthode pour générer les equipements
     * @param ObjectManager $manager
     * @return void
     */
    public function loadEquipements(ObjectManager $manager): void
    {
        $array_equipements = [
            "Blocs sanitaires (WC, douches, lavabos)",
            "Douches chaudes",
            "Toilettes adaptées PMR",
            "Laverie (machines à laver, sèche-linge)",
            "Éviers pour la vaisselle",
            "Aire de barbecue",
            "Restaurant/snack-bar",
            "Épicerie ou supérette",
            "Cuisine collective",
            "Micro-ondes et plaques de cuisson en commun",
            "Piscine (chauffée ou non)",
            "Toboggans aquatiques",
            "Terrain de pétanque",
            "Aires de jeux pour enfants",
            "Salle de jeux (baby-foot, billard, flipper)",
            "Location de vélos",
            "Mini-golf",
            "Terrain de sport (foot, basket, volley)",
            "Animations et spectacles",
            "Connexion Wi-Fi",
            "Électricité sur les emplacements",
            "Point de recharge pour véhicules électriques",
            "Location de draps et serviettes",
            "Service de navette",
            "Garde d'enfants / Club enfants",
            "Animaux acceptés"
        ];

        //on boucle sur le tableau pour créer les types
        foreach($array_equipements as $key => $value){
            //on instancie une type
            $equipement = new Equipement();
            $equipement->setLabel($value);
            //on persiste les données
            $manager->persist($equipement);
            //on définit une référence pour pouvoir faire nos relations
            $this->addReference('equipement_'.$key + 1, $equipement);
        }
    }

    /**
     * méthode pour générer les saisons
     * @param ObjectManager $manager
     * @return void
     */
    private function loadSaisons(ObjectManager $manager): void
    {
        // Crée une liste de saisons avec des dates de début et de fin
        $array_saisons = [
            [
                'saison' => 'Basse saison',
                'date_start' => new \DateTime('2025-04-01'),
                'date_end' => new \DateTime('2025-06-30')
            ],
            [
                'saison' => 'Haute saison',
                'date_start' => new \DateTime('2025-07-01'),
                'date_end' => new \DateTime('2025-08-31')
            ],
            [
                'saison' => 'Très haute saison',
                'date_start' => new \DateTime('2025-06-01'),
                'date_end' => new \DateTime('2025-06-30')
            ],
            [
                'saison' => 'Fermeture hivernale',
                'date_start' => new \DateTime('2025-10-01'),
                'date_end' => new \DateTime('2025-03-31')
            ]
        ];

        // Insère chaque saison dans la base de données
        foreach ($array_saisons as $key => $value) {
            $saison = new Saison();
            $saison->setLabel($value['saison']);
            $saison->setDateStart($value['date_start']);
            $saison->setDateEnd($value['date_end']);
            //on persiste les données
            $manager->persist($saison);
            //on définit une référence pour pouvoir faire nos relations
            $this->addReference('saison_'.$key + 1, $saison);
        }
    }


    /**
    * méthode pour générer les hebergements
    * @param ObjectManager $manager
    * @return void
    */
   public function loadHebergements(ObjectManager $manager): void
   {
       // Liste d'hébergements à insérer dans la base
       $array_hebergements = [
           [
               'type' => 1, // Premier type
               'capacite' => '5',
               'surface' => '40',
               'disponibilite' => true,
               'description' => 'Hébergement confortable pour 5 personnes.',
               'image' => 'image-1.jpeg', // Première image
               'equipement' => [13, 15, 7, 8, 4, 6, 10]
           ],
           [
               'type' => 1, // Deuxième type
               'capacite' => '2',
               'surface' => '20',
               'disponibilite' => true,
               'description' => 'Petit studio pour 2 personnes.',
               'image' => 'image-2.jpeg', // Deuxième image
               'equipement' => [3, 5, 7, 8]
           ],
           [
               'type' => 2, // Premier type
               'capacite' => '6',
               'surface' => '60',
               'disponibilite' => false,
               'description' => 'Grande maison pour 6 personnes.',
               'image' => 'image-3.jpeg', // Troisième image
               'equipement' => [3, 5, 7, 8]
           ]
       ];

       // Insertion des hébergements dans la base
       foreach ($array_hebergements as $key => $value) {
           $hebergement = new Hebergement();
           $hebergement->setType($this ->getReference('type_'.$value['type'], Type::class));
           $hebergement->setCapacity($value['capacite']);
           $hebergement->setSurface($value['surface']);
           $hebergement->setDisponibilite($value['disponibilite']);
           $hebergement->setDescription($value['description']);
           $hebergement->setImagePath($value['image']);
           //on va devoir boucler sur $value['equipement'] pour faire les relations du many to many
           foreach($value['equipement'] as $equipement){
            $hebergement->addEquipement($this->getReference('equipement_'.$equipement, Equipement::class));
        }
        //on persiste les données
        $manager->persist($hebergement);
        //on définit une référence pour pouvoir faire nos relations
        $this->addReference('hebergement_'.$key + 1, $hebergement);
       }
   }

    /**
     * méthode pour générer les tarifs
     * @param ObjectManager $manager
     * @return void
     */
    public function loadTarifs(ObjectManager $manager): void
    {
        $array_tarifs = [
            [
                'saisonId' => 1,
                'hebergementId' => 2,
                'prix' => '111'
            ],
            [
                'saisonId' => 2,
                'hebergementId' => 2,
                'prix' => '300'
            ],
            [
                'saisonId' => 3,
                'hebergementId' => 2,
                'prix' => '500'
            ],
            [
                'saisonId' => 4,
                'hebergementId' => 2,
                'prix' => '0'
            ],
        ];
        // Insère chaque tarif dans la base de données
        foreach ($array_tarifs as $key => $value) {
            $tarif = new Tarif();
            $tarif->setSaison($this->getReference('saison_'.$value['saisonId'], Saison::class));
            $tarif->setHebergement($this->getReference('hebergement_'.$value['hebergementId'], Hebergement::class));
            $tarif->setPrix($value['prix']);
            $manager->persist($tarif);
            //on définit une référence pour pouvoir faire nos relations
            $this->addReference('tarif_'.$key + 1, $tarif);
        }
    }

    /**
     * méthode pour générer les tarifs
     * @param ObjectManager $manager
     * @return void
     */
    public function loadRentals(ObjectManager $manager): void
    {
        $array_rentals = [
            [
                'user' => 1,
                'hebergement' => 1,
                'nbrAdults' => '3',
                'nbrKids' => '2',
                'dateStart' => new \DateTime('2025-4-13'),
                'dateEnd' => new \DateTime('2025-4-26')
            ],
            [
                'user' => 1,
                'hebergement' => 2,
                'nbrAdults' => '2',
                'nbrKids' => '0',
                'dateStart' => new \DateTime('2025-6-13'),
                'dateEnd' => new \DateTime('2025-6-26')
            ],
            [
                'user' => 1,
                'hebergement' => 3,
                'nbrAdults' => '4',
                'nbrKids' => '2',
                'dateStart' => new \DateTime('2025-5-13'),
                'dateEnd' => new \DateTime('2025-5-26')
            ]
        ];
         // Insertion des rentals dans la base
        foreach ($array_rentals as $key => $value) {
            $rental = new Rental();
            $rental->setUser($this ->getReference('user_'.$value['user'], User::class));
            $rental->setHebergement($this ->getReference('hebergement_'.$value['hebergement'], Hebergement::class));
            $rental->setNbrAdult($value['nbrAdults']);
            $rental->setNbrChildren($value['nbrKids']);
            $rental->setDateStart($value['dateStart']);
            $rental->setDateEnd($value['dateEnd']);

            $manager->persist($rental);
        }
    }


}