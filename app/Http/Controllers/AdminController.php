<?php

namespace App\Http\Controllers;

use App\Models\TelegramUser;
use App\Models\Task;
use App\Models\DailyTask;
use Illuminate\Http\Request;
use \App\Models\Mission;
use \App\Models\MissionLevel;
use \App\Models\MissionType;
use Illuminate\Support\Facades\DB;
// use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;



class AdminController extends Controller
{
    public function dashboard()
    {
        $userCount = TelegramUser::count();
        $taskCount = Task::count();
        $dailyTaskCount = DailyTask::count();
        return view('dashboard', compact('userCount', 'taskCount', 'dailyTaskCount'));
    }

    public function users()
    {
        $users = TelegramUser::all();
        return response()->json($users);

        // return view('users', compact('users'));
    }

//     public function testUpload(Request $request) {
//     if ($request->hasFile('image')) {
      
//         // $cloudinaryImage = $request->file('image')->storeOnCloudinary();
//       $cloudinaryImage = Cloudinary::upload($request->file('image')->getRealPath(), [
//     'resource_type' => 'image',
// ]); // Disable SSL verification
//         return response()->json(['url' =>  $cloudinaryImage->getSecurePath()]);
//         // return response()->json(['url' => $cloudinaryImage->getSecurePath()]);
//     }
//     return response()->json(['message' => 'No image uploaded'], 400);
// }
    public function updateUser(Request $request, $id)
    {

      
       $validated=  $request->validate([
    'first_name' => 'required|string|max:255',
    'last_name' => 'required|string|max:255',
    'username' => 'nullable|string|max:255',
    'telegram_id' => 'required|integer|min:1',
    'balance' => 'required|numeric|min:0',
    'earn_per_tap' => 'required|numeric|min:0',
    'available_energy' => 'required|integer|min:0',
    'max_energy' => 'required|integer|min:0',
    'production_per_hour' => 'nullable|numeric|min:0',
]);

        // Find the user by their ID
        $user = TelegramUser::findOrFail($id);

        // Update the user's information
        $user->update($validated);

        // Redirect back to the users list with a success message
        return response()->json(['message' => 'User updated successfully', 'user' => $user], 200);

        // return redirect()->route('admin.users')->with('success', 'User updated successfully.');
    }
public function deleteUser($id)
{
    $telegramUser = TelegramUser::findOrFail($id);
    
    // Optionally, detach related tasks or other relations
    // $telegramUser->tasks()->detach();
    
    // // Delete the user
    // $telegramUser->delete();

    DB::table('telegram_user_daily_tasks')->where('telegram_user_id', $telegramUser->id)->delete();
    DB::table('telegram_user_referral_task')->where('telegram_user_id', $telegramUser->id)->delete();
    DB::table('telegram_user_tasks')->where('telegram_user_id', $telegramUser->id)->delete();
    DB::table('telegram_user_missions')->where('telegram_user_id', $telegramUser->id)->delete();


    return response()->json(['message' => 'Telegram user and related tasks deleted successfully']);
}
    public function tasks()
    {
        $tasks = Task::all();
        return response()->json($tasks);
    }

