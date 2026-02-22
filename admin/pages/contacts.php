<?php
$page_title = "User Queries";
include 'includes/header.php';
include 'includes/sidebar.php';

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM user_queries WHERE id = $id");
    $_SESSION['success'] = "Query deleted successfully!";
    header('Location: ' . BASE_PATH . '/queries');
    exit();
}

// Get all queries newest first
$queries = mysqli_query($conn, "SELECT * FROM user_queries ORDER BY created_at DESC");
$total   = mysqli_num_rows($queries);

// Unread (today)
$today_count = mysqli_fetch_row(mysqli_query($conn,
    "SELECT COUNT(*) FROM user_queries WHERE DATE(created_at) = CURDATE()"))[0];
?>

<div class="main-content">

<style>
    .alert { padding:12px 20px; border-radius:6px; margin-bottom:20px; font-size:14px; }
    .alert-success { background:#d4edda; color:#155724; border:1px solid #c3e6cb; }

    .stats-row { display:flex; gap:16px; margin-bottom:24px; }
    .stat-box {
        flex:1; background:#fff; border-radius:10px;
        box-shadow:0 2px 12px rgba(0,0,0,0.07);
        padding:18px 22px; text-align:center;
    }
    .stat-box small { font-size:12px; color:#888; text-transform:uppercase; letter-spacing:.06em; display:block; margin-bottom:6px; }
    .stat-box strong { font-size:28px; font-weight:700; }

    /* table card */
    .queries-card {
        background:#fff; border-radius:10px;
        box-shadow:0 2px 12px rgba(0,0,0,0.07); overflow:hidden;
    }
    .queries-card-header {
        padding:16px 22px; border-bottom:1px solid #f0f0f0;
        font-weight:700; font-size:15px; color:#222;
        display:flex; align-items:center; gap:8px;
    }

    table { width:100%; border-collapse:collapse; font-size:13px; }
    thead th {
        background:#f8f9fa; padding:11px 14px; text-align:left;
        font-weight:600; font-size:11px; letter-spacing:.06em;
        text-transform:uppercase; color:#6c757d;
        border-bottom:2px solid #eee;
    }
    tbody td { padding:13px 14px; border-bottom:1px solid #f5f5f5; vertical-align:top; color:#333; }
    tbody tr:last-child td { border-bottom:none; }
    tbody tr:hover td { background:#fafafa; }

    .query-name   { font-weight:600; color:#222; }
    .query-email  { font-size:12px; color:#888; margin-top:2px; }
    .query-subject{ font-weight:500; color:#333; }
    .query-msg    { color:#555; max-width:320px; font-size:13px; line-height:1.5; }
    .query-date   { font-size:12px; color:#999; white-space:nowrap; }

    .btn-del {
        padding:5px 12px; font-size:12px; background:#fff;
        color:#dc3545; border:1.5px solid #dc3545; border-radius:5px;
        text-decoration:none; display:inline-block; transition:all .2s;
    }
    .btn-del:hover { background:#dc3545; color:#fff; }

    .btn-reply {
        padding:5px 12px; font-size:12px; background:#1aab8a;
        color:#fff; border:none; border-radius:5px;
        text-decoration:none; display:inline-block; margin-bottom:5px; transition:all .2s;
    }
    .btn-reply:hover { background:#158a6e; color:#fff; }

    .no-data { text-align:center; padding:50px; color:#aaa; font-size:15px; }

    /* Modal */
    .modal-overlay {
        display:none; position:fixed; inset:0;
        background:rgba(0,0,0,0.45); z-index:1000;
        align-items:center; justify-content:center;
    }
    .modal-overlay.active { display:flex; }
    .modal-box {
        background:#fff; border-radius:12px; padding:28px 30px;
        width:100%; max-width:520px; box-shadow:0 20px 60px rgba(0,0,0,0.2);
        position:relative;
    }
    .modal-box h5 { font-size:16px; font-weight:700; margin-bottom:4px; color:#222; }
    .modal-box .meta { font-size:12px; color:#888; margin-bottom:16px; }
    .modal-subject { font-size:14px; font-weight:600; color:#1aab8a; margin-bottom:10px; }
    .modal-message { font-size:14px; color:#444; line-height:1.7; background:#f8f9fa; border-radius:6px; padding:14px; margin-bottom:20px; }
    .modal-close {
        position:absolute; top:14px; right:16px;
        background:none; border:none; font-size:20px;
        color:#aaa; cursor:pointer; line-height:1;
    }
    .modal-close:hover { color:#333; }
</style>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
<?php endif; ?>

<!-- Stats -->
<div class="stats-row">
    <div class="stat-box">
        <small>Total Queries</small>
        <strong style="color:#667eea;"><?php echo $total; ?></strong>
    </div>
    <div class="stat-box">
        <small>Received Today</small>
        <strong style="color:#1aab8a;"><?php echo $today_count; ?></strong>
    </div>
</div>

<!-- Table -->
<div class="queries-card">
    <div class="queries-card-header">
        📩 Contact Queries
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Sender</th>
                <th>Subject</th>
                <th>Message</th>
                <th>Received</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($total === 0): ?>
                <tr><td colspan="6" class="no-data">📭 No queries yet.</td></tr>
            <?php else: ?>
                <?php while ($q = mysqli_fetch_assoc($queries)): ?>
                <tr>
                    <td style="color:#aaa;">#<?php echo $q['id']; ?></td>
                    <td>
                        <div class="query-name"><?php echo htmlspecialchars($q['name']); ?></div>
                        <div class="query-email"><?php echo htmlspecialchars($q['email']); ?></div>
                    </td>
                    <td class="query-subject"><?php echo htmlspecialchars($q['subject']); ?></td>
                    <td>
                        <div class="query-msg">
                            <?php
                            $msg = htmlspecialchars($q['message']);
                            echo strlen($msg) > 80 ? substr($msg, 0, 80) . '...' : $msg;
                            ?>
                            <?php if (strlen($q['message']) > 80): ?>
                                <a href="javascript:void(0)"
                                   onclick='openModal(
                                       <?php echo $q["id"]; ?>,
                                       "<?php echo addslashes(htmlspecialchars($q['name'])); ?>",
                                       "<?php echo addslashes(htmlspecialchars($q['email'])); ?>",
                                       "<?php echo addslashes(htmlspecialchars($q['subject'])); ?>",
                                       "<?php echo addslashes(htmlspecialchars($q['message'])); ?>",
                                       "<?php echo date('d M Y, h:i A', strtotime($q['created_at'])); ?>"
                                   )'
                                   style="color:#1aab8a; font-size:12px; white-space:nowrap;">
                                    Read more
                                </a>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td class="query-date">
                        <?php echo date('d M Y', strtotime($q['created_at'])); ?><br>
                        <span style="color:#bbb;"><?php echo date('h:i A', strtotime($q['created_at'])); ?></span>
                    </td>
                    <!-- <td>
                        <a href="mailto:<?php echo htmlspecialchars($q['email']); ?>?subject=Re: <?php echo urlencode($q['subject']); ?>"
                           class="btn-reply">Reply</a><br>
                        <a href="?delete=<?php echo $q['id']; ?>"
                           class="btn-del"
                           onclick="return confirm('Delete query from <?php echo addslashes($q['name']); ?>?')">
                           Delete
                        </a>
                    </td> -->
                </tr>
                <?php endwhile; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Full message modal -->
<div class="modal-overlay" id="msgModal">
    <div class="modal-box">
        <button class="modal-close" onclick="closeModal()">✕</button>
        <h5 id="modalName"></h5>
        <div class="meta" id="modalMeta"></div>
        <div class="modal-subject" id="modalSubject"></div>
        <div class="modal-message" id="modalMessage"></div>
        <a id="modalReply" href="#" class="btn-reply">↩ Reply via Email</a>
    </div>
</div>

<script>
function openModal(id, name, email, subject, message, date) {
    document.getElementById('modalName').textContent    = name;
    document.getElementById('modalMeta').textContent    = email + '  ·  ' + date;
    document.getElementById('modalSubject').textContent = '📌 ' + subject;
    document.getElementById('modalMessage').textContent = message;
    document.getElementById('modalReply').href = 'mailto:' + email + '?subject=Re: ' + encodeURIComponent(subject);
    document.getElementById('msgModal').classList.add('active');
}
function closeModal() {
    document.getElementById('msgModal').classList.remove('active');
}
// Close on backdrop click
document.getElementById('msgModal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});
</script>

</div>

<?php include 'includes/footer.php'; ?>