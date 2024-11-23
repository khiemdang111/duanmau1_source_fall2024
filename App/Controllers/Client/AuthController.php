<?php

namespace App\Controllers\Client;

use App\Models\User;
use App\Models\vnpays;
use App\Views\Client\Pages\Auth\Login;
use App\Views\Client\Layouts\Footer;
use App\Views\Client\Layouts\Header;
use App\Helpers\AuthHelper;
use App\Validations\AuthValidation;
use App\Helpers\NotificationHelper;
use App\Models\Order;
use App\Views\Client\Components\Notification;
use App\Views\Client\Pages\Auth\Edit;
use App\Views\Client\Pages\Auth\Forgetpass;
use App\Views\Client\Pages\Auth\Register;
use App\Views\Client\Pages\Order\index;
use App\Views\Client\Pages\Auth\Verification;
use App\Views\Client\Pages\Auth\ResetPassword;
use App\Views\Client\Pages\Auth\updatePassword;

class AuthController
{
    public static function login()
    {
        Header::render();
        Notification::render();
        NotificationHelper::unset();
        Login::render();
        Footer::render();
    }


    public static function loginAction()
    {
        $is_valid = AuthValidation::login();
        if (!$is_valid) {
            NotificationHelper::error('login_valid', 'Đăng nhập thất bại');
            header('Location: /login');
            exit();
        }
        $data = [
            'username' => $_POST['username'],
            'password' => $_POST['password'],
            'remember' => isset($_POST['remember'])
        ];

        $result = AuthHelper::login($data);
        if ($result) {
            header('Location: /');
        } else {
            NotificationHelper::error('login', 'Đăng nhập thất bại');
            header('Location: /login');
        }
    }

    public static function Register()
    {
        Header::render();
        Notification::render();
        NotificationHelper::unset();
        Register::render();
        Footer::render();
    }
    public static function registerAction()
    {
        $is_valid = AuthValidation::register();
        if (!$is_valid) {
            NotificationHelper::error('register_valid', 'Đăng ký thất bại');
            header('Location: /register');
            exit();
        }
        $hash_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $data = [
            'username' => $_POST['username'],
            'password' => $hash_password,
            'name' => $_POST['name'],
            'email' => $_POST['email'],
            'phone' => $_POST['phone'],
            'address' => $_POST['address']
        ];
        $user = new User();
        $result = $user->createUser($data);
        if ($result) {
            NotificationHelper::success('register_valid', 'Đăng ký thành công');
            header('Location: /login');
        } else {
            var_dump('Đăng ký thất bại');
        }
    }

    public static function logout()
    {
        AuthHelper::logout();
        NotificationHelper::success('logout', 'Đăng xuất thành công');
        header('Location: /');
    }

    public static function edit($id)
    {

        $is_login = AuthHelper::checkLogin();
        if (!$is_login) {
            header('Location: /login');
            NotificationHelper::error('login', 'Vui lòng đăng nhập để xem thông tin tài khoản');
            exit();
        } else {
            $result = AuthHelper::edit($id);
            if ($result) {
                if (isset($_SESSION['error']['login'])) {
                    header('Location: /login');
                    exit();
                }
                if (isset($_SESSION['error']['user_id'])) {
                    $data = $_SESSION['user'];
                    $user_id = $data['id'];
                    header("Location: /users/$user_id");
                    exit();
                }
            }
            $data = $_SESSION['user'];
            Header::render($data);
            Notification::render();
            NotificationHelper::unset();
            Edit::render($data);
            // var_dump($data);
            Footer::render();
        }
    }


    public static function update($id)
    {
        $is_valid = AuthValidation::update();
        if (!$is_valid) {
            NotificationHelper::error('update', 'Cập nhật thất bại  !');
            header("Location: /users/$id");
            exit();
        }
        $data = [
            'email' => $_POST['email'],
            'phone' => $_POST['phone'],
        ];
        // kiểm tra hình ảnh có hợp leej hay không 
        $is_upload = AuthValidation::uploadAvatar();
        if ($is_upload) {
            $data['avatar'] = $is_upload;
        }
        // gọi helper để udate 
        $result = AuthHelper::update($id, $data);
        // kiểm tra kết quả vầ và chuyển hướng
        header("Location: /users/$id");
    }

    public static function history()
    {
        $is_login = AuthHelper::checkLogin();
        if ($is_login) {
            $order = new vnpays();
            $data = $order->getAllvnpays();
            Header::render();
            Notification::render();
            NotificationHelper::unset();
            index::render($data);
            Footer::render();
        } else {
            NotificationHelper::error('er', 'Bạn hông có quyền truyên cập trang này');
            header('location: /');
        }

    }

    public static function forgetpass()
    {
        Header::render();
        Notification::render();
        NotificationHelper::unset();
        Forgetpass::render();
        Footer::render();
    }

    public static function Verification()
    {
        Header::render();
        Notification::render();
        NotificationHelper::unset();
        Verification::render();
        Footer::render();
    }
    public static function VerificationAction()
    {
        $number = $_POST['number'];
        $user = new User();
        $result = $user->getOneToken($number);
        if (!$result) {
            NotificationHelper::error('error', 'Mã xác thực không tồn tại');
            header('location: /Verification');
        } else {
            header('location: /resetPassword');
        }
    }
    public static function resetPassword()
    {
        Header::render();
        Notification::render();
        NotificationHelper::unset();
        ResetPassword::render();
        Footer::render();
    }

    public static function resetPasswordAction()
    {
        $password = $_POST['new_password'];
        $passwordConfirm = $_POST['confirm_password'];
        // echo $_SESSION['id'];
        $user = new User();
        $result = $user->getOneUser($_SESSION['id']);
        // var_dump($result);
        // die();
    }
    public static function updatePassword()
    {
        // Lấy dữ liệu từ POST
        $newPassword = $_POST['new_password'];
        $confirmPassword = $_POST['confirm_password'];

        if (!$newPassword || !$confirmPassword) {
            NotificationHelper::error('password_update', 'Vui lòng nhập đầy đủ thông tin!');
            header("Location: /resetPassword");
            exit();
        }

        if ($newPassword !== $confirmPassword) {
            NotificationHelper::error('password_mismatch', 'Mật khẩu xác nhận không trùng khớp!');
            header("Location: /resetPassword");
            exit();
        }


        // Mã hóa mật khẩu mới
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);


        // Lấy thông tin người dùng từ session (giả sử bạn lưu email hoặc username trong session)
        //  $username = $_SESSION['username']; 
        // Hoặc bạn có thể sử dụng email
        if (!$_SESSION['id']) {
            NotificationHelper::error('user_not_found', 'Không tìm thấy thông tin người dùng!');
            header("Location: /resetPassword");
            exit();
        }

        $data = [
            'password' => $hashedPassword,
        ];

        // Cập nhật mật khẩu vào cơ sở dữ liệu
        $newUser = new User();
        $updateResult = $newUser->update($_SESSION['id'], $data);
        //    var_dump(!$updateResult);
//    die;
        // Kiểm tra kết quả
        if ($updateResult) {
            NotificationHelper::success('password_update', 'Cập nhật mật khẩu thành công!');
            unset($_SESSION['username']); // Xóa session để đảm bảo an toàn
            header("Location: /login");
            exit();
        } else {
            NotificationHelper::error('password_update_fail', 'Có lỗi xảy ra. Vui lòng thử lại!');
            header("Location: /resetPassword");
            exit();
        }
    }
}
