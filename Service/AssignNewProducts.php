<?php
declare(strict_types=1);

namespace Kadoco\News\Service;

use Magento\Catalog\Api\CategoryLinkManagementInterface;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Kadoco\News\Model\NewsConfigProvider;

class AssignNewProducts
{
    private NewsConfigProvider $newsConfigProvider;
    /**
     * @var CategoryLinkManagementInterface
     */
    private CollectionFactory $collectionFactory;
    /**
     * @var CategoryRepositoryInterface
     */
    private CategoryRepositoryInterface $categoryRepository;

    public function __construct(
        NewsConfigProvider $newsConfigProvider,
        CollectionFactory $collectionFactory,
        CategoryRepositoryInterface $categoryRepository
    ) {
        $this->newsConfigProvider = $newsConfigProvider;
        $this->collectionFactory = $collectionFactory;
        $this->categoryRepository = $categoryRepository;
    }

    public function execute():void
    {
        if (!$this->validateConfig()) {
            return;
        }
        $newProductsIds = $this->getNewProductIds();
        $this->assignProductsToNews($newProductsIds);
    }

    public function validateConfig():bool
    {
        if (!$this->newsConfigProvider->getIsActive()) {
            return false;
        }
        if (!$this->newsConfigProvider->getCategoryId()) {
            return false;
        }
        if (!$this->newsConfigProvider->getDays()) {
            return false;
        }

        return true;
    }

    private function getNewProductIds():array
    {
        $days = (int) $this->newsConfigProvider->getDays();
        $to = date("Y-m-d h:i:s");
        $from = date('Y-m-d h:i:s', strtotime("-$days days", strtotime($to)));

        /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $collection */
        $collection = $this->collectionFactory->create();
        $collection
            ->addAttributeToSelect('entity_id')
            ->addFieldToFilter('created_at', ['from' => $from])
            ->addAttributeToSort('created_at', 'DESC');

        $ids = [];
        /** @var Product $product */
        foreach ($collection as $product) {
            $ids[] = $product->getEntityId();
        }

        return $ids;
    }

    private function assignProductsToNews(array $productsToAssign)
    {
        $categoryId = (int)$this->newsConfigProvider->getCategoryId();
        $category = $this->categoryRepository->get($categoryId);
        $postedProducts = [];
        $position = 0;
        foreach ($productsToAssign as $id) {
            $position++;
            $postedProducts[$id] = $position;
        }
        $category->setPostedProducts($postedProducts);
        $category->save();
    }
}
