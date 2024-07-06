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
 * Class Product
 *
 * @property int $id
 * @property integer $category_id
 * @property integer $unit_id
 * @property string $name
 * @property text $description
 * @property string $note
 * @property string $image
 * @property int $created_by
 * @property int $updated_by
 * @property bool $is_active
 *
 * @package App\Models
 */
class Product extends Model implements Auditable{
    use HasFactory, SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'products';

    protected $casts = [
        'is_active' => 'bool'
    ];

    protected $fillable = [
		'category_id',
		'unit_id',
		'name',
		'description',
		'note',
		'image',
        'created_by',
        'updated_by',
        'is_active',
    ];

    public function unit(): BelongsTo{
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function category(): BelongsTo{
        return $this->belongsTo(Category::class, 'category_id');
    }
}
