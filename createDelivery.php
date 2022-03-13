<?php


// direction :
// 1 - ממני אל היעד
// 22 - מהיעד אליי
// 333 - מיעד ליעד

if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
$BASE = "https://shipping360-cukp6uab4a-uc.a.run.app/api/v1";
$company = "";


function log_me($message)
{
    if (WP_DEBUG === true) {
        if (is_array($message) || is_object($message)) {
            error_log(print_r($message, true));
        } else {
            error_log($message);
        }
    }
}

add_action('admin_head', function () {
    ?>
    <style>
    #order_data .order_data_column div.address[style*="display: none"] + div.edit_address {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    }
    </style>
    <?php
});


function function_alert($msg)
{
    echo "<script type='text/javascript'>alert('$msg');</script>";
}
function getCompanyDeatials()
{
    $email  = get_option('mail');
    $token = get_option('token');

    $URL = "https://shipping360-cukp6uab4a-uc.a.run.app/api/v1". "/company?email=". $email. "&token=". $token;
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($curl, CURLOPT_URL, $URL);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
        'Content-Type:application/json; UTF-8'));
    $result = curl_exec($curl);
    $var = json_decode($result, true);
    $body = $var["body"];


    curl_close($curl);
    return $body;
}

function createanarray($order_id)
{
    $order = wc_get_order($order_id);
    $company = get_option("company");

    $senderinfo = array(
        "name"=> $company["CompanyName"],
        "address"=>$company["CompanyStreet"],
        "city"=>$company["CompanyCity"],
        "phone"=>$company["CompanyPhone"],
        "email"=>get_option('mail'),
        "phone2"=>"",
        "comment"=>$company["CompanyComment"],
    );
    $receiverinfo = array(
        "name"=> $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(),
        "address"=>$order->get_shipping_address_1() . ' ' . $order->get_shipping_address_2(),
        "city"=>$order->get_shipping_city(),
        "phone"=>$order->get_billing_phone(),
        "email"=>$order->get_billing_email(),
        "phone2"=>"",
        "comment"=>"",
    );
    $data = array(
        "type"=>"רגילה",
        "distributor"=>"אחר",
        "companyid"=> 1,
        "token"=> get_option("token"),
        "direction"=>"1",
        "senderinfo"=> $senderinfo,
        "receiverinfo"=>$receiverinfo,
    );

    
  
  
    return $data;
}

function loader($data_id)
{
    ?>
     <div class="buttonloader">
      <div id="lock-modal"></div>
        <div id="loading-circle"></div>
        <button class="shipping360_shop_order_delivery_btn" data-id = "<?php echo $data_id?>"
             style="background-color: #2e4453; color:#fff; border-radius: 5px;text-align: center;width:100px;height:auto;font-size:10px; line-height: 1.5em;  border: none;-webkit-appearance: none; outline: none; padding:10px;cursor: pointer;">יצירת משלוח</button>
       
    </div>

         
    <?php
}

add_action('add_meta_boxes', 'add_meta_boxes_shipping360');
function add_meta_boxes_shipping360()
{
    add_meta_box(
        'shipping360_delivery_box',
        __('Shipping360 delivery settings', 'shipping360'),
        'shipping360_delivery_box_func',
        'shop_order',
        'side',
        'high'
    );
}

function get_distributor()
{
    // global $company;
    $data = get_option("company");

    // $data = $GLOBALS["company"];
    $distributors = $data["Distributors"];
    return $distributors;
}
add_action('wp_ajax_popupnew', 'popupnew');
function popupnew()
{
    $distributors = get_distributor();
    $order_id = isset($_POST['id']) ? $_POST['id'] : 0; ?>   
    <form method="POST">
        <div id="order-model" class="order-model full-screen flex-container-center">
            <div class="modal-contents">

                <div data-id = "<?php echo $order_id?>" class="close_btn_order" id="close">+</div>
                <div class="orderdetails" >

                        <div class="container">

                            <div class="row">

                                <div class="col">
                                    <br>
                                    <strong id="sorderistributor" for="orderistributor"> מפיץ :</strong> 
                                        <select list="distributor" class="form-control deliverydistributor"
                                                name="distributor" id="distributor" style="width:150px;" required>
                                                <datalist id="distributor">
                                                    <option value="" disabled selected hidden>שם מפיץ</option>
                                                                    <?php
                                                        $option_values = $distributors;

    foreach ($option_values as $key => $value) {
        if ($value["IsActive"] == true) {
            ?>
                                                                    <option value = "<?php echo $key; ?>"><?php echo $value["DisplayName"]; ?></option>
                                                                    <?php
        }
    } ?>
                                                </datalist>
                                        </select>
                                        <br>
                                                   
                                        <button id="createorder" data-id="<?php echo $order_id?>" 
                                        style="margin-top:10px;background-color: #2e4453; color:#fff; border-radius: 5px;text-align: center;width:100px;height:auto;font-size:10px; line-height: 1.5em;  border: none;-webkit-appearance: none; outline: none; padding:10px;cursor: pointer;">יצירת משלוח</button>
                                        
                                     
                
                                </div>

                            </div>
                          
                        </div>

                    

                </div>
            </div>
        </div>
       <form>
    <?php
}



function shipping360_delivery_box_func()
{
    $order_id = get_the_ID();
    $delivery_id = get_post_meta($order_id, 'shipping360_delivery_number', true);

    if ($delivery_id) {
        ?>
        <label style="color: #4b8680; font-size:16px;"> משלוח נוצר</label>
       
        <?php
    } else {
        loader($order_id);
    }
}

