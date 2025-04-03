<?php
// Check if user is admin
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    $_SESSION['message'] = 'Bạn không có quyền truy cập trang này';
    $_SESSION['message_type'] = 'danger';
    header('Location: index.php');
    exit;
}

// Get all majors for dropdown
$majors = getAllMajors($pdo);
?>

<div class="card">
    <div class="card-header">
        <h4 class="mb-0">Thêm Sinh viên mới</h4>
    </div>
    <div class="card-body">
        <form action="actions/student-actions.php?action=create" method="POST" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="maSV" class="form-label">Mã sinh viên</label>
                        <input type="text" class="form-control" id="maSV" name="maSV" required>
                    </div>
                    <div class="mb-3">
                        <label for="hoTen" class="form-label">Họ tên</label>
                        <input type="text" class="form-control" id="hoTen" name="hoTen" required>
                    </div>
                    <div class="mb-3">
                        <label for="gioiTinh" class="form-label">Giới tính</label>
                        <select class="form-select" id="gioiTinh" name="gioiTinh" required>
                            <option value="Nam">Nam</option>
                            <option value="Nữ">Nữ</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="ngaySinh" class="form-label">Ngày sinh</label>
                        <input type="date" class="form-control" id="ngaySinh" name="ngaySinh" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="maNganh" class="form-label">Ngành học</label>
                        <select class="form-select" id="maNganh" name="maNganh" required>
                            <option value="">-- Chọn ngành học --</option>
                            <?php foreach ($majors as $major): ?>
                                <option value="<?php echo $major['MaNganh']; ?>"><?php echo $major['TenNganh']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="hinh" class="form-label">Hình ảnh</label>
                        <input type="file" class="form-control" id="hinh" name="hinh" onchange="previewImage(this)">
                        <div class="mt-2">
                            <img id="imagePreview" src="#" alt="Preview" style="max-width: 200px; max-height: 200px; display: none;">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Mật khẩu</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="confirmPassword" class="form-label">Xác nhận mật khẩu</label>
                        <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required>
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-end">
                <a href="index.php?page=students" class="btn btn-secondary me-2">Hủy</a>
                <button type="submit" class="btn btn-primary">Lưu</button>
            </div>
        </form>
    </div>
</div>

