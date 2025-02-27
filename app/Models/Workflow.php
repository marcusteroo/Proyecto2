<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workflow extends Model
{
    use HasFactory;

    protected $table = 'workflows';
    protected $primaryKey = 'id_workflow';
    protected $fillable = ['nombre', 'descripcion', 'trigger_type', 'trigger_params', 'status'];

    protected $casts = [
        'trigger_params' => 'array',
    ];

    public function actions()
    {
        return $this->hasMany(WorkflowAction::class, 'id_workflow', 'id_workflow')
            ->orderBy('order_index');
    }

    public function executions()
    {
        return $this->hasMany(WorkflowExecution::class, 'id_workflow', 'id_workflow');
    }
}