<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\Chat;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    // Function to send a message
    public function sendMessage(Request $request)
    {
         // Validate incoming request data
         $request->validate([
            'content' => 'required|string',
            // 'chatId' => 'required|integer|exists:chats,id',
            'chatId' => 'required|integer',
        ]);

        // Get the content and chatId from the request
        $content = $request->input('content');
        $chatId = $request->input('chatId');

        // Create new message
        $newMessage = new Message();
        // $newMessage->sender_id = Auth::id(); // Get the authenticated user's ID
        $newMessage->sender_id = 1;
        $newMessage->content = $content;
        $newMessage->chat_id = $chatId;
        $newMessage->save();

        // Populate additional fields (sender and chat)
        // $newMessage->load('sender:id,name,pic');
        // $newMessage->load('chat');

        // Update the latest message in the chat
        // $chat = Chat::findOrFail($chatId);
        // $chat->latest_message_id = $newMessage->id;
        // $chat->save();

        // Return the new message as a JSON response
        return response()->json($newMessage, 201);
    }
}