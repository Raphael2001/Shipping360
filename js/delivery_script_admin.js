(function ($) {

    $(document).ready(function () {
        if (document.URL.includes("post.php")) {

            if (localStorage.getItem("redirect") === null) {
                localStorage.setItem("redirect", false);
            }
            if (localStorage.getItem("redirect") == "true") {
                alert("i have been redirected")
                // $("#shipping360_create_delivery").trigger("click");
                document.getElementById("shipping360_create_delivery").click()
                localStorage.removeItem("redirect")
            }
        }

        $(".shipping360_shop_order_delivery_btn").click(function (e) {
            e.preventDefault();
            showspinner();


            const ordermodel = 'order-model';

            elm = document.getElementById(ordermodel)
            if (elm) {
                elm.parentNode.removeChild(elm);

            }

            var order_id = $(this).data('id');

            $.ajax({
                type: "POST",
                url: ajaxurl,
                cache: false,
                async: true,
                data: {
                    'action': 'popupnew',
                    'id': order_id,
                },
                success: function (response) {
                    document.body.insertAdjacentHTML("beforeend", response);

                    document.getElementById("close").addEventListener("click", function (e) {
                        e.preventDefault();
                        var element = document.getElementById(ordermodel);
                        element.parentNode.removeChild(element);

                    });
                    document.getElementById("createorder").addEventListener("click", function (e) {
                        e.preventDefault();
                        const distributor = document.getElementById("distributor").value;

                        $.ajax({

                            url: ajaxurl,
                            method: "POST",
                            data: {
                                'action': 'createshipping',
                                'id': order_id,
                                'distributor': distributor,
                            },
                        }).success(function (response) {

                            // document.body.insertAdjacentHTML("beforeend", response);
                            // var element = document.getElementById(ordermodel);
                            // element.parentNode.removeChild(element);
                        }).fail(function () {
                        }).complete(function () {
                            location.reload();

                        }).always(function () {
                        });
                    });

                },
                fail: function () {

                },
                complete: function () {
                    hidespinner();

                }
            });


        })
    })
}(jQuery));

function openpopupafterredicret() {
    $("#shipping360_create_delivery").trigger("click");

}

function close_popup(order_id) {

    document.getElementById('order-model' + order_id).style.display = "none";
    document.getElementById('order-model' + order_id).innerHTML = '';
    // var element = document.getElementById('order-model' + order_id);

    // element.parentNode.removeChild();
}


function showspinner() {
    // showing a spinner

    document.body.style = "overflow: hidden;margin: 0;";

    document.getElementById("lock-modal").style.display = "block";
    document.getElementById("loading-circle").style.display = "block";

}

function hidespinner() {
    // hidding a spinner
    document.body.style = "overflow: scroll";

    document.getElementById("lock-modal").style.display = "none";
    document.getElementById("loading-circle").style.display = "none";


}


function openorder(id) {


    var url = window.location.toString();
    var pathArray = url.split('/');
    new_url = '';
    for (i = 0; i < pathArray; i++) {
        if (pathArray[i].substring(0, 8) == "edit.php")
            break;
        new_url = + pathArray[i];
    }
    window.location.assign(new_url + "post.php?post=" + id + "&action=edit");

}