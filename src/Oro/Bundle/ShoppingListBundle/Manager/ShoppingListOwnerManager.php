<?php

namespace Oro\Bundle\ShoppingListBundle\Manager;

use Doctrine\ORM\EntityRepository;
use Oro\Bundle\CustomerBundle\Entity\CustomerUser;
use Oro\Bundle\EntityConfigBundle\Provider\ConfigProvider;
use Oro\Bundle\SecurityBundle\AccessRule\AclAccessRule;
use Oro\Bundle\SecurityBundle\AccessRule\AvailableOwnerAccessRule;
use Oro\Bundle\SecurityBundle\ORM\Walker\AclHelper;
use Oro\Bundle\ShoppingListBundle\Entity\ShoppingList;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Class that allows to set owner to shopping list.
 */
class ShoppingListOwnerManager
{
    /**
     * @var AclHelper
     */
    protected $aclHelper;

    /**
     * @var RegistryInterface
     */
    protected $registry;

    /**
     * @var ConfigProvider
     */
    protected $configProvider;

    /**
     * ShoppingListOwnerManager constructor.
     * @param AclHelper $aclHelper
     * @param RegistryInterface $registry
     * @param ConfigProvider $configProvider
     */
    public function __construct(AclHelper $aclHelper, RegistryInterface $registry, ConfigProvider $configProvider)
    {
        $this->aclHelper = $aclHelper;
        $this->registry = $registry;
        $this->configProvider = $configProvider;
    }

    /**
     * @param int $ownerId
     * @param ShoppingList $shoppingList
     */
    public function setOwner($ownerId, ShoppingList $shoppingList)
    {
        $user = $this->registry->getRepository(CustomerUser::class)->find($ownerId);
        if (null === $user) {
            throw new \InvalidArgumentException(sprintf("User with id=%s not exists", $ownerId));
        }
        if ($user === $shoppingList->getCustomerUser()) {
            return;
        }
        if ($this->isUserAssignable($ownerId)) {
            $shoppingList->setCustomerUser($user);
            $this->registry->getManagerForClass(ShoppingList::class)->flush();
        } else {
            throw new AccessDeniedException();
        }
    }

    /**
     * @param int $id
     * @return boolean
     */
    protected function isUserAssignable($id)
    {
        /** @var EntityRepository $repository */
        $repository = $this->registry->getRepository(CustomerUser::class);
        $qb = $repository
            ->createQueryBuilder('user')
            ->where("user.id = :id")
            ->setParameter("id", $id);

        $query = $this->aclHelper->apply(
            $qb,
            'ASSIGN',
            [
                AclAccessRule::DISABLE_RULE => true,
                AvailableOwnerAccessRule::ENABLE_RULE => true,
                AvailableOwnerAccessRule::TARGET_ENTITY_CLASS => ShoppingList::class
            ]
        );

        $owner = $query->getOneOrNullResult();

        return null !== $owner;
    }
}
