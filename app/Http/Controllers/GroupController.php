<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\UserGroup;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        $groups = $user->groups;
        return response()->json([
            'status' => 'success',
            'message' => 'groups returned succesfully',
            'groups' => $groups
        ], 200);
    }

    public function store(Request $request)
    {
        $validate = $request->validate([
            'name' => 'string|required',
        ]);
        $group = new Group();
        $group->name = $validate['name'];
        $group->creator_id = auth()->user()->id;
        $group->save();
        if ($request->has('members')) {
            foreach ($request->input('members') as $member) {
                if (!(auth()->user()->friends->contains('id', $member))) {
                    return response()->json([
                        'status' => 'failed',
                        'message' => 'you can only add your friends to the group',
                    ], 401);
                }
            }
            $members = $request->input('members', []);
            $group->members()->attach($members); 
        }
        $group->members()->syncWithoutDetaching(auth()->user()->id); 
        return response()->json([
            'status' => 'group created successfully',
            'group' => $group,
        ], 200);
    }
    public function addMembersToGroup(Request $request, string $id)
    {
        $group = Group::where('id', $id)->first();
        if (auth()->user()->id != $group->creator_id || !($group->members->contains(auth()->user()->id))) {
            return response()->json([
                'status' => 'failed',
                'message' => 'you can only add your friends to a group you are a part of',
            ], 401);
        }
        foreach ($request->input('members') as $member) {
            if (!(auth()->user()->friends->contains('id', $member))) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'you can only add your friends to the group',
                ], 401);
            }
        }
        $group->members()->syncWithoutDetaching($request->input('members'));
        return response()->json([
            'status' => 'members added',
            'group' => $group,
        ], 200);
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Retrieve the group with its tasks and assigned users
        $group = Group::with(['tasks.assignedUsers', 'members'])->find($id);

        if (!$group) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Group not found'
            ], 404);
        }

        // Return the group, tasks, and assigned users in the response
        return response()->json([
            'group' => $group,
        ], 200);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $group = Group::where('id', $id)->first();
        $validate = $request->validate([
            'name' => 'string',
        ]);
        $group->name = $validate['name'];
        $group->save();
        return response()->json([
            'status' => 'success',
            'message' => 'updated',
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $group = Group::where('id', $id)->first();
        if (auth()->user()->id == $group->creator_id) {
            $group->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'deleted'
            ]);
        }
    }
}
