<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Chat\Conversation;

Broadcast::channel('chat.{conversationId}', function ($user, $conversationId) {
    // Custom auth logic for session-based auth
    // In L11 with custom auth, $user might be generic or needs mapping.
    // However, the channel callback receives the authenticated user instance.
    // Since we use custom session auth, standard Broadcast might fail to resolve user automatically
    // unless we use 'auth:sanctum' or similar. 
    
    // BUT, for this to work with our custom 'session' auth, we might need a custom driver or 
    // ensure the 'web' guard picks it up.
    
    // For now, let's assume the user is injected if logged in via web.
    
    // Check if user is participant
    $conversation = Conversation::find($conversationId);
    if (!$conversation) return false;

    // We need to match the user to the participant.
    // $user is the model instance (Admin, Professor, Student).
    
    // Helper to get type from class
    $userType = '';
    if ($user instanceof \App\Models\Administrador) $userType = \App\Models\Administrador::class;
    elseif ($user instanceof \App\Models\Profesor) $userType = \App\Models\Profesor::class;
    elseif ($user instanceof \App\Models\Estudiante) $userType = \App\Models\Estudiante::class;

    return $conversation->participants()
        ->where('participant_id', $user->id)
        ->where('participant_type', $userType)
        ->exists();
});
