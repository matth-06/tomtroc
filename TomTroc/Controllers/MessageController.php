<?php
require_once __DIR__ . '/../Models/Message.php';
require_once __DIR__ . '/../Models/MessageRepository.php';
require_once __DIR__ . '/Controller.php';

class MessageController extends Controller
{
    /**
     * Send a message to another user
     */
    public function sendMessage(int $receiverId, string $content): void
    {
        if (empty($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit();
        }

        $senderId = (int)$_SESSION['user_id'];
        
        if (empty(trim($content))) {
            header('Location: index.php?action=messagerie&user_id=' . $receiverId);
            exit();
        }

        $repository = new MessageRepository();
        $repository->create($senderId, $receiverId, trim($content));
        header('Location: index.php?action=messagerie&user_id=' . $receiverId);
        exit();
    }

    /**
     * Display messagerie with conversations list and current conversation
     */
    public function messagerie(?int $otherUserId = null): void
    {
        if (empty($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit();
        }

        $userId = (int)$_SESSION['user_id'];
        $title = 'Messagerie';
        
        // Get all conversations for this user
        $repository = new MessageRepository();
        $conversations = $repository->getConversations($userId);
        
        $messages = [];
        $currentConversation = null;
        
        // If a specific conversation is selected
        if ($otherUserId) {
            $messages = $repository->getConversation($userId, $otherUserId);
            
            $repository->markAsRead($userId, $otherUserId);
            $currentConversation = $repository->findConversationUser($userId, $otherUserId);
        }

        $currentUserId = $userId;
        $this->render('template/messagerie.php', compact('title', 'conversations', 'messages', 'currentConversation', 'currentUserId'));
    }
}
?>