    public function storeTask(Request $request)
    {
        // Validate the incoming request data
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'reward_coins' => 'required|numeric|min:0',
            'link' => 'nullable|url',
            'type' => 'required|string|max:50', // e.g., video, other
            'action_name' => 'nullable|string|max:255',
        ]);

        if ($request->hasFile('image')) {
           $imagePath = $request->file('image')->store('images/tasks', 'public');
           $validated['image'] = $imagePath; // Save the relative path
         };

         $task = Task::create($validated);

        // Return a success response
        return response()->json(['message' => 'Task created successfully', 'task' => $task], 201);
    }

    public function updateTask(Request $request, $id)
    {
        $task = Task::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'reward_coins' => 'required|numeric|min:0',
            'link' => 'nullable|url',
            'type' => 'required|string|max:50', // e.g., video, other
            'action_name' => 'nullable|string|max:255',
            'image' => 'nullable|image|max:2048', 
        ]);

        if ($request->hasFile('image')) {
        // Optionally, delete the old image if it exists
        if ($task->image) {
            Storage::disk('public')->delete($task->image);
        }

        $imagePath = $request->file('image')->store('images/tasks', 'public');
        $validated['image'] = $imagePath; // Save the new image path
    }
        $task->update($validated);

        return response()->json(['message' => 'Task updated successfully', 'task' => $task], 200);
    }

       public function deleteTask($id)
    {
        // Find the task by ID
        $task = Task::findOrFail($id);

        // If task has related Telegram Users, detach them (optional, depending on whether you want to remove relationships)
        $task->telegramUsers()->detach();

        // Delete the task
        $task->delete();

        // Return a response
        return response()->json(['message' => 'Task deleted successfully']);
    }


   public function missions()
   {
    // Fetch all data from the missions table along with the mission type's name
    $missions = DB::table('missions')
        ->join('mission_types', 'missions.mission_type_id', '=', 'mission_types.id')
        ->select(
            'missions.*',
            'mission_types.name as mission_type_name' // Get the mission type's name
        )
        ->get();

    // Return the data as a JSON response
    return response()->json($missions);
   }

   public function storeMission(Request $request)
   {
   try{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'mission_type_id' => 'required|exists:mission_types,id', // Ensure it references a valid mission type
        'image' => 'nullable|image|max:2048', 
        'required_user_level' => 'required|string|max:255', 
        'required_friends_invitation' => 'required|string|max:255', 
    ]); 

     if ($request->hasFile('image')) {


        if (!$request->file('image')->isValid()) {
            return response()->json(['message' => 'Invalid image file.'], 400);
         }
        // Upload image to Cloudinary
    
        //       $cloudinaryImage = Cloudinary::upload($request->file('image')->getRealPath(), [
        //         'resource_type' => 'image',
        //  ]);
        // $url=$cloudinaryImage->getSecurePath();
        $path = $request->file('image')->store('missions', 'public');
        $validated['image'] = env('APP_URL') . '/storage/' . $path;

       
        //   $validated['image'] = $url;
     
    }

    // Create a new mission
    $mission = Mission::create($validated);

    return response()->json(['message' => 'Mission added successfully', 'mission' => $mission]);
} catch (\Exception $e) {
    return response()->json(['message' => 'Failed to assign task to user', 'error' => $e->getMessage()], 500);
}
    // Redirect to the missions list with a success message
    // return redirect()->route('admin.missions')->with('success', 'Mission created successfully.');
}

    public function updateMission(Request $request, $id)
    {

        // Validate the incoming request data
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'image' => 'nullable|image|max:2048', // Optional image upload
            'mission_type_id' => 'nullable|exists:mission_types,id', // MissionType must exist
            'required_user_level' => 'nullable|string|max:255', // Validate image input
            'required_friends_invitation' => 'nullable|string|max:255', // Validate image input
        ]);
        // return response()->json(['message' => 'validated updated successfully', 'mission' => $request->all()]);
        // return response()->json($request->all()); 
        // Find the mission by ID
        $mission = Mission::findOrFail($id);

        // Handle image upload if present
       if ($request->hasFile('image')) {


        if (!$request->file('image')->isValid()) {
            return response()->json(['message' => 'Invalid image file.'], 400);
         }
        // Upload image to Cloudinary
    
        //       $cloudinaryImage = Cloudinary::upload($request->file('image')->getRealPath(), [
        //         'resource_type' => 'image',
        //  ]);
        // $url=$cloudinaryImage->getSecurePath();

       
        //   $validated['image'] = $url;

        $path = $request->file('image')->store('missions', 'public');
        $validated['image'] = env('APP_URL') . '/storage/' . $path;

       
     
    }
        // Update the mission with the validated data
        $mission->update($validated);

        // Return a success response
        return response()->json(['message' => 'Mission updated successfully', 'mission' => $mission]);
    }
     public function deleteMission($id)
    {
        // Find the mission by ID
        $mission = Mission::findOrFail($id);

        // Option 1: Automatically delete associated levels (this should work if `onDelete('cascade')` is set in your migration)
        // Option 2: 
        $mission->levels()->delete();

        // Delete the mission
        $mission->delete();

        // Return a response or redirect
        return response()->json(['message' => 'Mission and associated levels deleted successfully']);
    }
       // Delete a MissionType and associated Missions
    
public function mission_levels()
{
    // Fetch all data from the mission_levels table along with the mission's name
    $missionLevels = DB::table('mission_levels')
        ->join('missions', 'mission_levels.mission_id', '=', 'missions.id')
        ->select(
            'mission_levels.*',
            'missions.name as mission_name' // Get the mission's name
        )
        ->get();

    // Return the data as a JSON response
    return response()->json($missionLevels);
}

