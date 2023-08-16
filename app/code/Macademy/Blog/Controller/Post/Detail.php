<?php

declare(strict_types=1);

namespace Macademy\Blog\Controller\Post;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;

class Detail implements HttpGetActionInterface
{

    private $pageFactory;
    private $eventManager;
    private $request;

    public function __construct(
        PageFactory $pageFactory,
        EventManager $eventManager,   // ManagerInterface
        RequestInterface $request
    )  {
        $this->pageFactory  = $pageFactory;
        $this->eventManager = $eventManager;
        $this->request      = $request;
    }

    public function execute(): Page
    {
        // dispatch => ارسال
        // dispatch an event containing details of the blog post that is being viewed
        $this->eventManager->dispatch('macademy_blog_post_detail_view' , [
            'request' => $this->request,
        ]);
        return $this->pageFactory->create();
    }

}
