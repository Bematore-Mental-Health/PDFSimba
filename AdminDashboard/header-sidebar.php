<!-- Sidebar Toggle -->
<button id="sidebarToggle" class="btn btn-dark d-md-none position-fixed m-3">
  <i class="bi bi-list fs-4"></i>
</button>

<!-- Sidebar -->
<div id="sidebar" class="sidebar">
  <h4 class="logo"><i class="bi bi-file-earmark-text-fill me-2"></i>PDFSimba</h4>
<a href="index.php" class="sidebar-link active">
  <i class="bi bi-speedometer2 me-2"></i> Home
</a>

<a href="dashboardconv.php">
  <i class="bi bi-graph-up me-2"></i> Conversion Analytics
</a>

<a href="mainpageconv.php">
  <i class="bi bi-file-earmark-bar-graph me-2"></i> Main Page Stats
</a>

<a href="users.php">
  <i class="bi bi-people me-2"></i> User Management
</a>

<a href="admins.php">
  <i class="bi bi-shield-lock me-2"></i> Admin Management
</a>

<a href="profile.php">
  <i class="bi bi-person-circle me-2"></i> My Profile
</a>
<div class="p-3">
  <a href="../DashboardBackend/logout.php" class="btn btn-light text-dark w-100 d-flex align-items-center justify-content-center custom-logout-btn">
    <i class="bi bi-box-arrow-right me-2 text-dark"></i> Log Out
  </a>
</div>

</div>


  <div class="main-content">
    <div class="top-banner d-flex justify-content-between align-items-center">
     <div>
  <h4>Hi, <?= htmlspecialchars($_SESSION['admin_name'] ?? 'Admin'); ?></h4>
  <p>Convert, manage, and secure your documents with ease. Trusted by professionals and students worldwide.</p>
</div>

      <div>
        <i class="bi bi-book-half fs-1"></i>
      </div>
    </div>

