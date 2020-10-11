<?php

declare(strict_types=1);

namespace Cafe\UserInterface\Web\Form;

use Cafe\UserInterface\Web\Model\OrderItem;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('menuNumber', TextType::class, [
                'disabled' => true,
            ])
            ->add('description', TextType::class, [
                'disabled' => true,
            ])
            ->add('numberToOrder', IntegerType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => OrderItem::class,
        ]);
    }
}