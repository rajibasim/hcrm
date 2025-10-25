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
 * Class PaymentHistory
 *
 * @property int $id
 * @property int $bill_id
 * @property date $payment_date
 * @property decimal $online_amount
 * @property decimal $cash_amount
 * @property decimal $balance_amount
 * @property string $attachment
 * @property int $created_by
 * @property int $updated_by
 * @property bool $is_active
 *
 * @package App\Models
 */
class PaymentHistory extends Model implements Auditable{
    use HasFactory, SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'bill_payment_histories';

    protected $casts = [
        'is_active' => 'bool'
    ];

    protected $fillable = [
        'bill_id',
        'payment_date',
        'online_amount',
        'cash_amount',
        'balance_amount',
        'attachment',
        'created_by',
        'updated_by',
        'is_active',
    ];
}
