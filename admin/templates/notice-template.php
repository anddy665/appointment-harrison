<?php if (!empty($message) && !empty($class)) : ?>
    <div class="notice <?= esc_attr($class); ?>">
        <p><?= esc_html($message); ?></p>
    </div>
<?php endif; ?>