<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * Class ReturnEntryProduct
 *
 * @property int $id
 * @property date $return_date
 * @property int $return_entry_id
 * @property int $product_id
 * @property decimal $product_qty
 * @property decimal $product_unit_price
 * @property decimal $sub_total
 * @property int $created_by
 * @property int $updated_by
 * @property bool $is_active
 *
 * @package App\Models
 */
class ReturnEntryProduct extends Model implements Auditable{
    use HasFactory, SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'return_entry_products';

    protected $casts = [
        'is_active' => 'bool'
    ];

    protected $fillable = [
    	'return_entry_id',
        'return_date',
		'product_id',
		'product_qty',
		'product_unit_price',
		'sub_total',
        'created_by',
        'updated_by',
        'is_active',
    ];

    public function product(): BelongsTo{
        return $this->belongsTo(Product::class, 'product_id');
    }
}
