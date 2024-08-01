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
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Auth;

class PermissionGroup extends Model implements Auditable{

    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'permission_groups';
    public $timestamp = true;
    protected $keyType = 'string';

    function groupPermissions($group_id){
        return $groupPermissions = Permission::where('group_id', '=', $group_id)->get();
    }
}
