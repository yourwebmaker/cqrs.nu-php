<?php

namespace Cafe\UserInterface\Web\Form;

use Cafe\Application\OpenTabCommand;
use Cafe\UserInterface\Web\StaticData\StaticData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OpenTabType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('tableNumber', IntegerType::class)
            ->add('waiter', ChoiceType::class, [
                'choices' => array_flip(StaticData::getWaitStaff()),
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Open Tab',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => OpenTabCommand::class,
        ]);
    }
}
