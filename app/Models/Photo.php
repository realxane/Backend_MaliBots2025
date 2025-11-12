<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Photo extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id','titre','description','regionId','publieParAdminId'
    ];

    public function images()
    {
        return $this->hasMany(PhotoImage::class, 'photoId');
    }

    public function region()
    {
        return $this->belongsTo(Region::class, 'regionId');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'publieParAdminId');
    }
}
