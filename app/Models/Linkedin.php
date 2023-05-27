<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Linkedin extends Model
{
    use HasFactory;

    protected $table = "linkedin_accounts";
    protected $fillable = [
        "name",
        "link",
        "mailbox_id",
        "avatar",
        "password",
        "create_date",
        "proxy_id",
        "warmup",
    ];

    public function mailbox()
    {
        return $this->belongsTo(Mailbox::class);
    }

    public function projects() {
        return $this->belongsToMany(Project::class, 'linkedin_accounts_projects', 'linkedin_id', 'project_id');
    }

    //TODO proxy
}
