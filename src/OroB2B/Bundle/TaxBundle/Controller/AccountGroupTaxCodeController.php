<?php

namespace OroB2B\Bundle\TaxBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Oro\Bundle\SecurityBundle\Annotation\Acl;
use Oro\Bundle\SecurityBundle\Annotation\AclAncestor;

use OroB2B\Bundle\TaxBundle\Entity\AccountGroupTaxCode;
use OroB2B\Bundle\TaxBundle\Form\Type\AccountGroupTaxCodeType;

class AccountGroupTaxCodeController extends Controller
{
    /**
     * @Route("/", name="orob2b_tax_account_group_tax_code_index")
     * @Template
     * @AclAncestor("orob2b_tax_account_group_tax_code_view")
     *
     * @return array
     */
    public function indexAction()
    {
        return [
            'entity_class' => $this->container->getParameter('orob2b_tax.entity.account_group_tax_code.class')
        ];
    }

    /**
     * @Route("/view/{id}", name="orob2b_tax_account_group_tax_code_view", requirements={"id"="\d+"})
     * @Template
     * @Acl(
     *      id="orob2b_tax_account_group_tax_code_view",
     *      type="entity",
     *      class="OroB2BTaxBundle:AccountGroupTaxCode",
     *      permission="VIEW"
     * )
     *
     * @param AccountGroupTaxCode $accountGroupTaxCode
     * @return array
     */
    public function viewAction(AccountGroupTaxCode $accountGroupTaxCode)
    {
        return [
            'entity' => $accountGroupTaxCode
        ];
    }

    /**
     * @Route("/create", name="orob2b_tax_account_group_tax_code_create")
     * @Template("OroB2BTaxBundle:AccountGroupTaxCode:update.html.twig")
     * @Acl(
     *      id="orob2b_tax_account_group_tax_code_create",
     *      type="entity",
     *      class="OroB2BTaxBundle:AccountGroupTaxCode",
     *      permission="CREATE"
     * )
     *
     * @return array
     */
    public function createAction()
    {
        return $this->update(new AccountGroupTaxCode());
    }

    /**
     * @Route("/update/{id}", name="orob2b_tax_account_group_tax_code_update", requirements={"id"="\d+"})
     * @Template
     * @Acl(
     *      id="orob2b_tax_account_group_tax_code_update",
     *      type="entity",
     *      class="OroB2BTaxBundle:AccountGroupTaxCode",
     *      permission="EDIT"
     * )
     *
     * @param AccountGroupTaxCode $accountGroupTaxCode
     * @return array
     */
    public function updateAction(AccountGroupTaxCode $accountGroupTaxCode)
    {
        return $this->update($accountGroupTaxCode);
    }

    /**
     * @param AccountGroupTaxCode $accountGroupTaxCode
     * @return array|RedirectResponse
     */
    protected function update(AccountGroupTaxCode $accountGroupTaxCode)
    {
        return $this->get('oro_form.model.update_handler')->handleUpdate(
            $accountGroupTaxCode,
            $this->createForm(AccountGroupTaxCodeType::NAME, $accountGroupTaxCode),
            function (AccountGroupTaxCode $accountTaxCode) {
                return [
                    'route' => 'orob2b_tax_account_group_tax_code_update',
                    'parameters' => ['id' => $accountTaxCode->getId()]
                ];
            },
            function (AccountGroupTaxCode $accountTaxCode) {
                return [
                    'route' => 'orob2b_tax_account_group_tax_code_view',
                    'parameters' => ['id' => $accountTaxCode->getId()]
                ];
            },
            $this->get('translator')->trans('orob2b.tax.controller.account_group_tax_code.saved.message')
        );
    }
}
