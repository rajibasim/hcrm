<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
//use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Permission\Models\Permission as SpatiePermission;
use App\Models\PermissionGroup;

class Permission extends Model implements Auditable{
    
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'permissions';
    public $timestamp = true;
    protected $keyType = 'string';

    function group()
    {
        return $this->belongsTo(PermissionGroup::class, 'group_id', 'id');
    }
}
