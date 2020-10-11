<?php

declare(strict_types=1);

namespace Cafe\UserInterface\Web\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class CloseTabType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('amountPaid', MoneyType::class)
            ->add('save', SubmitType::class, [
                'label' => 'Close Tab',
            ])
        ;
    }
}
