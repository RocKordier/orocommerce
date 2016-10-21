<?php

namespace Oro\Bundle\CustomerBundle\Form\EventListener;

use Oro\Bundle\FormBundle\Event\FormHandler\AfterFormProcessEvent;

class ProductVisibilityPostSubmitListener extends AbstractPostSubmitVisibilityListener
{
    /**
     * @param AfterFormProcessEvent $event
     */
    public function onPostSubmit(AfterFormProcessEvent $event)
    {
        $form = $event->getForm();
        $targetEntity = $form->getData();

        foreach ($form->all() as $visibilityForm) {
            $this->saveForm($visibilityForm, $targetEntity);
        }

        $this->getEntityManager($targetEntity)->flush();
    }
}