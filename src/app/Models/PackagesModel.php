<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PackagesModel extends Model
{
    use HasFactory;

    public function search(string $logDate){
        return DB::table('packages')->get('logDate', $logDate);
    }
}
