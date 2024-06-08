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
 * Class Area
 *
 * @property int $id
 * @property int $beat_id
 * @property string $area
 * @property int $created_by
 * @property int $updated_by
 * @property bool $is_active
 *
 * @package App\Models
 */
class Area extends Model implements Auditable{
    use HasFactory, SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'areas';

    protected $primaryKey = 'id';

    protected $casts = [
        'is_active' => 'bool'
    ];

    protected $fillable = [
    	'beat_id',
        'area',
        'created_by',
        'updated_by',
        'is_active',
    ];


    public function beats(): BelongsTo{
        return $this->belongsTo(Beat::class, 'beat_id');
    }
}