add_action('wp_ajax_createshipping', 'createshipping');
function createshipping()
{
    global $BASE;

    $distributor = isset($_POST['distributor']) ? $_POST['distributor'] : "אחר";
    $order_id = isset($_POST['id']) ? $_POST['id'] : 0;
    $order = wc_get_order($order_id);
    $company = get_option("company");

    // $apar = get_post_meta($order_id, "_billing_apar", true);
    // $apar = check_comment_shipping360($apar, "דירה");
    $comments=array($order->get_customer_note());
    $receiver_comment = get_comments_string($comments);


    $senderinfo = array(
        "name"=> $company["CompanyName"],
        "address"=>$company["CompanyStreet"],
        "city"=>$company["CompanyCity"],
        "phone"=>$company["CompanyPhone"],
        "email"=>get_option('mail'),
        "phone2"=>"",
        "comment"=>$company["CompanyComment"],
    );
    $receiverinfo = array(
        "name"=> $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(),
        "address"=>$order->get_shipping_address_1() . ' ' . $order->get_shipping_address_2(),
        "city"=>$order->get_shipping_city(),
        "phone"=>$order->get_billing_phone(),
        "email"=>$order->get_billing_email(),
        "phone2"=>"",
        "comment"=> $receiver_comment,
    );
    $data = array(
        "type"=>"רגילה",
        "distributor"=> $distributor,
        "companyid"=> $company["CompanyID"],
        "token"=> get_option("token"),
        "direction"=>"1",
        "senderinfo"=> $senderinfo,
        "receiverinfo"=>$receiverinfo,
        "source"=>"WordPress",
        "orderid"=>$order_id,
    );
    $url= $BASE."/shipping";
    $result = basic_post_with_curl_shipping360($url, $data);
    $obj = json_decode($result);
    $num =  $obj->body;
    update_order_new($order_id, $num);
}

function check_comment_shipping360($comment, $label)
{
    // checks if the comment has content
    if ($comment!="") {
        $comment = $label. " " .$comment;
    } else {
        $comment="";
    }
    return $comment;
}
function get_comments_string($comments)
{
    // go over the array and returns a string of the comments
    $receiver_comment = "";

    foreach ($comments as $comm) {
        if ($comm != "") {
            if ($receiver_comment != "") {
                $receiver_comment = $receiver_comment . ", ".$comm;
            } else {
                $receiver_comment  = $receiver_comment .$comm;
            }
        }
    }
    return $receiver_comment;
}

add_action('wp_ajax_update_order', 'update_order');
function update_order()
{
    $order_id = isset($_POST['id']) ? $_POST['id'] : 0;
    $shipping_number = isset($_POST['shipping_num']) ? $_POST['shipping_num'] : "";

    $order = wc_get_order($order_id);

    add_post_meta($order_id, 'shipping360_delivery_number', $shipping_number);
    $order->add_order_note('משלוח נוצר בהצלחה, מספר משלוח: ' . $shipping_number);
    die();
}
function update_order_new($order_id, $shipping_number)
{
    $order = wc_get_order($order_id);

    update_post_meta($order_id, 'shipping360_delivery_number', $shipping_number);
    $order->add_order_note('משלוח נוצר בהצלחה, מספר משלוח: ' . $shipping_number);
    die();
}


/**
 * @param $columns
 * @return array
 * create column
 */
function shipping360_create_shoporder_column($columns)
{
    $new_columns = (is_array($columns)) ? $columns : array();
    unset($new_columns['order_actions']);

    //edit this for you column(s)
    //all of your columns will be added before the actions column
    $new_columns['shipping360_delivry_column'] = 'Shipping360';
    //stop editing

    $new_columns['order_actions'] = $columns['order_actions'];
    return $new_columns;
}

add_filter('manage_edit-shop_order_columns', 'shipping360_create_shoporder_column');
function shipping360_print_shoporder_column($column)
{
    $order_id = get_the_ID();
    $has_delivery = get_post_meta($order_id, 'shipping360_delivery_number', true);


    //start editing, I was saving my fields for the orders as custom post meta
    //if you did the same, follow this code
    if ($column == 'shipping360_delivry_column') {
        $order = new WC_Order($order_id);
        if (!$has_delivery) {
            $array = (createanarray(get_the_ID()));
            loader($order_id);
        } else {
            ?>
            <label style="color: #4b8680; font-size:16px;"> משלוח נוצר</label>
           
            <?php
        }
    }
}

add_action('manage_shop_order_posts_custom_column', 'shipping360_print_shoporder_column', 2);

add_action('admin_init', 'set_company');
add_action('wp_login', 'set_company_without_checking');
function set_company_without_checking()
{
    $company = getCompanyDeatials();
    ?>
    <p class = "checktest" style="font-size:70px;">$company</p>
    <?php
    var_dump($company);
    update_option("company", $company);
}

                
function set_company()
{
   
    if (get_option("need_reload") == "true") {
        $company = getCompanyDeatials();
        update_option("company", $company);
        update_option("need_reload", "false");
    }
}



function success_notice() { ?>
	
	<div class="notice notice-success is-dismissible">
		<p><?php _e('משלוח נוצר בהצלחה', 'shapeSpace'); ?></p>
	</div>
	
<?php }

function basic_post_with_curl_shipping360($url, $data)
{
    $data_string = json_encode($data);
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt(
        $curl,
        CURLOPT_HTTPHEADER,
        array(
    'Content-Type: application/json',
    'Content-Length: ' . strlen($data_string))
    );
    $result = curl_exec($curl);

    curl_close($curl);
    return $result;
}
