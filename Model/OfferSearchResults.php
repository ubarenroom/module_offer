<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Ubarenroom\OfferManager\Model;

use Ubarenroom\OfferManager\Api\Data\OfferSearchResultsInterface;
use Magento\Framework\Api\SearchResults;

/**
 * Service Data Object with Page search results.
 */
class OfferSearchResults extends SearchResults implements OfferSearchResultsInterface
{
}
