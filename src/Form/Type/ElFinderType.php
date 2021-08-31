<?php

declare(strict_types=1);

namespace FM\ElfinderBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ElFinderType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->setAttribute('enable', $options['enable']);

        if ($builder->getAttribute('enable')) {
            $builder->setAttribute('instance', $options['instance']);
        }
        $builder->setAttribute('homeFolder', $options['homeFolder']);
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['enable'] = $options['enable'];

        if ($options['enable']) {
            $view->vars['instance']   = $options['instance'];
            $view->vars['homeFolder'] = $options['homeFolder'];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'enable'        => true,
                'instance'      => 'default',
                'homeFolder'    => '',
            ])
            ->setAllowedTypes('enable', 'bool')
            ->setAllowedTypes('instance', ['string', 'null'])
            ->setAllowedTypes('homeFolder', ['string', 'null']);
    }

    public function getName(): string
    {
        return $this->getBlockPrefix();
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'elfinder';
    }
}
