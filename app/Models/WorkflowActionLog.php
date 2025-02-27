<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkflowActionLog extends Model
{
    use HasFactory;

    protected $table = 'workflow_action_logs';
    protected $fillable = [
        'id_execution',
        'id_action',
        'started_at',
        'completed_at',
        'status',
        'result'
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime'
    ];

    public function execution()
    {
        return $this->belongsTo(WorkflowExecution::class, 'id_execution', 'id_execution');
    }

    public function action()
    {
        return $this->belongsTo(WorkflowAction::class, 'id_action', 'id_action');
    }
}