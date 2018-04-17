<?php

namespace AppBundle\Form;

use AppBundle\Entity\Detail;
use AppBundle\Entity\Employee;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Entity\Project;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class DetailProjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('employee', EntityType::class, array(
            'label' => 'Employé',
            'mapped' => true,
            'class' => Employee::class,
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('e')
                    ->where('e.active = true');
            },
            'choice_label' => function($employee){
                return $employee->getName().' '.$employee->getSurname();
            }
        ));

        $builder->add('duration', TextType::class, array('label' => 'Nombre de jours'));
        $builder->add('save', SubmitType::class, array('label' => 'Enregistrer'));

    }

    public function getName()
    {
        return 'detail_form';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Detail::class,
        ));
    }

}