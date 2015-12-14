<?php

namespace Oro\Bundle\ActionBundle\Model;

use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactoryInterface;

class ActionFormManager
{
    /** @var FormFactoryInterface */
    protected $formFactory;

    /** @var ActionManager */
    protected $actionManager;

    /** @var ContextHelper */
    protected $contextHelper;

    /**
     * @param FormFactoryInterface $formFactory
     * @param ActionManager $actionManager
     * @param ContextHelper $contextHelper
     */
    public function __construct(
        FormFactoryInterface $formFactory,
        ActionManager $actionManager,
        ContextHelper $contextHelper
    ) {
        $this->formFactory = $formFactory;
        $this->actionManager = $actionManager;
        $this->contextHelper = $contextHelper;
    }

    /**
     * @param string $actionName
     * @param ActionContext $actionContext
     * @return Form
     */
    public function getActionForm($actionName, ActionContext $actionContext)
    {
        $action = $this->actionManager->getAction($actionName, $actionContext);

        return $this->formFactory->create(
            $action->getDefinition()->getFormType(),
            $actionContext,
            array_merge($action->getFormOptions($actionContext), ['action' => $action])
        );
    }
}
