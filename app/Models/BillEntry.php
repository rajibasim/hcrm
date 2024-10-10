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
 * Class BillEntry
 *
 * @property int $id
 * @property string $bill_no
 * @property date $return_date
 * @property int $sales_person_id
 * @property int $beat_id
 * @property int $area_id
 * @property int $customer_id
 * @property string $note
 * @property decimal $total_amount
 * @property decimal $online_amount
 * @property decimal $offline_amount
 * @property decimal $balance_amount
 * @property int $created_by
 * @property int $updated_by
 * @property bool $is_active
 *
 * @package App\Models
 */
class BillEntry extends Model implements Auditable{
    use HasFactory, SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'bill_entries';

    protected $casts = [
        'is_active' => 'bool'
    ];

    protected $fillable = [
    	'bill_no',
		'return_date',
		'sales_person_id',
		'beat_id',
		'area_id',
		'customer_id',
		'note',
		'total_amount',
        'online_amount',
        'offline_amount',
        'balance_amount',
        'created_by',
        'updated_by',
        'is_active',
    ];

    public function area(): BelongsTo{
        return $this->belongsTo(Area::class, 'area_id');
    }

    public function beat(): BelongsTo{
        return $this->belongsTo(Beat::class, 'beat_id');
    }

    public function customer(): BelongsTo{
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function sales_person(): BelongsTo{
        return $this->belongsTo(SalesPerson::class, 'sales_person_id');
    }
}
