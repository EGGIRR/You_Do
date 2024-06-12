<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        "name",
        "description",
        "expired_date",
        "important",
        "card_id"
    ];

    public function complete()
    {
        $this->complete = true;
        $this->save();
    }

    public function expire()
    {
        $this->expired = $this->expired_date < Carbon::now();
        $this->save();
    }

    public function important()
    {
        if ($this->important) {
            $this->important = false;
            $this->save();
        } else {
            $this->important = true;
            $this->save();
        }
    }
}
