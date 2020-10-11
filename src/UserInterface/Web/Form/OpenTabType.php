<?php

declare(strict_types=1);

namespace Cafe\UserInterface\Web\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class OpenTabType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('tableNumber', IntegerType::class)
            ->add('waiter', ChoiceType::class, [
                'choices' => StaticData::getWaitStaff(),
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Open Tab',
            ])
        ;
    }
}
