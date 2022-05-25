<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Modules extends Model
{
    /**
     * Get the phone associated with the user.
     */
    protected $table = 'modules';

    public function children()
    {
        return $this->hasMany(self::class, 'module_parent_id', 'id')
            // ->where(['status' => 1])
            ->with(['children' => function ($query) {
                $query->select('id', 'module_parent_id', 'module_name', 'module_type', 'module_code', 'status');
            }]);
    }
}
