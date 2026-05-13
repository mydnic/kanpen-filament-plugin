<?php

namespace Mydnic\KanpenFilamentPlugin\Models;

use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    protected $table = 'kanpen_templates';

    protected $fillable = [
        'name',
        'content_type',
        'content_markdown',
        'design',
        'content_html',
    ];
}
