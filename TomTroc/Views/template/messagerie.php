<link rel="stylesheet" href="assets/css/messagerie.css">

<section class="message-container">
    <div class="messagerie-wrapper">
        <div class="conversation-list">
            <h1>Messagerie</h1>
            <?php if (!empty($conversations)): ?>
                <ul class="conversations-ul">
                    <?php foreach ($conversations as $conversation): ?>
                        <li class="conversation-item <?= isset($currentConversation) && $currentConversation->getId() == $conversation->getId() ? 'active' : '' ?>">
                            <a href="index.php?action=messagerie&user_id=<?= (int)$conversation->getId() ?>">
                                <div class="user-name">
                                    <img src="<?= !empty($conversation->getAvatar()) ? htmlspecialchars($conversation->getAvatar()) : 'assets/users/default-avatar.png' ?>"
                                        alt="Photo de profil"
                                        class="owner-avatar">
                                    <div class="conv-info">
                                        <div style="display:flex; align-items:center; gap:8px; width:100%">
                                            <div class="conv-name"><?= htmlspecialchars($conversation->getNickname()) ?></div>
                                            <?php if (!empty($conversation->getLastTime())): ?>
                                                <div class="conv-time" style="margin-left:auto; font-size:0.75rem; color:var(--text-muted)"><?= date('H:i', strtotime($conversation->getLastTime())) ?></div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="conv-preview"><?= htmlspecialchars(mb_strimwidth($conversation->getLastMessage(), 0, 60, '...')) ?></div>
                                    </div>
                                </div>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p class="no-conversations">Aucune conversation pour le moment.</p>
            <?php endif; ?>
        </div>

        <div class="message-section">
            <?php if (isset($currentConversation) && $currentConversation): ?>
                <div class="message-header">
                    <img src="<?= !empty($currentConversation->getAvatar()) ? htmlspecialchars($currentConversation->getAvatar()) : 'assets/users/default-avatar.png' ?>"
                        alt="Photo de profil"
                        class="owner-avatar"><strong><?= htmlspecialchars($currentConversation->getNickname()) ?></strong>
                </div>

                <div class="messages-list">
                    <?php if (!empty($messages)): ?>
                        <?php foreach ($messages as $message): ?>
                            <div class="message-item <?= $message->getSenderId() === $currentUserId ? 'sent' : 'received' ?>">
                                <div class="message-content">
                                    <span class="message-time">
                                        <?= date('d/m/Y H:i', strtotime($message->getCreatedAt())) ?>
                                    </span>
                                    <p><?= htmlspecialchars($message->getContent()) ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="no-messages">Aucun message pour le moment. Commencez la conversation!</p>
                    <?php endif; ?>
                </div>

                <form method="POST" action="index.php?action=sendMessage&receiver_id=<?= (int)$currentConversation->getId() ?>" class="message-form">
                    <div class="form-group-message">
                        <textarea
                            name="content"
                            placeholder="Tapez votre message ici"
                            required
                            class="message-input"
                            rows="3"></textarea>
                    </div>
                    <button type="submit" class="btn btn-send">Envoyer</button>
                </form>
            <?php else: ?>
                <div class="empty-state">
                    <p>📭 Sélectionnez une conversation pour voir les messages</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>