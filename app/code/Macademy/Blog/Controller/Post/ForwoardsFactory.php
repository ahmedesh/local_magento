<?php declare(strict_types=1);

namespace Macademy\Blog\Controller\Post;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\Result\Forward;
use Magento\Framework\Controller\Result\ForwardFactory;

class ForwoardsFactory implements HttpGetActionInterface
{
//  ForwordFactory

    private $forwordFactory;

    public function __construct(
        ForwardFactory $forwordFactory
    ) {
        $this->forwordFactory = $forwordFactory;
    }

    public function execute(): Forward
    {
        /** @var Forward $forward */
        $forward = $this->forwordFactory->create();
        return $forward->setController('post')->forward('listaction'); // return data from lisaction to this my page
    }

}
