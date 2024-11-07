<?php

namespace App\Views\Admin\Pages\User;

use App\Views\BaseView;

class index extends BaseView
{
    public static function render($data = null)
    {
?>
        <!-- / Navbar -->

        <!-- Content wrapper -->
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="card mb-3">
                <h5 class="card-header">Danh sách khách hàng</h5>
                <div class="card-body">
                    <!-- Basic Breadcrumb -->
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="javascript:void(0);">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="javascript:void(0);">Danh sách khách hàng</a>
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
            <!-- Basic Bootstrap Table -->
            <div class="card">
                <div class="table-responsive text-nowrap">
                    <?php
                    if (count($data)):
                    ?>
                        <table class="table">
                            <thead class="table-light">
                                <tr>
                                    <th>Họ và Tên</th>
                                    <th>Avatar</th>
                                    <th>Email</th>
                                    <th>Số điện thoại</th>
                                    <th>Địa Chỉ</th>
                                    <th>Trạng thái</th>
                                    <th>Quyền</th>
                                    <th>Tùy chỉnh</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                                <?php
                                foreach ($data as $item):
                                ?>
                                    <tr>
                                        <td>
                                            <?= $item['username'] ?>
                                        </td>
                                        <td>
                                            <img src="<?= APP_URL ?>/public/uploads/users/<?= $item['avatar'] ?>" alt="Avatar" class="rounded-circle" width="50px" />
                                        </td>

                                        <td>
                                            <?= $item['email'] ?>
                                        </td>
                                        <td>
                                            <?= $item['phone'] ?>
                                        </td>
                                        <td>
                                            <?= $item['address'] ?>
                                        </td>
                                        <td><?= ($item['status'] == 0) ? 'Khóa ' : 'Hoạt động ' ?></td>
                                        <td><?= ($item['role'] == 0) ? 'Quản trị viên ' : 'Khách hàng' ?></td>

                                        <td>
                                            <div class="dropdown">
                                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                                    data-bs-toggle="dropdown">
                                                    <i class="bx bx-dots-vertical-rounded"></i>
                                                </button>
                                                <div class="dropdown-menu">
                                                    <a class="dropdown-item" href=""><i
                                                            class="bx bx-edit-alt me-1"></i> Edit</a>
                                                    <a class="dropdown-item" href=""><i class="bx bx-trash me-1"></i>
                                                        Delete</a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>

                                <?php
                                endforeach;
                                ?>
                            </tbody>
                        </table>

                    <?php
                    else:

                    ?>
                        <h4 class="text-center text-danger">Không có dữ liệu</h4>
                    <?php
                    endif;

                    ?>


                </div>
            </div>
            <!--/ Basic Bootstrap Table -->
        </div>
<?php
    }
}