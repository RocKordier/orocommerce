<?php

namespace Oro\Bundle\ProductBundle\Tests\Unit\ContentVariant\Stub;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Oro\Bundle\ProductBundle\Entity\Product;
use Oro\Bundle\ScopeBundle\Entity\Scope;
use Oro\Component\WebCatalog\Entity\ContentVariantInterface;

class ContentVariantStub implements ContentVariantInterface
{
    /**
     * @var string
     */
    protected $type;

    /**
     * @var Product
     */
    protected $productPageProduct;

    /**
     * @var ArrayCollection|Scope[]
     */
    protected $scopes;

    /**
     * @var bool
     */
    protected $default;

    public function __construct()
    {
        $this->scopes = new ArrayCollection();
    }

    /**
     * @return Collection|Scope[]
     */
    public function getScopes()
    {
        return $this->scopes;
    }

    /**
     * @param Scope $scope
     * @return $this
     */
    public function addScope(Scope $scope)
    {
        if (!$this->scopes->contains($scope)) {
            $this->scopes->add($scope);
        }

        return $this;
    }

    /**
     * @param Scope $scope
     * @return $this
     */
    public function removeScope(Scope $scope)
    {
        if ($this->scopes->contains($scope)) {
            $this->scopes->removeElement($scope);
        }

        return $this;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return 1;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return ContentVariantStub
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return Product
     */
    public function getProductPageProduct()
    {
        return $this->productPageProduct;
    }

    /**
     * @param Product $productPageProduct
     * @return ContentVariantStub
     */
    public function setProductPageProduct($productPageProduct)
    {
        $this->productPageProduct = $productPageProduct;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getNode()
    {
        return null;
    }

    /**
     * @param bool $default
     * @return $this
     */
    public function setDefault($default)
    {
        $this->default = (bool)$default;

        return $this;
    }

    /**
     * @return bool
     */
    public function isDefault()
    {
        return $this->default;
    }
}