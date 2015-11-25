<?php

namespace OroB2B\Bundle\ProductBundle\Form\Type;

use OroB2B\Bundle\ProductBundle\Form\DataTransformer\QuickAddRowCollectionToQuickAddOrderTransformer;
use OroB2B\Bundle\ProductBundle\Model\QuickAddRowCollection;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuickAddOrderType extends AbstractType
{
    const NAME = 'orob2b_product_quick_add_order';

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(QuickAddType::PRODUCTS_FIELD_NAME, QuickAddCollectionType::NAME)
            ->add(QuickAddType::COMPONENT_FIELD_NAME, 'hidden')
            ->add(QuickAddType::ADDITIONAL_FIELD_NAME, 'hidden');

        $builder->addModelTransformer(new QuickAddRowCollectionToQuickAddOrderTransformer());
    }

    public function getName()
    {
        return self::NAME;
    }


}
