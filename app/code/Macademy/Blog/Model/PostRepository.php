<?php declare(strict_types=1);

namespace Macademy\Blog\Model;

use \Macademy\Blog\Model\ResourceModel\Post as PostResourceModel;
use Macademy\Blog\Api\Post\PostInterface;
use Macademy\Blog\Api\PostRepositoryInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Exception\NoSuchEntityException;

class PostRepository implements PostRepositoryInterface
{

    private $postResourceModel;
    private $postFactory;

    public function __construct(
        PostFactory                                   $postFactory,
        PostResourceModel                             $postResourceModel
    ) {
        $this->postFactory = $postFactory;
        $this->postResourceModel = $postResourceModel;
    }


    public function getById(int $id): PostInterface
    {
        $post = $this->postFactory->create();
        $this->postResourceModel->load($post , $id);

        if (!$post->getId()){
            throw new NoSuchEntityException(__('The blog post with "%1" ID doesn\'t exist.', $id));  // pass $id to "%1"
//            try {
//                $this->postResourceModel->load($post , $id);
//            } catch(\Exception $exception) {
////                 do something with $e, for example log $e->getMessage()
//                throw new NoSuchEntityException(__($exception->getMessage()));
//            }
          }

        return $post;
    }

    public function save(PostInterface $post): PostInterface
    {
        try {
            $this->postResourceModel->save($post);
        }
        catch (\Exception $exception){
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
        return $post;
    }

    public function deleteById(int $id): bool
    {
        $post = $this->getById($id);

        try {
            $this->postResourceModel->delete($post);
        }
        catch (\Exception $exception){
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        return true;
    }
}