public function updateMissionLevels(Request $request, $id)
    {

      
    $validated=  $request->validate([
    'mission_id' => 'required|integer|exists:missions,id',
    'level' => 'required|numeric|max:255',
    'cost' => 'required|numeric|max:255',
    'production_per_hour' => 'nullable|numeric|min:0',

]);

     $missionLevel = MissionLevel::findOrFail($id);

    // Update the mission level with the validated data
    $missionLevel->update($validated);

    // Return a success response or redirect
    return response()->json(['message' => 'Mission level updated successfully']);
    }
public function storeMissionLevels(Request $request)
{
    // Validate the incoming request data
    $validated = $request->validate([
        'level' => 'required|integer|min:1',
        'cost' => 'required|integer|min:0',
        'production_per_hour' => 'required|integer|min:0',
        'mission_id' => 'required|integer|exists:missions,id', // Ensure the mission ID exists
    ]);

    // Create a new MissionLevel with the validated data
    $missionLevel = MissionLevel::create($validated);

    // Return a success response or redirect
    return response()->json(['message' => 'Mission level added successfully', 'mission_level' => $missionLevel]);
}
 public function deleteMissionLevel($id)
    {
        // Find the mission level by ID
        $missionLevel = MissionLevel::findOrFail($id);

        // Delete the mission level
        $missionLevel->delete();

        // Return a response
        return response()->json(['message' => 'Mission Level deleted successfully']);
    }

   public function mission_types()
   {
    // Fetch all missions from the Mission model
    $missionTypes = MissionType::all();

    // Return missions as a JSON response
    return response()->json($missionTypes);
   }  
    public function storeMissionType(Request $request)
    {
        // Validate the incoming request data
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        // Create a new mission type
        $missionType = MissionType::create($validated);

        // Return a success response
        return response()->json([
            'message' => 'Mission type added successfully',
            'mission_type' => $missionType
        ]);
    }

      public function updateMissionType(Request $request, $id)
    {
        // Validate the incoming request data
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        // Find the mission type by ID
        $missionType = MissionType::findOrFail($id);

        // Update the mission type with the validated data
        $missionType->update($validated);

        // Return a success response
        return response()->json([
            'message' => 'Mission type updated successfully',
            'mission_type' => $missionType
        ]);
    }

    public function deleteMissionType($id)
    {
        // Find the mission type by ID
        $missionType = MissionType::findOrFail($id);

        // Option 1: Delete all associated missions before deleting the mission type
        $missionType->missions()->delete();

        // Option 2: Use `onDelete('cascade')` in the migration file if you want to delete associated missions automatically (more efficient)
        // Now delete the mission type itself
        $missionType->delete();

        // Return a response or redirect
        return response()->json(['message' => 'Mission Type and associated missions deleted successfully']);
    }


