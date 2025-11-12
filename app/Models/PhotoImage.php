<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PhotoImage extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $table = 'photo_images';

    protected $fillable = [
        'id','photoId','filename','url','mime','size','order'
    ];

    public function photo()
    {
        return $this->belongsTo(Photo::class, 'photoId');
    }
}
