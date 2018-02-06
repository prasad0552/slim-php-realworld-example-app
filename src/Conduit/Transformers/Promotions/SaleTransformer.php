<?php

namespace Conduit\Transformers\Promotions;

use Conduit\Models\Promotions\Sale;
use Conduit\Transformers\AuthorTransformer;
use League\Fractal\TransformerAbstract;

class SaleTransformer extends TransformerAbstract
{
    /**
     * @var integer|null
     */
    protected $requestUserId;

    /**
     * SaleTransformer constructor.
     *
     * @param int $requestUserId
     */
    public function __construct($requestUserId = null)
    {
        $this->requestUserId = $requestUserId;
    }

    public function transform(Sale $sale)
    {
        return [
            "code" => $sale->code,
            "name" => $sale->name,
            "description" => $sale->description,
            "startDate" => $sale->dateFrom,
            "endDate" => $sale->dateTo,
            'createdAt' => $sale->created_at->toIso8601String(),
            'updatedAt' => isset($sale->updated_at) ? $sale->updated_at->toIso8601String() : $sale->updated_at
        ];
    }


    /**
     * Include Author
     *
     * @param \Conduit\Models\Promotions\Sale $sale
     *
     * @return \League\Fractal\Resource\Item
     * @internal param \Conduit\Models\Comment $comment
     *
     */
    public function includeAuthor(Sale $sale)
    {
        $author = $sale->user;

        return $this->item($author, new AuthorTransformer($this->requestUserId));
    }
}