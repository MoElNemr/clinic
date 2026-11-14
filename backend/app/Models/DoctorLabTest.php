<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class DoctorLabTest extends Model
{
    use BelongsToTenant;

    protected $guarded = ['id'];

    protected $appends = ['name', 'category'];

    public function getNameAttribute(): ?string
    {
        return $this->test_name;
    }

    public function getCategoryAttribute(): ?string
    {
        return $this->type;
    }
}
