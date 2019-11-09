<?php
/**
 * Copyright (C) 11 / 2019 | David annebicque | IUT de Troyes - All Rights Reserved
 * @file /Users/davidannebicque/htdocs/intranetv3/src/Form/QuizzQuestionType.php
 * @author     David Annebicque
 * @project intranetv3
 * @date 09/11/2019 10:16
 * @lastUpdate 09/11/2019 10:12
 */

namespace App\Form;

use App\Entity\QuizzQuestion;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuizzQuestionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('libelle', TextType::class, ['label' => 'label.libelle'])
            ->add('help', TextType::class, ['label' => 'label.question.help'])
            ->add('type', ChoiceType::class,
                ['expanded' => true, 'multiple' => false, 'choices' => QuizzQuestion::LISTE_TYPE_QUESTION]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => QuizzQuestion::class,
        ]);
    }
}