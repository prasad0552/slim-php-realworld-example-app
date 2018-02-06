<?php

namespace Conduit\Models\Promotions;

use Illuminate\Database\Eloquent\Model;


/**
 * @property integer                                  id
 * @property string                                   code
 * @property string                                   name
 * @property string                                   description
 * @property string                                   dateFrom
 * @property integer                                  dateTo
 * @property string                                   discountType
 * @property float                                    discountAmount
 * @property integer                                  enabled
 * @property \Carbon\Carbon                           created_at
 * @property \Carbon\Carbon                           update_at
 */
class Sale extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'promotion_sale';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code',
        'name',
        'description',
    ];

    public function setCodeAttribute($value)
    {
        $index = 0;
        $slug = $value;
        while (self::newQuery()
            ->where('code', $slug)
            ->where('id', '!=', $this->id)
            ->exists()) {
            $slug = $value . '-' . ++$index;
        }

        return $this->attributes['code'] = $slug;
    }
}