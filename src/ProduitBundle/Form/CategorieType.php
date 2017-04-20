<?php

namespace ProduitBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Doctrine\ORM\EntityManager;

class CategorieType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $em = $options['em'];
        $builder
            ->add('nom');

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) use($em){
            $form = $event->getForm();
            $categorie = $event->getData();
            $addParent = true;
            $id = null;

            if ($categorie && $categorie->getId() !== null) {
                $id = $categorie->getId();
                if ($id === 1) {
                    $addParent = false;
                }
            }

            $repo = $em->getRepository('ProduitBundle:Categorie');
            if($addParent) {
                $tabIds = array();
                foreach($categorie->getAllChildrens() as $child) {
                    $tabIds[] = $child->getId();
                }

                $form->
                    add('parent',EntityType::class, array(
                        'class'=>'ProduitBundle:Categorie',
                        'required' => false,
                        'multiple' => false,
                        'expanded' => false,
                        'query_builder' => function () use ($id, $repo, $tabIds) {
                            $query = $repo->createQueryBuilder('c');
                            if (isset($id)) {
                                $query->where($query->expr()->notIn('c.id', ':ids'))
                                    ->setParameter('ids', $tabIds);
                            }
                            $query->orderBy('c.root', 'ASC')
                                ->addOrderBy('c.lft', 'ASC');

                            return $query;
                        },
                        'choice_label'=> 'nom'
                    )

                );
            } //if addParent
        });
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ProduitBundle\Entity\Categorie'
        ));
        $resolver->setRequired(array('em'));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'produitbundle_categorie';
    }


}
