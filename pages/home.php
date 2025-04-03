<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body text-center">
                <h1 class="card-title">Hệ thống Quản lý Sinh viên</h1>
                <p class="card-text">Chào mừng đến với hệ thống quản lý sinh viên và đăng ký học phần</p>
                
                <?php if (!isset($_SESSION['user_id'])): ?>
                <div class="mt-4">
                    <a href="index.php?page=login" class="btn btn-primary btn-lg">Đăng nhập</a>
                </div>
                <?php else: ?>
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-body">
                                <h5 class="card-title"><i class="fas fa-book"></i> Đăng ký Học phần</h5>
                                <p class="card-text">Xem danh sách và đăng ký các học phần mới</p>
                                <a href="index.php?page=register-courses" class="btn btn-primary">Đăng ký ngay</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-body">
                                <h5 class="card-title"><i class="fas fa-clipboard-list"></i> Học phần đã đăng ký</h5>
                                <p class="card-text">Xem danh sách các học phần bạn đã đăng ký</p>
                                <a href="index.php?page=my-courses" class="btn btn-primary">Xem học phần</a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

