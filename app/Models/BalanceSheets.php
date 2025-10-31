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
 * Class BalanceSheets
 *
 * @property int $id
 * @property string $financial_year
 * @property date $entry_date
 * @property decimal $amount
 * @property int $purpose
 * @property int $type
 * @property text $notes
 * @property int $created_by
 * @property int $updated_by
 * @property bool $is_active
 *
 * @package App\Models
 */
class BalanceSheets extends Model implements Auditable{
    use HasFactory, SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'balance_sheets';

    protected $casts = [
        'is_active' => 'bool'
    ];

    protected $fillable = [
        'financial_year',
        'entry_date',
        'amount',
        'purpose',
        'type',
        'notes',
        'created_by',
        'updated_by',
        'is_active',
    ];
}
