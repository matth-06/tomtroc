<link rel="stylesheet" href="assets/css/messagerie.css">

<section class="message-container">
    <div class="messagerie-wrapper">
        <div class="conversation-list">
            <h2>Messagerie</h2>
            <?php if (!empty($conversations)): ?>
                <ul class="conversations-ul">
                    <?php foreach ($conversations as $conversation): ?>
                        <li class="conversation-item <?= isset($currentConversation) && $currentConversation['id'] == $conversation['id'] ? 'active' : '' ?>">
                            <a href="index.php?action=messagerie&user_id=<?= (int)$conversation['id'] ?>">
                                <div class="user-name">
                                    <img src="<?= !empty($conversation['avatar']) ? htmlspecialchars($conversation['avatar']) : 'assets/users/default-avatar.png' ?>"
                                        alt="Photo de profil"
                                        class="owner-avatar">
                                    <div class="conv-info">
                                        <div style="display:flex; align-items:center; gap:8px; width:100%">
                                            <div class="conv-name"><?= htmlspecialchars($conversation['nickname']) ?></div>
                                            <?php if (!empty($conversation['last_time'])): ?>
                                                <div class="conv-time" style="margin-left:auto; font-size:0.75rem; color:var(--text-muted)"><?= date('H:i', strtotime($conversation['last_time'])) ?></div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="conv-preview"><?= htmlspecialchars(mb_strimwidth($conversation['last_message'], 0, 60, '...')) ?></div>
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
                    <img src="<?= !empty($currentConversation['avatar']) ? htmlspecialchars($currentConversation['avatar']) : 'assets/users/default-avatar.png' ?>"
                        alt="Photo de profil"
                        class="owner-avatar"><strong><?= htmlspecialchars($currentConversation['nickname']) ?></strong>
                </div>

                <div class="messages-list">
                    <?php if (!empty($messages)): ?>
                        <?php foreach ($messages as $message): ?>
                            <div class="message-item <?= $message['sender_id'] === $_SESSION['user_id'] ? 'sent' : 'received' ?>">
                                <div class="message-content">
                                    <span class="message-time">
                                        <?= date('d/m/Y H:i', strtotime($message['created_at'])) ?>
                                    </span>
                                    <p><?= htmlspecialchars($message['content']) ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="no-messages">Aucun message pour le moment. Commencez la conversation!</p>
                    <?php endif; ?>
                </div>

                <form method="POST" action="index.php?action=sendMessage&receiver_id=<?= (int)$currentConversation['id'] ?>" class="message-form">
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