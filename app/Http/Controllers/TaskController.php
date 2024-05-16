<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    public function index()
    {
        $todos = Task::all();
        return view('taskList', compact('todos'));
    }
    public function show()
    {
        $todos = Task::all();
        return response()->json(['data' => $todos]);
    }
    public function store(Request $request)
    {
        if ($request->get('is_completed') == 'on') {
            $is_complete = 1;
        } else {
            $is_complete = 0;
        }

        $exist =  Task::where('title', $request->get('title'))->get();
        if ($exist) {
            return response()->json(['message' => 'Task Exist Already', 'status' => 'false']);
        }
        $new_task = Task::create([
            'title' => $request->get('title'),
            // 'is_completed' => $is_complete
        ]);

        if ($new_task) {
            return response()->json(['message' => 'Task Added successfully', 'data' => $new_task]);
        } else {
            return response()->json(['message' => 'something went wrong , please try again']);
        }
    }

    public function destroy($id)
    {
        $dd =  Task::where('id', $id)->delete();
        if ($dd) {
            return response()->json(['message' => 'Task deleted successfully']);
        } else {
            return response()->json(['message' => 'something went wrong , please try again']);
        }
    }
}
