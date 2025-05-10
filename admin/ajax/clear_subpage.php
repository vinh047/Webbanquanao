<?php
session_start();
unset($_SESSION['last_subpage']); // Xoá subpage nhớ cuối cùng
header('Location: /admin/index.php?page=thuoctinh&pageadmin=1');
exit;