public function telegram_user_tasks()
{
    // Fetch all data from the telegram_user_tasks table along with the task's name and telegram user's first name
    $telegramUserTasks = DB::table('telegram_user_tasks')
        ->join('tasks', 'telegram_user_tasks.task_id', '=', 'tasks.id')
        ->join('telegram_users', 'telegram_user_tasks.telegram_user_id', '=', 'telegram_users.id')
        ->select(
            'telegram_user_tasks.*',
            'tasks.name as task_name', // Get the task's name
            'telegram_users.first_name as user_first_name' // Get the telegram user's first name
        )
        ->get();
        $telegramUserTasks = $telegramUserTasks->map(function ($task) {
        $task->is_submitted = (bool) $task->is_submitted; // Cast to boolean
        $task->is_rewarded = (bool) $task->is_rewarded;   // Cast to boolean
        return $task;
    });

    // Return the data as a JSON response
    return response()->json($telegramUserTasks);
}
    public function storeTelegramUserTask(Request $request)
    {
        try {
        // Validate the incoming request data
        $validated = $request->validate([
            'telegram_user_id' => 'required|exists:telegram_users,id',
            'task_id' => 'required|exists:tasks,id',
            'is_submitted' => 'required|boolean', // Must be true or false
            'is_rewarded' => 'required|boolean', // Must be true or false
            'submitted_at' => 'nullable|date', // Can be null or a valid date
        ]);

        // $validated['is_submitted'] = $validated['is_submitted'] ? 1 : 0;
        // $validated['is_rewarded'] = $validated['is_rewarded'] ? 1 : 0;

        // Insert the new task record into the table
        DB::table('telegram_user_tasks')->insert([
            'telegram_user_id' => $validated['telegram_user_id'],
            'task_id' => $validated['task_id'],
            'is_submitted' => $validated['is_submitted'],
            'is_rewarded' => $validated['is_rewarded'],
            'submitted_at' => $validated['submitted_at'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Return a success response
        return response()->json(['message' => 'Task assigned to user successfully']);
    } catch (\Exception $e) {
    return response()->json(['message' => 'Failed to assign task to user', 'error' => $e->getMessage()], 500);
}
    }
    
     public function updateTelegramUserTask(Request $request, $id)
{
    // Validate the incoming request data
    $validated = $request->validate([
        'telegram_user_id' => 'required|exists:telegram_users,id',
        'task_id' => 'required|exists:tasks,id',
        'is_submitted' => 'required|boolean', // Must be true or false
        'is_rewarded' => 'required|boolean',  // Must be true or false
    ]);

    // Update the existing task record in the table
    $updated = DB::table('telegram_user_tasks')
        ->where('id', $id) // Match by the primary 'id' column in the table
        ->update([
            'telegram_user_id'=>$validated['telegram_user_id'],
            'task_id'=>$validated['task_id'],
            'is_submitted' => $validated['is_submitted'],
            'is_rewarded' => $validated['is_rewarded'],
            // 'updated_at' => now(), // Update the timestamp
        ]);

    // Check if the update was successful
    if ($updated) {
        return response()->json(['message' => 'Task updated successfully']);
    } else {
        return response()->json(['message' => 'No task found to update'], 404);
    }
}

    public function deleteTelegramUserTask($id)
    {
        // Find the record by ID and delete it
        $deleted = DB::table('telegram_user_tasks')->where('id', $id)->delete();

        if ($deleted) {
            // Return success message if the record was deleted
            return response()->json(['message' => 'Record deleted successfully']);
        } else {
            // Return error message if the record was not found
            return response()->json(['message' => 'Record not found'], 404);
        }
    }


    public function createTask()
    {
        return view('create_task');
    }

    // public function storeTask(Request $request)
    // {
    //     $validated = $request->validate([
    //         'name' => 'required|string|max:255',
    //         'description' => 'required|string',
    //         'reward_coins' => 'required|integer|min:1',
    //     ]);

    //     Task::create($validated);

    //     return redirect()->route('tasks')->with('success', 'Task created successfully');
    // }

    public function dailyTasks()
    {
        $dailyTasks = DailyTask::all();
        return view('daily_tasks', compact('dailyTasks'));
    }

    public function createDailyTask()
    {
        return view('create_daily_task');
    }

    public function storeDailyTask(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'required_login_streak' => 'required|integer|min:1|max:10',
            'reward_coins' => 'required|integer|min:1',
        ]);

        DailyTask::create($validated);

        return redirect()->route('daily_tasks')->with('success', 'Daily task created successfully');
    }

    public function editTask(Task $task)
    {
        return view('tasks.edit', compact('task'));
    }

    // public function updateTask(Request $request, Task $task)
    // {
    //     $validated = $request->validate([
    //         'name' => 'required|string|max:255',
    //         'description' => 'required|string',
    //         'required_taps' => 'required|integer|min:0',
    //         'reward_coins' => 'required|integer|min:1',
    //     ]);

    //     $task->update($validated);

    //     return redirect()->route('tasks')->with('success', 'Task updated successfully');
    // }

  
    public function editDailyTask(DailyTask $dailyTask)
    {
        return view('daily_tasks.edit', compact('dailyTask'));
    }

    public function updateDailyTask(Request $request, DailyTask $dailyTask)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'required_login_streak' => 'required|integer|min:1|max:10',
            'reward_coins' => 'required|integer|min:1',
        ]);

        $dailyTask->update($validated);

        return redirect()->route('daily_tasks')->with('success', 'Daily task updated successfully');
    }

    public function deleteDailyTask(DailyTask $dailyTask)
    {
        $dailyTask->delete();
        return redirect()->route('daily_tasks')->with('success', 'Daily task deleted successfully');
    }
}