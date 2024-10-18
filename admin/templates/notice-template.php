<?php
if (!empty($message) && !empty($class)) : ?>
    <div class="notice <?php echo esc_attr($class); ?>">
        <p><?php echo esc_html($message); ?></p>
    </div>
<?php endif; ?>
