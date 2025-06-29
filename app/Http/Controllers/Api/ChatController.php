<?php
namespace App\Http\Controllers\Api;

use App\Events\LatestMassageEvent;
use App\Events\MessageSentEvent;
use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ChatController extends Controller
{
    use ApiResponse;

    public function conversations(Request $request)
    {
        $user = auth()->user();

        $query = $user->conversations()
            ->with([
                'participants' => function ($query) {
                    $query->with('user:id,first_name,last_name,avatar')->where('user_id', '!=', auth()->id());
                },
                'lastMessage:id,sender_id,receiver_id,conversation_id,message,type,is_read,created_at'
            ]);

        // Search by participant's name (excluding self)
        if ($request->has('search')) {
            $search = $request->search;

            $query->whereHas('participants.user', function ($q) use ($search, $user) {
                $q->where('first_name', 'like', '%' . $search . '%')
                ->where('id', '!=', $user->id);
            });
        }

        $data = $query->latest()->get();


        if ($data->isEmpty()) {
            return $this->error([], 'No conversations found', 404);
        }

        return $this->success($data, 'Conversations fetched successfully.', 200);
    }

    public function getChat(int $id)
    {
        $user = auth()->user();

        $receiverUser = User::find($id);

        if (!$receiverUser) {
            return $this->error([], 'User not found', 200);
        }

        Message::where('sender_id', $id)
            ->where('receiver_id', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $messages = Message::with('sender:id,first_name,last_name,avatar', 'receiver:id,first_name,last_name,avatar')
            ->select('id', 'sender_id', 'receiver_id', 'message', 'is_read', 'file_path', 'file_type', 'type', 'created_at')
            ->where(function ($query) use ($user, $id) {

                $query->where('sender_id', $user->id)
                    ->where('receiver_id', $id);

            })->orWhere(function ($query) use ($user, $id) {

            $query->where('sender_id', $id)
                ->where('receiver_id', $user->id);

        })->orderBy('created_at', 'asc')->get();

        
        if ($messages->isEmpty()) {
            return $this->error([], 'No messages found', 200);
        }

        $response = [
            'user'     => $receiverUser,
            'messages' => $messages
        ];

        return $this->success($response, 'Messages fetched successfully.', 200);
    }

    public function sendMessage(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'nullable|string',
            'file'    => 'nullable|max:5120',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }

        $user = auth()->user();

        try {
            DB::beginTransaction();

            $conversations = Conversation::whereHas('participants', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                })
                ->whereHas('participants', function ($q) use ($id) {
                    $q->where('user_id', $id);
                })
                ->where('type', 'private')
                ->first();

            if (! $conversations) {
                $conversations = Conversation::create([
                    'type' => 'private',
                ]);

                $conversations->participants()->createMany([
                    ['user_id' => $user->id],
                    ['user_id' => $id],
                ]);
            }

            
            if ($request->hasFile('file')) {
                $fileName         = time() . '.' . $request->file('file')->getClientOriginalExtension();
                $fileOriginalName = $request->file('file')->getClientOriginalName();
                $filePath         = $request->file('file')->storeAs('images', $fileName, 'public');
                $fileType         = $request->file('file')->getClientMimeType();

                $data = Message::create([
                    'sender_id'       => $user->id,
                    'receiver_id'     => $id,
                    'conversation_id' => $conversations->id,
                    'file_name'       => $fileName,
                    'original_name'   => $fileOriginalName,
                    'file_path'       => $filePath,
                    'file_type'       => $fileType,
                    'type'            => 'file',
                    'is_read'         => false,
                ]);
            } else {
                $data = Message::create([
                    'sender_id'       => $user->id,
                    'receiver_id'     => $id,
                    'conversation_id' => $conversations->id,
                    'message'         => $request->message,
                    'type'            => 'text',
                    'is_read'         => false,
                ]);
            }

            if (! $data) {
                return $this->error([], 'Message not sent', 404);
            }

            $unreadMessageCount = Message::where('receiver_id', $id)->where('is_read', false)->count();

            // # Broadcast the message
            broadcast(new MessageSentEvent($data));

            // # Broadcast the unread message
            broadcast(new LatestMassageEvent($data->sender_id, $data->receiver_id, $data, $unreadMessageCount))->toOthers();

            DB::commit();
            return $this->success($data, 'Message sent successfully.', 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error([], $e->getMessage(), 404);
        }
    }
}
