<?php

namespace OroB2B\Bundle\PricingBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

use Oro\Bundle\BatchBundle\ORM\Query\BufferedQueryResultIterator;

use OroB2B\Bundle\PricingBundle\Entity\ChangedPriceListChain;

class ChangedPriceListCollectionRepository extends EntityRepository
{
    /**
     * @return BufferedQueryResultIterator|ChangedPriceListChain[]
     */
    public function getCollectionChangesIterator()
    {
        $qb = $this->createQueryBuilder('changes');

        return new BufferedQueryResultIterator($qb->getQuery());
    }
}
