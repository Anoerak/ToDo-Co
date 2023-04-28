<?php

namespace App\Form;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('content')
            ->add('author', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'username',
                'label' => 'Auteur',
                'placeholder' => 'Choisir un auteur',
                'required' => false,
            ]);
    }
}
