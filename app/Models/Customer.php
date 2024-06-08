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
 * Class Customer
 *
 * @property int $id
 * @property int $beat_id
 * @property int $area_id
 * @property string $store_name
 * @property string $proprietor_name
 * @property string $addrsss
 * @property bigInt $mobile
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
		'beat_id',
		'area_id',
		'store_name',
		'proprietor_name',
		'addrsss',
		'mobile',
        'created_by',
        'updated_by',
        'is_active',
    ];
}
