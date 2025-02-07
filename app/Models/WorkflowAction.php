<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkflowAction extends Model
{
    use HasFactory;

    protected $table = 'workflow_actions';
    protected $primaryKey = 'id_action';
    protected $fillable = ['id_workflow', 'name', 'description', 'x_position', 'y_position'];
}