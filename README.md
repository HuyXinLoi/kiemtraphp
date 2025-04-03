# Hệ thống Quản lý Sinh viên và Đăng ký Học phần

Đây là ứng dụng web PHP để quản lý sinh viên và đăng ký học phần, được xây dựng theo yêu cầu.

## Tính năng

- **Quản lý Sinh viên**: Thêm, sửa, xóa, xem chi tiết sinh viên
- **Đăng ký Học phần**: Sinh viên có thể đăng ký các học phần
- **Giỏ Học phần**: Hệ thống giỏ hàng để đăng ký nhiều học phần cùng lúc
- **Xem Học phần đã đăng ký**: Sinh viên có thể xem danh sách học phần đã đăng ký
- **Hệ thống Đăng nhập**: Đăng nhập bằng mã sinh viên và mật khẩu
- **Quản lý số lượng sinh viên**: Mỗi học phần có số lượng dự kiến, giảm khi sinh viên đăng ký

## Cài đặt

1. Clone repository này vào thư mục web server của bạn (ví dụ: htdocs nếu bạn dùng XAMPP)
2. Tạo cơ sở dữ liệu MySQL với tên "Test1" và import file SQL đã cung cấp
3. Chạy file `setup.php` để thiết lập các cột bổ sung cần thiết cho ứng dụng
4. Cấu hình kết nối cơ sở dữ liệu trong file `config/database.php`
5. Truy cập ứng dụng qua trình duyệt web

## Tài khoản mặc định

- **Admin**: 
  - Tên đăng nhập: admin
  - Mật khẩu: admin

- **Sinh viên**: 
  - Tên đăng nhập: Mã sinh viên (ví dụ: 0123456789)
  - Mật khẩu mặc định: Mã sinh viên (ví dụ: 0123456789)

## Cấu trúc thư mục

