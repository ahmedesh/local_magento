<?php declare(strict_types=1);

namespace Macademy\Blog\ViewModel;

use Macademy\Blog\Api\Post\PostInterface;
use Macademy\Blog\Api\PostRepositoryInterface;
use Macademy\Blog\Model\ResourceModel\Post\Collection;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Block\ArgumentInterface;


class Post implements ArgumentInterface
{
//    Pass test data to template using a viewModel
//    ========================================
//    public function getList(): array
//    {
//        return[
//          new DataObject(['id' => 1, 'title' => 'Post A' ]),
//          new DataObject(['id' => 1, 'title' => 'Post B' ]),
//          new DataObject(['id' => 3, 'title' => 'Post C' ]),
//        ];
//    }
//
//    public function getCount(): int
//    {
//        return count($this->getList());
//    }

//    Load real data using a collection from database
//    ===============================================
    private $collection;
    private $postRepository;
    private $request;

    public function __construct(
        Collection $collection,
        PostRepositoryInterface $postRepository,
        RequestInterface $request
    )
    {
        $this->collection = $collection;
        $this->postRepository = $postRepository;
        $this->request = $request;
    }

    public function getList(): array
    {
        return $this->collection->getItems();
    }

    public function getCount(): int
    {
        return $this->collection->count();
    }

//    Load data using a repository
//    ============================
    /**
     * @throws LocalizedException
     */
    public function getDetail(): PostInterface
    {
        $id = (int) $this->request->getParam('id');  // (int) => to ensure that is always a number
        return $this->postRepository->getById($id);
    }

}
