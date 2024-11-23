<?php

namespace App\Views\Admin\Pages\ProductVariant;

use App\Views\BaseView;

class SettingVariant extends BaseView
{
  public static function render($data = null)
  {
    unset($_SESSION['id']); 
    ?>
    <div class="content-wrapper">
      <!-- Content -->

      <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
          <div class="col-md-12">
            <div class="card mb-6">
              <!-- Account -->
              <div class="card-body">
                <div class="">
                  <h2 class="text-center">Tạo biến thể</h2>
                </div>
              </div>
              <div class="card-body pt-4">
                <form action="/admin/productvariant" id="" method="post" enctype="multipart/form-data">
                  <input type="hidden" name="method" id="" value="POST">
                  <div class="row g-6">
                    <input type="hidden" name="id" value="<?= $data['product'][0]['id'] ?>">
                    <?php
                      $_SESSION['id'] = $data['product'][0]['id'];
                    ?>
                    <div class="col-md-12">
                      <img src="<?= APP_URL ?>/public/uploads/products/<?= $data['product'][0]['image'] ?>" width="200px"
                        class="rounded mx-auto d-block" alt="">
                    </div>
                    <div class="col-md-6">
                      <label for="name" class="form-label">Tên <span class="text-danger"> *</span></label>
                      <input value="<?= $data['product'][0]['name'] ?>" class="form-control" type="text" id="name"
                        name="name" disabled />
                    </div>


                    <div class="col-md-6">
                      <label for="category_name" class="form-label">Danh mục <span class="text-danger"> *</span></label>
                      <input value="<?= $data['product'][0]['category_name'] ?>" type="category_name" class="form-control"
                        id="category_name" name="category_name" placeholder="Address" disabled />
                    </div>
                    <table class="table table-striped">
                      <div class="row mt-3 mb-3 justify-content-between">
                        <!-- <a href="javascript:void(0)" onclick="create()" class="btn btn-primary btn-sm"
                        style="width: 100px">Thêm hàng</a> -->
                        <a href="/admin/variant/add" class="btn btn-primary" style="width: 200px">Thêm thuộc tính</a>
                      </div>

                      <thead>
                        <tr>
                          <th scope="col">Tên</th>
                          <?php
                          foreach ($data['variant'] as $itemVariant):
                            ?>
                            <th value="<?= $itemVariant['product_variant_id'] ?>">
                              <?= $itemVariant['product_variant_name'] ?>
                            </th>
                            <?php
                          endforeach;
                          ?>
                        </tr>
                      </thead>
                      <tbody id="multi_properties">
                        <tr class="items_properties">
                          <th scope="row"><?= $data['product'][0]['name'] ?></th>
                          <?php
                          $processed_ids = []; // Mảng để theo dõi các pro_variant_id đã xử lý
                          foreach ($data['variant_opt'] as $itemVariant_opt):
                            if (in_array($itemVariant_opt['pro_variant_id'], $processed_ids)) {
                              // Bỏ qua nếu pro_variant_id đã tồn tại
                              continue;
                            }
                            // Thêm pro_variant_id vào danh sách đã xử lý
                            $processed_ids[] = $itemVariant_opt['pro_variant_id'];
                            ?>
                            <td value="<?= $itemVariant_opt['pro_variant_id'] ?>">
                              <?php
                              foreach ($data['variant_opt'] as $itemVariant_value):

                                if ($itemVariant_value['pro_variant_id'] === $itemVariant_opt['pro_variant_id']):
                                  // Chỉ hiển thị nếu pro_variant_id khớp
                                  ?>
                                  <div class="form-check w-50"> <!-- Checkbox input -->
                                    <input class="form-check-input" type="checkbox" value="<?= $itemVariant_value['id'] ?>"
                                      id="flexCheckChecked" name="option_vl_name[]">

                                    <!-- Input ẩn để gửi pro_variant_id -->
                                    <input type="hidden" name="pro_variant_id[]"
                                      value="<?= $itemVariant_opt['pro_variant_id'] ?>">

                                    <label class="form-check-label" for="flexCheckChecked">
                                      <?= $itemVariant_value['name'] ?>
                                    </label>
                                  </div>
                                  <?php
                                endif;
                              endforeach;
                              ?>

                            </td>
                            <?php
                          endforeach;
                          ?>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                  <div class="mt-6">
                    <button type="submit" class="btn btn-primary me-3" name>Lưu</button>
                    <button type="reset" class="btn btn-outline-secondary" name>Nhập lại</button>
                  </div>
              </div>
              </form>
            </div>
            <!-- /Account -->
          </div>
        </div>
      </div>
    </div>
    <script>
      CKEDITOR.replace('description');
    </script>
    <script>
      function create() {
        let count_items = document.querySelectorAll(".items_properties").length - 1;
        count_items++;
      }
    </script>
    <?php
  }
}