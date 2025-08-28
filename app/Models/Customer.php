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
 * Class Customer
 *
 * @property int $id
 * @property string $party_name
 * @property bigInt $phone_no
 * @property string $party_code
 * @property string $beat
 * @property text $address
 * @property text $party_channel
 * @property string channel
 * @property string hul_code
 * @property int $created_by
 * @property int $updated_by
 * @property bool $is_active
 *

 * @package App\Models
 */
class Customer extends Model implements Auditable{
    use HasFactory, SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'customers';

    protected $casts = [
        'is_active' => 'bool'
    ];

    protected $fillable = [
		'party_name',
        'phone_no',
        'party_code',
        'beat',
        'address',
        'party_channel',
        'channel',
        'hul_code',
        'created_by',
        'updated_by',
        'is_active',
    ];
}
