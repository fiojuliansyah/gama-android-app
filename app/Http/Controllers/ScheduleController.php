<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\TaskPlanner;
use App\Models\TaskProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class ScheduleController extends Controller
{
    public function index()
    {
        
        $now = Carbon::now();
        
        
        $tomorrow = Carbon::tomorrow();
    
        
        $tasks = TaskPlanner::whereDate('date', $now->toDateString())->get();
    
        
        $taskProgressInProgress = TaskProgress::whereDate('date', $now->toDateString())
                                              ->where('status', 'in_progress')
                                              ->get();
        $taskProgressCompleted = TaskProgress::whereDate('date', $now->toDateString())
                                            ->where('status', 'completed')
                                            ->get();
    
        
        $taskProgressInProgressIds = $taskProgressInProgress->pluck('task_planner_id')->toArray();
        $taskProgressCompletedIds = $taskProgressCompleted->pluck('task_planner_id')->toArray();
    
        
        $tasksPending = $tasks->filter(function($task) use ($taskProgressInProgressIds, $taskProgressCompletedIds) {
            return !in_array($task->id, $taskProgressInProgressIds) && !in_array($task->id, $taskProgressCompletedIds);
        });
    
        
        $tasksTomorrow = TaskPlanner::whereDate('date', $tomorrow->toDateString())->get();
    
        
        return view('tasks.index', compact('tasksPending', 'taskProgressInProgress', 'taskProgressCompleted', 'tasksTomorrow'));
    }    
    


    public function show($id)
    {
        
        $task = TaskPlanner::findOrFail($id);

        
        $taskProgress = TaskProgress::where('task_planner_id', $task->id)->get();

        
        return view('tasks.show', compact('task', 'taskProgress'));
    }

    public function progressStart(Request $request)
    {
        $user = Auth::user();
        $timeNow = Carbon::now()->toTimeString();
        $currentDate = Carbon::now()->toDateString();
    
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            
            $cloudinaryImage = Cloudinary::upload($image->getRealPath(), [
                'folder' => 'progress',
                'transformation' => [
                    'width' => 300, 
                    'height' => 200,
                    'crop' => 'fill'
                ]
            ]);
            $imgUrl = $cloudinaryImage->getSecurePath();
            $imgPublicId = $cloudinaryImage->getPublicId();
        } else {
            $imgUrl = null;
            $imgPublicId = null;
        }
    
        $taskProgress = new TaskProgress;
        $taskProgress->task_planner_id = $request->task_planner_id;
        $taskProgress->user_id = $user->id;
        $taskProgress->status = 'in_progress';
        $taskProgress->date = $currentDate;
        $taskProgress->start_time = $timeNow;
        $taskProgress->image_before_url = $imgUrl;
        $taskProgress->image_before_public_id = $imgPublicId;
        $taskProgress->save();
    
        return redirect()->back()
                         ->with('success', 'Task recorded successfully.');
    }

    public function progressEnd(Request $request)
    {
        $user = Auth::user();
        $timeNow = Carbon::now()->toTimeString();
    
        
        $taskProgress = TaskProgress::where('task_planner_id', $request->task_planner_id)
                                   ->where('status', 'in_progress')
                                   ->first();
    
        if (!$taskProgress) {
            return redirect()->back()
                            ->with('error', 'No in-progress task found to complete.');
        }
    
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            
            $cloudinaryImage = Cloudinary::upload($image->getRealPath(), [
                'folder' => 'progress',
                'transformation' => [
                    'width' => 300, 
                    'height' => 200,
                    'crop' => 'fill'
                ]
            ]);
            $imgUrl = $cloudinaryImage->getSecurePath();
            $imgPublicId = $cloudinaryImage->getPublicId();
        } else {
            $imgUrl = null;
            $imgPublicId = null;
        }
    
        
        $taskProgress->status = 'completed';
        $taskProgress->end_time = $timeNow;
        $taskProgress->image_after_url = $imgUrl;
        $taskProgress->image_after_public_id = $imgPublicId;
        $taskProgress->progress_description = $request->progress_description;
        $taskProgress->save();
    
        return redirect()->back()
                        ->with('success', 'Task completed successfully.');
    }
}