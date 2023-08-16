<?php declare(strict_types=1);

namespace Macademy\Blog\Api\Post;


/**
 * Blog post interface.
 * @api
 * @since 1.0.0
 */

interface PostInterface
{

    const ID         = 'id';
    const TITLE      = 'title';
    const CONTENT    = 'content';
    CONST CREATED_AT = 'created_at';

// ================== id =========================
    /**
     * @return int
     */
    public function getId();
    /**
     * @param int $id
     * @return $this
     */
    public function setId($id);
// ================== title =======================

    /**
     * @return string
     */
    public function getTitle();
    /**
     * @param string $title
     * @return $this
     */
    public function setTitle($title);
//    =============== content========================

    /**
     * @return string
     */
    public function getContent();
    /**
     * @param string $content
     * @return $this
     */
    public function setContent($content);
//    =============== created at ======================

    /**
     * @return string
     */
    public function getCreatedAt();




}


