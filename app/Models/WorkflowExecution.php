<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkflowExecution extends Model
{
    use HasFactory;

    protected $table = 'workflow_executions';
    protected $primaryKey = 'id_execution';
    protected $fillable = [
        'id_workflow',
        'started_at',
        'completed_at',
        'status',
        'result',
        'trigger_data'
    ];

    protected $casts = [
        'trigger_data' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime'
    ];

    public function workflow()
    {
        return $this->belongsTo(Workflow::class, 'id_workflow', 'id_workflow');
    }

    public function logs()
    {
        return $this->hasMany(WorkflowActionLog::class, 'id_execution', 'id_execution');
    }
}