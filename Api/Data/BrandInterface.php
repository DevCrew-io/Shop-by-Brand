<?php
/**
 * @author DevCrew Team
 * @copyright Copyright (c) 2023 DevCrew {https://devcrew.io}
 */

namespace Devcrew\Brand\Api\Data;

/**
 * Interface BrandInterface
 */
interface BrandInterface
{
    /**
     * Constants for keys of data array. Identical to the name of the getter in snake case.
     */
    const BRAND_ID         = 'brand_id';
    const STATUS           = 'status';
    const TITLE            = 'title';
    const DESCRIPTION      = 'description';
    const URL_KEY          = 'url_key';
    const META_TITLE       = 'meta_title';
    const META_KEYWORDS    = 'meta_keywords';
    const META_DESCRIPTION = 'meta_description';
    const IMAGE            = 'image';
    const BANNER_IMAGE     = 'banner_image';
    const POSITION         = 'position';
    /**#@-*/

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Get status
     *
     * @return int|null
     */
    public function getStatus();

    /**
     * Get title
     *
     * @return string|null
     */
    public function getTitle();

    /**
     * Get description
     *
     * @return string|null
     */
    public function getDescription();

    /**
     * Get url key
     *
     * @return string|null
     */
    public function getUrlKey();

    /**
     * Get meta title
     *
     * @return string|null
     */
    public function getMetaTitle();

    /**
     * Get meta keywords
     *
     * @return string|null
     */
    public function getMetaKeywords();

    /**
     * Get meta description
     *
     * @return string|null
     */
    public function getMetaDescription();

    /**
     * Get Position
     *
     * @return string|null
     */
    public function getPosition();

    /**
     * Get image
     *
     * @return string|null
     */
    public function getImage();

    /**
     * Get banner image
     *
     * @return string|null
     */
    public function getBannerImage();

    /**
     * Set Id
     *
     * @param int|null $id
     * @return BrandInterface
     */
    public function setId($id);

    /**
     * Set Status
     *
     * @param int|null $status
     * @return BrandInterface
     */
    public function setStatus($status);

    /**
     * Set Title
     *
     * @param string $title title
     * @return BrandInterface
     */
    public function setTitle($title);

    /**
     * Set Description
     *
     * @param string $description
     * @return BrandInterface
     */
    public function setDescription($description);

    /**
     * Set Url key
     *
     * @param string $urlKey
     * @return BrandInterface
     */
    public function setUrlKey($urlKey);

    /**
     * Set meta title
     *
     * @param string $metaTitle
     * @return BrandInterface
     */
    public function setMetaTitle($metaTitle);

    /**
     * Set meta keywords
     *
     * @param string $metaKeywords
     * @return BrandInterface
     */
    public function setMetaKeywords($metaKeywords);

    /**
     * Set meta description
     *
     * @param string $metaDescription
     * @return BrandInterface
     */
    public function setMetaDescription($metaDescription);

    /**
     * Set Position
     *
     * @param string $position
     * @return BrandInterface
     */
    public function setPosition($position);

    /**
     * Set image
     *
     * @param string $image
     * @return BrandInterface
     */
    public function setImage($image);

    /**
     * Set banner image
     *
     * @param string $bannerImage
     * @return BrandInterface
     */
    public function setBannerImage($bannerImage);
}
