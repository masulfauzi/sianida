<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IjinSholat extends Model
{
    use HasFactory;

    /**
     * The connection name for the model.
     *
     * @var string|null
     */
    protected $connection = 'mysql-niaga';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ijin_sholat';
}
