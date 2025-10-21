<?php
session_start();
require 'db.php';

// ✅ Redirect if not logged in
if (!isset($_SESSION['staff_logged_in']) || $_SESSION['staff_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

$username = $_SESSION['auth_user'];
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// ✅ Fetch doctors list
$doctors = $pdo->query("SELECT * FROM doctors ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

// ✅ Initialize messages
$messages = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ➕ Add Doctor
    if (isset($_POST['add_doctor'])) {
        $name = trim($_POST['doctor_name'] ?? '');
        $specialty = trim($_POST['specialty'] ?? '');
        $availability = trim($_POST['availability'] ?? '');

        if ($name === '') {
            $messages[] = ['⚠ Doctor name cannot be empty.', 'warning'];
        } else {
            $stmt = $pdo->prepare("INSERT INTO doctors (name, specialty, availability) VALUES (?, ?, ?)");
            $stmt->execute([$name, $specialty, $availability]);
            $messages[] = ["✅ Doctor '$name' added successfully.", 'success'];
            $doctors = $pdo->query("SELECT * FROM doctors ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
        }
    }

    // 📅 Schedule Appointment
    if (isset($_POST['schedule_appointment'])) {
        $doctor_id = $_POST['doctor_id'] ?? '';
        $patient_name = trim($_POST['patient_name'] ?? '');
        $appointment_date = $_POST['appointment_date'] ?? '';

        if ($doctor_id && $patient_name !== '' && $appointment_date !== '') {
            $stmt = $pdo->prepare("INSERT INTO appointments (doctor_id, patient_name, appointment_date) VALUES (?, ?, ?)");
            $stmt->execute([$doctor_id, $patient_name, $appointment_date]);
            $messages[] = ["✅ Appointment scheduled successfully for $patient_name.", 'success'];
        } else {
            $messages[] = ["⚠ Please fill all appointment fields.", 'warning'];
        }
    }

    // 🩺 Record Visit
    if (isset($_POST['record_visit'])) {
        $doctor_id = $_POST['visit_doctor_id'] ?? '';
        $patient_name = trim($_POST['visit_patient_name'] ?? '');
        $visit_date = $_POST['visit_date'] ?? '';
        $notes = trim($_POST['visit_notes'] ?? '');

        if ($doctor_id && $patient_name !== '' && $visit_date !== '') {
            $stmt = $pdo->prepare("INSERT INTO patient_visits (patient_name, doctor_id, visit_date, notes) VALUES (?, ?, ?, ?)");
            $stmt->execute([$patient_name, $doctor_id, $visit_date, $notes]);
            $messages[] = ["✅ Patient visit recorded successfully for $patient_name.", 'success'];
        } else {
            $messages[] = ["⚠ Please fill all patient visit fields.", 'warning'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Staff Dashboard</title>
<style>
  @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap');
  body {
    font-family: 'Montserrat', sans-serif;
    background: #f0f4fa;
    margin: 0;
    padding: 0;
  }
  header {
    background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
    color: white;
    padding: 20px 40px;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  header h1 {
    margin: 0;
    font-weight: 600;
    letter-spacing: 1px;
  }
  .logout-link {
    background: white;
    color: #2575fc;
    padding: 8px 15px;
    border-radius: 6px;
    font-weight: bold;
    text-decoration: none;
    transition: 0.3s;
  }
  .logout-link:hover {
    background: #e6edff;
  }

  main {
    padding: 30px 10%;
  }

  .messages {
    margin: 20px 0;
  }
  .msg {
    padding: 12px 18px;
    border-radius: 8px;
    margin-bottom: 10px;
    font-weight: 500;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
  }
  .msg.success {
    background: #eaf6ea;
    color: #155724;
    border: 1px solid #c3e6cb;
  }
  .msg.warning {
    background: #fff3cd;
    color: #856404;
    border: 1px solid #ffeeba;
  }

  section {
    background: white;
    padding: 25px 30px;
    margin-bottom: 30px;
    border-radius: 10px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.05);
  }
  section h2 {
    margin-top: 0;
    color: #2575fc;
    font-weight: 600;
  }
  form label {
    display: block;
    margin-top: 10px;
    font-weight: bold;
  }
  form input, form select, form textarea {
    width: 100%;
    padding: 10px 12px;
    margin-top: 5px;
    border-radius: 6px;
    border: 1px solid #ccc;
    font-size: 14px;
    transition: border 0.2s;
  }
  form input:focus, form select:focus, form textarea:focus {
    border-color: #2575fc;
    outline: none;
  }
  form textarea {
    resize: vertical;
  }
  form button {
    margin-top: 15px;
    padding: 12px 20px;
    background: #2575fc;
    border: none;
    color: white;
    font-weight: bold;
    border-radius: 8px;
    cursor: pointer;
    transition: 0.3s;
  }
  form button:hover {
    background: #1b56d9;
  }

  @media (max-width: 768px) {
    main {
      padding: 20px;
    }
  }
</style>
</head>
<body>

<header>
  <h1>Welcome, <?= htmlspecialchars($username) ?> 👋</h1>
  <a href="logout.php" class="logout-link">Logout</a>
</header>

<main>
  <?php if ($messages): ?>
    <div class="messages">
      <?php foreach ($messages as $msg): ?>
        <div class="msg <?= htmlspecialchars($msg[1]) ?>"><?= htmlspecialchars($msg[0]) ?></div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <section>
    <h2>Add Doctor & Availability</h2>
    <form method="POST">
      <label for="doctor_name">Doctor Name</label>
      <input type="text" id="doctor_name" name="doctor_name" required />

      <label for="specialty">Specialty</label>
      <input type="text" id="specialty" name="specialty" placeholder="e.g. Cardiology" />

      <label for="availability">Availability (e.g. Mon–Fri 9am–5pm)</label>
      <textarea id="availability" name="availability" rows="3"></textarea>

      <button type="submit" name="add_doctor">Add Doctor</button>
    </form>
  </section>

  <section>
    <h2>Schedule Appointment</h2>
    <form method="POST">
      <label for="doctor_id">Select Doctor</label>
      <select id="doctor_id" name="doctor_id" required>
        <option value="">-- Select Doctor --</option>
        <?php foreach ($doctors as $doc): ?>
          <option value="<?= $doc['id'] ?>">
            <?= htmlspecialchars($doc['name'] . ' (' . $doc['specialty'] . ')') ?>
          </option>
        <?php endforeach; ?>
      </select>

      <label for="patient_name">Patient Name</label>
      <input type="text" id="patient_name" name="patient_name" required />

      <label for="appointment_date">Appointment Date & Time</label>
      <input type="datetime-local" id="appointment_date" name="appointment_date" required />

      <button type="submit" name="schedule_appointment">Schedule Appointment</button>
    </form>
  </section>

  <section>
    <h2>Record Patient Visit</h2>
    <form method="POST">
      <label for="visit_patient_name">Patient Name</label>
      <input type="text" id="visit_patient_name" name="visit_patient_name" required />

      <label for="visit_doctor_id">Select Doctor</label>
      <select id="visit_doctor_id" name="visit_doctor_id" required>
        <option value="">-- Select Doctor --</option>
        <?php foreach ($doctors as $doc): ?>
          <option value="<?= $doc['id'] ?>">
            <?= htmlspecialchars($doc['name'] . ' (' . $doc['specialty'] . ')') ?>
          </option>
        <?php endforeach; ?>
      </select>

      <label for="visit_date">Visit Date & Time</label>
      <input type="datetime-local" id="visit_date" name="visit_date" required />

      <label for="visit_notes">Notes</label>
      <textarea id="visit_notes" name="visit_notes" rows="4"></textarea>

      <button type="submit" name="record_visit">Record Visit</button>
    </form>
  </section>
</main>

</body>
</html>
