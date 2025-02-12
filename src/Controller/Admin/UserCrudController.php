<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;

class UserCrudController extends AbstractCrudController
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function persistEntity($entityManager, $entity): void
    {
        if ($entity instanceof User) {
            if ($entity->getPassword()) {
                $hashedPassword = $this->passwordHasher->hashPassword($entity, $entity->getPassword());
                $entity->setPassword($hashedPassword);
            }
        }

        parent::persistEntity($entityManager, $entity);
    }

    public function updateEntity($entityManager, $entity): void
    {
        if ($entity instanceof User) {
            if ($entity->getPassword()) {
                $hashedPassword = $this->passwordHasher->hashPassword($entity, $entity->getPassword());
                $entity->setPassword($hashedPassword);
            }
        }

        parent::updateEntity($entityManager, $entity);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('email')->setLabel('Email'),
            TextField::new('password')->setLabel('Mot de passe')->onlyOnForms(),
            ChoiceField::new('roles')
                ->setChoices([
                    'Utilisateur' => 'ROLE_USER',
                    'Administrateur' => 'ROLE_ADMIN'
                ])
                ->allowMultipleChoices()
                ->setLabel('Rôles')
        ];
    }
}
