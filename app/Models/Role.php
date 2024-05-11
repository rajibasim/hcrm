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
use Spatie\Permission\Models\Role as SpatieRole;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Role extends SpatieRole implements AuditableContract
{
    use SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'roles';
    public $timestamp = true;
    protected $keyType = 'string';
    public $guard_name = 'web';

    protected $auditExclude = [
        'name'
    ];

    protected $casts = [
        'is_active' => 'bool'
    ];

    protected $fillable = [
        'name',
        'label',
        'created_by',
        'updated_by',
        'is_active',
    ];
}
