<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkflowAction extends Model
{
    use HasFactory;

    protected $table = 'workflow_actions';
    protected $primaryKey = 'id_action';
    protected $fillable = [
        'id_workflow', 
        'action_type',
        'name', 
        'description', 
        'order_index',
        'x_position', 
        'y_position',
        'config'
    ];

    protected $casts = [
        'config' => 'array',
    ];

    public function workflow()
    {
        return $this->belongsTo(Workflow::class, 'id_workflow', 'id_workflow');
    }

    public function logs()
    {
        return $this->hasMany(WorkflowActionLog::class, 'id_action', 'id_action');
    }
}