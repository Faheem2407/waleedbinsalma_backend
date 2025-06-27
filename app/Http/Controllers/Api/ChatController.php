<?php
namespace App\Http\Controllers\Api;

use App\Models\Message;
use App\Models\OnlineStore;
use App\Traits\ApiResponse;
use App\Models\Conversation;
use Illuminate\Http\Request;
use App\Events\MessageSentEvent;
use App\Events\LatestMassageEvent;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ChatController extends Controller
{
    use ApiResponse;

    public function conversations(Request $request)
    {
        $user = auth()->user();

        $query = $user->conversations()
        ->with(['sender:id,first_name,last_name,avatar', 'receiver:id,first_name,last_name,avatar', 'lastMessage:id,sender_id,receiver_id,conversation_id,message,type,is_read,created_at']);

        if($request->has('search')) {
            $query->whereHas('sender', function ($query) use ($request) {
                $query->where('first_name', 'like', '%' . $request->search . '%');
            });
            $query->orWhereHas('receiver', function ($query) use ($request) {
                $query->where('first_name', 'like', '%' . $request->search . '%');
            });
        }

        $data = $query ->orderBy('updated_at', 'desc')->get();

        if ($data->isEmpty()) {
            return $this->error([], 'No conversations found', 404);
        }

        return $this->success($data, 'Conversations fetched successfully.', 200);
    }

    public function getChat($id)
    {
        $user = auth()->user();

        $business = OnlineStore::where('id', $id)->first();

        if (! $business) {
            return $this->error([], 'Business not found', 404);
        }

        $messages = Message::with('sender:id,first_name,last_name,avatar', 'receiver:id,first_name,last_name,avatar')
        ->select('id', 'sender_id', 'receiver_id', 'message', 'is_read', 'file_path', 'file_type', 'type', 'created_at')
        ->where(function ($query) use ($user, $business) {

            $query->where('sender_id', $user->id)
                ->where('receiver_id', $business->businessProfile->user->id);

        })->orWhere(function ($query) use ($user, $business) {

            $query->where('sender_id', $business->businessProfile->user->id)
                ->where('receiver_id', $user->id);

        })->orderBy('created_at', 'asc')->get();

        // Mark messages as read
        Message::where('sender_id', $business->businessProfile->user->id)
            ->where('receiver_id', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        if ($messages->isEmpty()) {
            return $this->error([], 'No messages found', 404);
        }

        return $this->success($messages, 'Messages fetched successfully.', 200);
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

        // $business = OnlineStore::where('id', $id)->first();

        // if (! $business) {
        //     return $this->error([], 'Business not found', 404);
        // }

        try {
            DB::beginTransaction();

            $conversations = Conversation::where(function ($query) use ($user, $id) {
                $query->where('sender_id', $user->id)
                    ->where('receiver_id', $id);
            })->orWhere(function ($query) use ($user, $id) {
                $query->where('sender_id', $id)
                    ->where('receiver_id', $user->id);
            })->first();

            if (!$conversations) {
                $conversations = Conversation::create([
                    'sender_id'   => $user->id,
                    'receiver_id' => $id,
                    'type'        => 'private',
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
            broadcast(new LatestMassageEvent($data->sender_id, $data->receiver_id, $data , $unreadMessageCount))->toOthers();

            DB::commit();
            return $this->success($data, 'Message sent successfully.', 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error([], $e->getMessage(), 404);
        }
    }
}
