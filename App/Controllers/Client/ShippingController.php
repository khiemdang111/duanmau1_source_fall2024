<?php
namespace App\Controllers\Client;

use App\Helpers\CartHelper;
use App\Models\GhnService;


class ShippingController
{

    private $ghnService;

    public function __construct()
    {
        $this->ghnService = new GhnService();
    }

    public function createOrderGHN()
    {
        $cart_data = CartController::getorder();
        $address = $_SESSION['information']['address'] . " " . $_SESSION['information']['ward'] . " " . $_SESSION['information']['district'] . " " . $_SESSION['information']['province'];
        $total_vulue = CartHelper::tatol($cart_data);
        $orderData = [
            "payment_type_id" => 2,
            "note" => "Ghi chú ...",
            "required_note" => "KHONGCHOXEMHANG",
            "from_name" => "Wine Cần Thơ",
            "from_phone" => "0987654321",
            "from_address" => "Phường Thường Thạnh, Quận Cái Răng , TP.Cần Thơ, Vietnam",
            "from_ward_name" => "Phường Thường Thạnh",
            "from_district_name" => "Quận Cái Răng",
            "from_province_name" => "TP.Cần Thơ",
            "return_phone" => "0332190444",
            "return_address" => "Phường Thường Thạnh, Quận Cái Răng , TP.Cần Thơ, Vietnam",
            "to_name" => $_SESSION['information']['name'],
            "to_phone" => $_SESSION['information']['phone'],
            "to_address" => $address,
            "to_ward_code" => "20308",
            "to_district_id" => 1444,
            "cod_amount" => (int) $total_vulue['total'],
            "content" => "Thong tin đơn hàng của Wine Cần Thơ",
            "weight" => 200,
            "length" => 1,
            "width" => 19,
            "height" => 10,
            "insurance_value" => (int) $total_vulue['total'],
            "service_id" => 0,
            "service_type_id" => 2,
            "items" => [],
        ];
        foreach ($cart_data as $cart) {
            if (isset($cart['data']) && $cart['data']) {
                $weight = isset($cart['data']['weight']) ? $cart['data']['weight'] : 0.6;
                $length = isset($cart['data']['length']) ? $cart['data']['length'] : 10;
                $width = isset($cart['data']['width']) ? $cart['data']['width'] : 10;
                $height = isset($cart['data']['height']) ? $cart['data']['height'] : 10;
                $product = [
                    'name' => $cart['data']['name'],
                    'code' => (string) $cart['data']['id'],
                    'quantity' => (int) $cart['quantity'],
                    'price' => (int) $cart['data']['price'],
                    'length' => (int) $length,
                    'width' => (int) $width,
                    'height' => (int) $height,
                    'weight' => (int) $weight,
                    'category' => [
                        "level1" => $cart['data']['category']['level1'] ?? "Chưa xác định",
                        "level2" => $cart['data']['category']['level2'] ?? null,
                    ],
                ];
                $orderData['items'][] = $product;
            }
        }
        try {
            $response = $this->ghnService->createShippingOrder($orderData);
            if (!empty($response['code']) && $response['code'] === 200) {
                if (!isset($_SESSION['information']['delivery'])) {
                    echo 'Tạo đơn hàng thành công!';
                    print_r($response);
                }
            } else {
                echo 'Có lỗi xảy ra: ' . $response['message'];
                die;
            }
        } catch (\Exception $e) {
            echo 'Lỗi: ' . $e->getMessage();
        }
    }
    public function createOrderGHTK()
    {
        $cart_data = CartController::getorder();
        $order = [
            'products' => []  // Khởi tạo mảng sản phẩm trống
        ];
        // Lặp qua giỏ hàng và xây dựng mảng sản phẩm
        foreach ($cart_data as $cart) {
            if ($cart['data']) {
                $weight = isset($cart['data']['weight']) ? $cart['data']['weight'] : 0.6;
                $product = [
                    'name' => $cart['data']['name'],
                    'weight' => $weight,
                    'quantity' => $cart['quantity'],
                    'product_code' => $cart['data']['id'],
                ];
                $order['products'][] = $product;
            }
        }
        if (empty($order['products'])) {
            echo "Lỗi: Đơn hàng không có sản phẩm!";
            return;
        }
        $total_vulue = CartHelper::tatol($cart_data);
        $order['order'] = [
            'id' => (string) $_SESSION['order_id'],
            'pick_name' => 'Wine Cần Thơ',
            'pick_address' => 'FPT Polytechnic',
            'pick_province' => 'TP.Cần Thơ',
            'pick_district' => 'Quận Cái Răng',
            'pick_ward' => 'Phường Thường Thạnh',
            'pick_tel' => '0901234567',
            'tel' => $_SESSION['information']['phone'],
            'name' => $_SESSION['information']['name'],
            'address' => $_SESSION['information']['address'],
            'province' => $_SESSION['information']['province'],
            'district' => $_SESSION['information']['district'],
            'ward' => $_SESSION['information']['ward'],
            'hamlet' => 'Khác',
            'is_freeship' => 1,
            'pick_money' => $total_vulue['total'],
            'note' => 'Giao giờ hành chính',
            'value' => $total_vulue['total'],
            'transport' => 'road',
        ];
        // echo '<pre>';
        // var_dump($order);
        // die;
        // Gửi yêu cầu API
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://services.giaohangtietkiem.vn/services/shipment/order',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($order),
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
                "Token: ATo2Yp39vAKo3XErRxJZERRIisA4QIHqA4KgCE",
            ],
        ]);
        $response = curl_exec($curl);
        curl_close($curl);
        $data = json_decode($response, true);
        if ($data['success']) {
            if (!isset($_SESSION['information']['delivery'])) {
                echo "Tạo đơn hàng thành công! Booking ID: " . $data['order']['label'];
            }
        } else {
            echo "Lỗi: " . $data['message'];
            die;
        }
    }


    public function deleteOrderGHTK()
    {

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://services.giaohangtietkiem.vn/services/shipment/cancel/S22783950.MN15-09-H31.1724187581",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_HTTPHEADER => array(
                "Token: ATo2Yp39vAKo3XErRxJZERRIisA4QIHqA4KgCE",
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        echo 'Response: ' . $response;
    }
    public function getGHTKFee()
    {
        // Nhận dữ liệu từ form

        // $province = isset($_POST['province']) ? $_POST['province'] : '';
        // $district = isset($_POST['district']) ? $_POST['district'] : '';


        $apiUrl = "https://services.giaohangtietkiem.vn/services/shipment/fee";
        $apiKey = "ATo2Yp39vAKo3XErRxJZERRIisA4QIHqA4KgCE";

        // Dữ liệu gửi lên API
        $data = [
            'weight' => 2500,
            'distance' => 15,
            'pick_province' => 'Hồ Chí Minh',
            'pick_district' => 'Quận 5',
            'province' => 'Hà Nội',
            'district' => 'Quận 1',
        ];

        // Gửi yêu cầu tới API GHTK
        $ch = curl_init($apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Token: $apiKey",
            "Content-Type: application/json"
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($httpCode !== 200) {
            echo json_encode(['error' => 'Không thể tính phí giao hàng', 'code' => $httpCode]);
            exit;
        }


        $result = json_decode($response, true);

        if (isset($result['fee']['options']['shipMoneyText'])) {

            $shipMoneyText = $result['fee']['options']['shipMoneyText'];
            $shipMoney = preg_replace('/[^0-9]/', '', $shipMoneyText);


            header('Content-Type: application/json');
            // $_SESSION['phihip'] = $shipMoney;
            // echo json_encode(['fee' => $shipMoney]);
            var_dump(json_encode(['fee' => $shipMoney]));
            die;
        } else {
            // Trả về thông báo lỗi nếu không tìm thấy phí
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Không có phí giao hàng trong phản hồi']);
        }

       
    }

}



