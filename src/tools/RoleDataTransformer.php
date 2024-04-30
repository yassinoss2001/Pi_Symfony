<?php
namespace App\tools;

use Symfony\Component\Form\DataTransformerInterface;
use App\Enums\Role;

class RoleDataTransformer implements DataTransformerInterface
{
    public function transform($value): ?string
    {
        return $value ? $value->getValue() : null;
    }

    public function reverseTransform($value): ?Role
{
    // Vérifiez si la valeur est déjà une instance de Role
    if ($value instanceof Role) {
        return $value;
    }

    // Retournez l'instance de Role en fonction de la valeur
    switch ($value) {
        case Role::GERANT:
            return new Role(Role::GERANT);
        case Role::LIVREUR:
            return new Role(Role::LIVREUR);
        case Role::CLIENT:
            return new Role(Role::CLIENT);
        default:
            return null;
    }
}
}
