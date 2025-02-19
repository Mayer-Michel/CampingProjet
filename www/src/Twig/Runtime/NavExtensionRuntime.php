<?php

namespace App\Twig\Runtime;

use App\Repository\HebergementRepository;
use Twig\Extension\RuntimeExtensionInterface;

class NavExtensionRuntime implements RuntimeExtensionInterface
{
    //on va déclarer une variable en private pour stocker l'instance de gameRepository
    private $hebergementRepository;

    public function __construct(HebergementRepository $hebergementRepository)
    {
       $this->hebergementRepository = $hebergementRepository;
    }

    public function badgeUser($roles)
    {
        foreach ($roles as $role) {
            switch ($role) {
                case 'ROLE_ADMIN':
                    return '<span class="badge text-bg-warning">Admin</span>';
                case 'ROLE_USER':
                    return '<span class="badge text-bg-primary">Client</span>';
               default:
                    return '<span class="badge text-bg-secondary">Inconnu</span>';
            }
        }
    }

}
