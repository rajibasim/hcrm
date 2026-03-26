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
 * Class BalanceSheets
 *
 * @property int $id
 * @property integer $financial_year
 * @property date $entry_date
 * @property tinyInteger $purpose
 * @property tinyInteger $expenditure_purpose
 * @property tinyInteger $type
 * @property integer $invoice_number
 * @property integer $bill_no
 * @property decimal $inventory_amount
 * @property decimal $online_amount
 * @property decimal $cash_amount
 * @property decimal $opening_inventory_amount
 * @property decimal $opening_online_amount
 * @property decimal $opening_cash_amount
 * @property decimal $closing_inventory_amount
 * @property decimal $closing_online_amount
 * @property decimal $closing_cash_amount
 * @property decimal $claim_amount
 * @property text $notes
 * @property int $created_by
 * @property int $updated_by
 * @property bool $is_active
 *
 * @package App\Models
 */
class BalanceSheetTransactions extends Model implements Auditable{
    use HasFactory, SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'balance_sheet_transactions';

    protected $casts = [
        'is_active' => 'bool'
    ];

    protected $fillable = [
        'financial_year',
        'entry_date',
        'purpose',
        'type',
        'bill_id',
        'expenditure_purpose',
        'invoice_number',
        'inventory_amount',
        'inventory_amount',
        'online_amount',
        'cash_amount',
        'opening_inventory_amount',
        'opening_online_amount',
        'opening_cash_amount',
        'closing_inventory_amount',
        'closing_online_amount',
        'closing_cash_amount',
        'claim_amount',
        'notes',
        'created_by',
        'updated_by',
        'is_active',
    ];


    public function billData(): BelongsTo{
        return $this->belongsTo(Bill::class, 'bill_id');
    }
}
