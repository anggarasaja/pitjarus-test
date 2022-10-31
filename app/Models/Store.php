<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'store';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'store_id';

    public function store_area()
    {
        return $this->hasOne('StoreArea', "area_id", "area_id");
    }
}
