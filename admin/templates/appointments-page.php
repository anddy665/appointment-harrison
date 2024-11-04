<div class="wrap">
    <h1>Appointments Page</h1>

    <?php
    global $wpdb;


    if (isset($_POST['update_appointment'])) {
        $edit_id = intval($_POST['edit_id']);
        $full_name = sanitize_text_field($_POST['full_name']);
        $email = sanitize_email($_POST['email']);
        $phone = sanitize_text_field($_POST['phone']);
        $appointment_datetime = sanitize_text_field($_POST['appointment_datetime']);
        $description = sanitize_textarea_field($_POST['description']);

        $wpdb->update(
            "{$wpdb->prefix}appointments",
            [
                'full_name' => $full_name,
                'email' => $email,
                'phone' => $phone,
                'appointment_date' => $appointment_datetime,
                'description' => $description
            ],
            ['id' => $edit_id],
            ['%s', '%s', '%s', '%s', '%s'],
            ['%d']
        );


        wp_redirect(admin_url('admin.php?page=appointments'));
    }


    if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
        $delete_id = intval($_GET['id']);
        $wpdb->delete("{$wpdb->prefix}appointments", ['id' => $delete_id], ['%d']);


        wp_redirect(admin_url('admin.php?page=appointments'));
    }


    $appointments = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}appointments");


    if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
        $edit_id = intval($_GET['id']);
        $appointment_to_edit = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}appointments WHERE id = %d", $edit_id));
    }
    ?>

    <?php if (isset($appointment_to_edit)): ?>
        <h2>Editar Cita</h2>
        <form method="POST" action="">
            <input type="hidden" name="edit_id" value="<?= $appointment_to_edit->id; ?>">
            <label>Nombre Completo:</label>
            <input type="text" name="full_name" value="<?= esc_attr($appointment_to_edit->full_name); ?>" required>

            <label>Email:</label>
            <input type="email" name="email" value="<?= esc_attr($appointment_to_edit->email); ?>" required>

            <label>Teléfono:</label>
            <input type="text" name="phone" value="<?= esc_attr($appointment_to_edit->phone); ?>" required>

            <label>Fecha y Hora de Cita:</label>
            <input type="datetime-local" name="appointment_datetime" value="<?= esc_attr(date('Y-m-d\TH:i', strtotime($appointment_to_edit->appointment_date))); ?>" required>

            <label>Descripción:</label>
            <textarea name="description" required><?= esc_textarea($appointment_to_edit->description); ?></textarea>

            <input type="submit" name="update_appointment" value="Actualizar Cita">
            <a href="<?= admin_url('admin.php?page=appointments'); ?>" class="button">Cancelar</a>
        </form>
    <?php endif; ?>

    <?php if (!empty($appointments)): ?>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre Completo</th>
                    <th>Email</th>
                    <th>Teléfono</th>
                    <th>Fecha y Hora de Cita</th>
                    <th>Descripción</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($appointments as $appointment) : ?>
                    <tr>
                        <td><?= esc_html($appointment->id); ?></td>
                        <td><?= esc_html($appointment->full_name); ?></td>
                        <td><?= esc_html($appointment->email); ?></td>
                        <td><?= esc_html($appointment->phone); ?></td>
                        <td><?= esc_html(date('Y-m-d H:i', strtotime($appointment->appointment_date))); ?></td>
                        <td><?= esc_html($appointment->description); ?></td>
                        <td>
                            <a href="?page=appointments&action=edit&id=<?= esc_attr($appointment->id); ?>" class="button">Editar</a>
                            <a href="?page=appointments&action=delete&id=<?= esc_attr($appointment->id); ?>" class="button" onclick="return confirm('¿Estás seguro de que deseas eliminar esta cita?');">Eliminar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No hay citas registradas.</p>
    <?php endif; ?>
</div>