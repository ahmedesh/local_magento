<?php declare(strict_types=1);

namespace Macademy\Blog\Setup\Patch\Data;

use Macademy\Blog\Api\PostRepositoryInterface;
use Macademy\Blog\Model\PostFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class PopulateBlogPosts implements DataPatchInterface
{

    private $moduleDataSetup;
    private $postFactory;
    private $postRepository;

    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        PostFactory $postFactory,
        PostRepositoryInterface $postRepository
    ) {
        $this->postRepository = $postRepository;
        $this->postFactory = $postFactory;
        $this->moduleDataSetup = $moduleDataSetup;
    }


    public static function getDependencies(): array
    {
        return [];
    }

    public function getAliases(): array
    {
        return [];
    }

    public function apply()
    {
        $this->moduleDataSetup->startSetup();

//        code goes here
            $posts = [
            [
            'title'   => 'An awesome post',
            'content' => 'this is totally awesome!',
            ],
            [
                'title' => 'Today is sunny',
                'content' => 'I give this movie 5 out of 5 stars!',
            ],
            [
                'title' => 'My movie review',
                'content' => 'I give this movie 5 out of 5 stars!',
            ],
                ];
            foreach ($posts as $postDta){
                $post = $this->postFactory->create();
                $post->setData($postDta);
                $this->postRepository->save($post);
            }

        $this->moduleDataSetup->endSetup();

    }
}
