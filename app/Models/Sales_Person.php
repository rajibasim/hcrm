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
 * Class Sales_Person
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property bigInteger $mobile
 * @property bigInteger $alternet_mobile
 * @property string $address
 * @property string $id_prove_type
 * @property string $id_prove
 * @property int $created_by
 * @property int $updated_by
 * @property bool $is_active
 *
 * @package App\Models
 */
class Sales_Person extends Model implements Auditable{
    use HasFactory, SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'sales_person';

    protected $casts = [
        'is_active' => 'bool'
    ];

    protected $fillable = [
    	'name',
		'email',
		'mobile',
		'alternet_mobile',
		'address',
		'id_prove_type',
		'id_prove',
        'created_by',
        'updated_by',
        'is_active',
    ];
}
