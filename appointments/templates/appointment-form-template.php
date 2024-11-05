<?php
if (!defined('ABSPATH')) {
    exit;
}

global $wpdb;
$schedules = $wpdb->get_results("SELECT id, schedule_date, start_time, end_time FROM {$wpdb->prefix}schedules");
?>

<div class="wrap">
    <h1>Book an Appointment</h1>

    <form id="appointment-form" method="POST">
        <?php wp_nonce_field('submit_appointment_form', 'appointment_form_nonce'); ?>
        <input type="text" name="full_name" placeholder="Full Name" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="text" name="phone" placeholder="Phone" required>
        <textarea name="description" placeholder="Description"></textarea>

        <!-- Selección de Fecha -->
        <label for="appointment_date">Select Date:</label>
        <input type="date" id="appointment_date" name="appointment_date" required>

        <!-- Selección de Hora de Inicio -->
        <label for="start_time">Start Time:</label>
        <input type="time" id="start_time" name="start_time" required>

        <!-- Selección de Hora de Fin -->
        <label for="end_time">End Time:</label>
        <input type="time" id="end_time" name="end_time" required>

        <button type="submit">Book Appointment</button>
    </form>
</div>

<script>

document.getElementById('appointment_date').addEventListener('change', function() {
    // Obtener el día seleccionado y calcular el día de la semana
    const selectedDate = new Date(this.value);
    const selectedDay = selectedDate.getDay(); // 0: Sunday, 1: Monday, ..., 6: Saturday
    
    // Traer los días de la semana disponibles desde PHP y convertirlos a números
    const availableDays = <?php echo json_encode(array_map('intval', array_column($schedules, 'schedule_date'))); ?>;

    // Verificar si al menos uno de los días disponibles coincide con el día seleccionado
    const isAvailable = availableDays.some(day => day === selectedDay);

    console.log(availableDays,"disponibles");
    console.log(selectedDay,"seleccionado");

    if (!isAvailable) {
        alert("Selected date is not available for appointments. Please choose another date.");
        this.value = ''; // Resetea el campo de fecha
    }
});

</script>
