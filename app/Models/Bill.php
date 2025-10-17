<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * Class Bill
 *
 * @property int $id
 * @property string $bill_number
 * @property date $invoice_date
 * @property int $customer_id
 * @property int $sales_person_id
 * @property date $delivery_status_update_date
 * @property int $delivery_status_id
 * @property decimal $billed_amount
 * @property decimal $damage_amount
 * @property decimal $return_amount
 * @property decimal $adjusment_percent
 * @property decimal $adjusment_amount
 * @property decimal $online_amount
 * @property decimal $cash_amount
 * @property decimal $balance_amount
 * @property text $notes
 * @property int $created_by
 * @property int $updated_by
 * @property bool $is_active
 *
 * @package App\Models
 */
class Bill extends Model implements Auditable{
    use HasFactory, SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'bills';

    protected $casts = [
        'is_active' => 'bool'
    ];

    protected $fillable = [
        'bill_number',
        'invoice_date',
        'customer_id',
        'sales_person_id',
        'delivery_status_update_date',
        'delivery_status_id',
        'billed_amount',
        'damage_amount',
        'return_amount',
        'adjusment_percent',
        'adjusment_amount',
        'online_amount',
        'cash_amount',
        'balance_amount',
        'notes',
        'created_by',
        'updated_by',
        'is_active',
    ];
}
