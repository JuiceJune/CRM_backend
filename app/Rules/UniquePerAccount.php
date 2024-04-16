<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

class UniquePerAccount implements Rule
{
    protected $table;
    protected $column;
    protected $accountId;
    protected $currentId;
    protected $projectId;

    public function __construct($table, $column, $accountId, $currentId = null, $projectId = null)
    {
        $this->table = $table;
        $this->column = $column;
        $this->accountId = $accountId;
        $this->currentId = $currentId;
    }

    public function passes($attribute, $value): bool
    {
        $query = DB::table($this->table)
            ->where($this->column, $value)
            ->where('account_id', $this->accountId);

        // Якщо заданий ID користувача для оновлення, ігноруємо його при перевірці унікальності
        if (!is_null($this->currentId)) {
            $query->where('id', '!=', $this->currentId);
        }

        if (!is_null($this->projectId)) {
            $query->where('project_id', '=', $this->projectId);
        }

        return !$query->exists();
    }

    public function message(): string
    {
        return 'The :attribute has already been taken for this account.';
    }
}
