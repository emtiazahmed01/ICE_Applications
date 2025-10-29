<?php
session_start();

// Redirect if not logged in
if (!isset($_SESSION['email'])) {
  header("Location: login.html");
  exit();
}

// Database connection
$conn = new mysqli("sql206.infinityfree.com", "if0_40132910", "2beornot2be2001", "if0_40132910_nstu_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch user info
$email = $_SESSION['email'];
$sql = "SELECT name, email FROM usersignup WHERE email=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="bn" xml:lang="bn">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Application Generator - NSTU Dashboard</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Bengali&display=swap" rel="stylesheet">
  <link rel="icon" href="./images/favicon.ico">
  <link rel="stylesheet" href="./dashboard.css">
  <style>
    .app-container {
      background: #fff;
      border-radius: 10px;
      padding: 25px 30px;
      box-shadow: 0 3px 8px rgba(0,0,0,0.1);
      max-width: 900px;
      margin: auto;
      font-family: "Noto Sans Bengali", Arial, sans-serif;
    }

    .app-container h2 {
      text-align: center;
      margin-bottom: 25px;
      color: #1a237e;
    }

    label {
      display: block;
      margin-top: 10px;
      font-weight: bold;
    }

    input, select {
      width: 100%;
      padding: 8px;
      margin-top: 5px;
      border: 1px solid #ccc;
      border-radius: 5px;
    }

    button {
      margin-top: 20px;
      padding: 10px 20px;
      background: #1a237e;
      color: white;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      transition: 0.3s;
    }

    button:hover { background: #303f9f; }

    .print-btn { background: #28a745; margin-left: 10px; }
    .download-btn { background: #ff9800; margin-left: 10px; }
    .next-btn { background: #673ab7; margin-left: 10px; }

    .application {
      margin-top: 40px;
      padding: 40px;
      border: 2px solid #333;
      background: #fff;
      line-height: 1.8;
      font-size: 16px;
      display: none;
    }

    .signature img {
      width: 150px;
      height: auto;
    }

    .note {
      margin-top: 50px;
      font-size: 14px;
    }
  </style>
</head>
<body>

  <!-- ===== HEADER ===== -->
  <header class="dashboard-header">
    <div class="logo">
      <a href="./services.php"><img src="./images/logo.png" alt="NSTU Logo"></a>
      <h1>NOAKHALI SCIENCE AND TECHNOLOGY UNIVERSITY</h1>
    </div>
    <div class="user-info">
      <img src="./images/user.png" alt="User Avatar" class="user-avatar">
      <span><?php echo htmlspecialchars($user['name']); ?></span>
    </div>
  </header>

  <!-- ===== SIDEBAR ===== -->
  <aside class="sidebar">
    <ul>
      <li><a href="./services.php">🏠 Dashboard</a></li>
      <li><a href="./applicationgenerator.php" class="active">📝 Application Generator</a></li>
      <li><a href="./payment_update.php">💳 Payment Status</a></li>
      <li><a href="./edit_profile.php">⚙️ Edit Profile</a></li>
      <li><a href="./logout.php" class="logout-btn">🚫 Logout</a></li>
    </ul>
  </aside>

  <!-- ===== MAIN CONTENT ===== -->
  <main class="dashboard-main">
    <div class="app-container">
      <h2>স্টুডেন্ট অ্যাপ্লিকেশন জেনারেটর</h2>

      <form id="appForm">
        <label>নাম (বাংলায়):</label>
        <input type="text" id="name_bn" required>

        <label>নাম (ইংরেজিতে):</label>
        <input type="text" id="name_en">

        <label>পিতার নাম (বাংলায়):</label>
        <input type="text" id="father_bn" required>

        <label>পিতার নাম (ইংরেজিতে):</label>
        <input type="text" id="father_en">

        <label>যাচাইকরণ রসিদ নং:</label>
        <input type="text" id="receipt">

        <label>বর্ষ:</label>
        <input type="text" id="year">

        <label>টার্ম:</label>
        <input type="text" id="term">

        <label>রোল নং:</label>
        <input type="text" id="roll">

        <label>শিক্ষাবর্ষ:</label>
        <input type="text" id="session">

        <label>আবেদনের ধরন:</label>
        <select id="subjectType">
          <option value="মার্কশীট">মার্কশীট</option>
          <option value="প্রত্যয়ন পত্র">প্রত্যয়ন পত্র</option>
          <option value="প্রশংসাপত্র">প্রশংসাপত্র</option>
        </select>

        <label>তারিখ:</label>
        <input type="date" id="date">

        <label>আপনার স্বাক্ষর (ছবি আপলোড করুন):</label>
        <input type="file" id="signature" accept="image/*">

        <button type="button" onclick="generateApplication()">Generate</button>
        <button type="button" class="print-btn" onclick="printApplication()">Print</button>
        <button type="button" class="download-btn" onclick="downloadPDF()">Download</button>
        <button type="button" class="next-btn" onclick="goNextPage()">Next</button>
      </form>

      <div id="output" class="application"></div>
    </div>
  </main>

  <!-- ===== FOOTER ===== -->
  <footer class="footer">
    <div class="footer-bottom">
      <p>© <?php echo date("Y"); ?> NSTU Academic Services</p>
    </div>
  </footer>

  <!-- ===== SCRIPTS ===== -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.3/html2pdf.bundle.min.js"></script>
  <script>
    let signatureData = "";

    document.getElementById("signature").addEventListener("change", function(event) {
      const reader = new FileReader();
      reader.onload = e => signatureData = e.target.result;
      reader.readAsDataURL(event.target.files[0]);
    });

    window.addEventListener('DOMContentLoaded', () => {
      fetch('get_user.php')
      .then(res => res.json())
      .then(user => {
          if(user.error){ alert(user.error); return; }
          document.getElementById('name_bn').value = user.name || '';
          document.getElementById('roll').value = user.studentid || '';
          document.getElementById('year').value = user.year || '';
          document.getElementById('term').value = user.term || '';
          document.getElementById('session').value = user.level || '';
      });

      // Auto-select type from query param
      let type = new URLSearchParams(window.location.search).get('type');
      if (type) document.getElementById('subjectType').value = type;
    });

    function generateApplication() {
      const name_bn = document.getElementById("name_bn").value;
      const name_en = document.getElementById("name_en").value;
      const father_bn = document.getElementById("father_bn").value;
      const father_en = document.getElementById("father_en").value;
      const receipt = document.getElementById("receipt").value;
      const year = document.getElementById("year").value;
      const term = document.getElementById("term").value;
      const roll = document.getElementById("roll").value;
      const session = document.getElementById("session").value;
      const subjectType = document.getElementById("subjectType").value;
      const date = document.getElementById("date").value;

      const fullName = name_bn + (name_en ? " (" + name_en + ")" : "");
      const fatherName = father_bn + (father_en ? " (" + father_en + ")" : "");

      const html = `
        <div class="app-header">তথ্য ও যোগাযোগ প্রকৌশল বিভাগ<br>নোয়াখালী বিজ্ঞান ও প্রযুক্তি বিশ্ববিদ্যালয়</div><br>
        <p>তারিখঃ ${date}</p>
        <div class="borabor">
          বরাবর,<br>
          তথ্য ও যোগাযোগ প্রকৌশল বিভাগ<br>
          চেয়ারম্যান<br>
          নোয়াখালী বিজ্ঞান ও প্রযুক্তি বিশ্ববিদ্যালয়<br>
          নোয়াখালী-৩৮১৪।
        </div><br>
        <p>বিষয়ঃ বর্ষ ${year}, টার্ম ${term} এর ${subjectType} প্রাপ্তির জন্য আবেদন।</p>
        <p>জনাব,</p>
        <p>সবিনয় নিবেদন এই যে, আমি ${name_bn}, আপনার বিভাগের বর্ষ ${year}, টার্ম ${term} এর একজন নিয়মিত ছাত্র।
        আমার ব্যাক্তিগত প্রয়োজনে এর জন্য ${subjectType} পত্র প্রয়োজন। আমার প্রয়োজনীয় তথ্যাবলী প্রদান করা হলো।
        অতএব জনাবের নিকট আকুল আবেদন এই যে, আমাকে ${subjectType} প্রদান করে বাধিত করবেন।</p>
        <div class="student-info">
          <p>নাম: ${fullName}</p>
          <p>পিতার নাম: ${fatherName}</p>
          <p>যাচাইকরণ রসিদ নং: ${receipt}</p>
        </div>
        <p>বিনীত নিবেদক,</p>
        <div class="signature">
          <p>নাম: ${name_bn} &nbsp;&nbsp; রোল নং: ${roll}</p>
          <p>বর্ষ: ${year}, টার্ম: ${term}</p>
          <p>শিক্ষাবর্ষ: ${session}</p>
          ${signatureData ? `<img src="${signatureData}" alt="Signature">` : "<p>স্বাক্ষর:</p>"}
          <p>${fullName}</p>
        </div>
        <div class="note"><b>দ্রষ্টব্যঃ</b> ৩ কর্ম দিবস পর অফিস হতে সংগ্রহ করতে হবে।</div>
      `;
      const output = document.getElementById("output");
      output.innerHTML = html;
      output.style.display = "block";
    }

    function printApplication() {
      const content = document.getElementById("output").innerHTML;
      if (!content) return alert("Please generate the application first!");
      const printWindow = window.open('', '', 'height=600,width=800');
      printWindow.document.write('<html><head><title>Print</title></head><body>' + content + '</body></html>');
      printWindow.document.close();
      printWindow.print();
    }

    function downloadPDF() {
      const element = document.getElementById("output");
      if (element.style.display === "none") return alert("Please generate the application first!");
      html2pdf().from(element).set({
        margin: 10,
        filename: 'application.pdf',
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2 },
        jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
      }).save();
    }

    function goNextPage() {
    let type = document.getElementById('subjectType').value;

    // Send the type to PHP to set session
    fetch('set_payment_session.php?type=' + encodeURIComponent(type))
      .then(() => {
          window.location.href = "./payment_form.php";
      });
      }

  </script>
</body>
</html>
