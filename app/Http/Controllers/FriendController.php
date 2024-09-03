<?php

namespace App\Http\Controllers;

use App\Models\Friend;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FriendController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function friends()
    {
        $user = auth()->user();
        $friends = $user->friends;
        return response()->json([
            'status' => 'success',
            'friends' => $friends
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function sendFriendRequest(string $id)
    {
        $sender = auth()->user();
        $receiver = User::find($id);
        if ($sender->id == $receiver->id) {
            return response()->json([
                'status' => 'failed',
                'message' => 'You cannot send a friend request to yourself',
            ], 400);
        }
        if ($sender->friends->contains('id', $id)) {
            return response()->json([
                'status' => 'failed',
                'message' => 'You are already friends',
            ], 401);
        }
        if (
            $sender->friendRequestsSent()->where('receiver_id', $id)->exists() ||
            $receiver->friendRequestsReceived()->where('sender_id', $sender->id)->exists()
        ) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Friend request already sent',
            ], 400);
        }

        DB::table('friend_request')->insert([
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Friend request sent',
        ], 200);
    }

    public function receivedFriendRequests()
    {
        $user = auth()->user();
        $receivedRequests = $user->friendRequestsReceived;
        $response = $receivedRequests->map(function ($request) {
            $sender = User::find($request->pivot->sender_id);
            $receiver = User::find($request->pivot->receiver_id);

            return [
                'id' => $request->id,
                'sender' => [
                    'id' => $sender->id,
                    'name' => $sender->name,
                    'email' => $sender->email,
                ],
                'receiver' => [
                    'id' => $receiver->id,
                    'name' => $receiver->name,
                    'email' => $receiver->email,
                ],
                'created_at' => $request->created_at,
            ];
        });

        return response()->json([
            'status' => 'success',
            'received' => $response,
        ], 200);
    }

    public function accept(string $id)
    {
        $user = auth()->user();
        $friendRequest = $user->friendRequestsReceived()->findOrFail($id);
        $friend = new Friend();
        $friend->user_id = $user->id;
        $friend->friend_id = $friendRequest->pivot->sender_id;
        $friend->save();
        $friendInverse = new Friend();
        $friendInverse->user_id = $friendRequest->pivot->sender_id;
        $friendInverse->friend_id = $user->id;
        $friendInverse->save();
        $friendRequest->pivot->delete();
        return response()->json(['friend' => $friend], 200);
    }

    public function decline(string $id) {
        $friendRequest = $user->friendRequestsReceived()->findOrFail($id);
        $friendRequest->pivot->delete();
        return response()->json([
            'status'=>'success',
            'message'=>'declined',
        ],200);
    }
}
