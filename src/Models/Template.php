<?php

namespace Mydnic\KanpenFilamentPlugin\Models;

use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    protected $table = 'kanpen_templates';

    protected $fillable = [
        'name',
        'design',
        'content_html',
    ];
}
