
function validateOrderFilters()
{
    var q    = document.getElementById("orderSearch");
    var from = document.getElementById("fromDate");
    var to   = document.getElementById("toDate");

    if(q && q.value.trim().length > 100){
        alert("Search text must be 100 characters or less");
        return false;
    }
    if(from && from.value != "" && to && to.value != "" && from.value > to.value){
        alert("From date cannot be after to date");
        return false;
    }
    return true;
}

function searchOrders()
{
    if(!validateOrderFilters()){
        return;
    }

    var q      = document.getElementById("orderSearch").value.trim();
    var status = document.getElementById("statusFilter").value;
    var from   = document.getElementById("fromDate").value;
    var to     = document.getElementById("toDate").value;

    var url = "../control/orders_search_api.php?status=" + encodeURIComponent(status) +
              "&from=" + encodeURIComponent(from) +
              "&to="   + encodeURIComponent(to) +
              "&q="    + encodeURIComponent(q);

    var tbody = document.getElementById("ordersTableBody");
    if(tbody){
        tbody.innerHTML = "<tr><td colspan='6' class='text-center'>Searching...</td></tr>";
    }

    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function(){
        if(this.readyState == 4){
            if(this.status == 200){
                var data = JSON.parse(this.responseText);
                if(data.success){
                    renderOrderRows(data.orders);
                } else {
                    showOrderMsg(data.message || "Search failed", "error");
                    if(tbody){
                        tbody.innerHTML = "<tr><td colspan='6' class='text-center'>Search failed.</td></tr>";
                    }
                }
            } else {
                if(tbody){
                    tbody.innerHTML = "<tr><td colspan='6' class='text-center'>Server error.</td></tr>";
                }
            }
        }
    };
    xhttp.open("GET", url, true);
    xhttp.send();
}

function renderOrderRows(orders)
{
    var tbody = document.getElementById("ordersTableBody");
    if(!tbody){
        return;
    }
    tbody.innerHTML = "";

    if(orders.length == 0){
        tbody.innerHTML = "<tr><td colspan='6' class='text-center'>No orders found.</td></tr>";
        return;
    }

    for(var i = 0; i < orders.length; i++){
        var o = orders[i];
        var tr = document.createElement("tr");

        tr.innerHTML =
            "<td>#" + o.id + "</td>" +
            "<td>" + o.order_date + "</td>" +
            "<td>" + parseFloat(o.total_amount).toFixed(2) + "</td>" +
            "<td>" + (o.payment_method || "—") + "</td>" +
            "<td><span class='order-status-badge status-" + o.status + "'>" +
                o.status.charAt(0).toUpperCase() + o.status.slice(1) + "</span></td>" +
            "<td class='order-actions'>" +
                "<a class='btn-link' href='customer_order_detail.php?order_id=" + o.id + "'>View</a>" +
                "<a class='btn-link' href='order_invoice.php?order_id=" + o.id + "' target='_blank'>Invoice</a>" +
            "</td>";

        tbody.appendChild(tr);
    }
}

function resetOrderFilters()
{
    document.getElementById("orderSearch").value = "";
    document.getElementById("statusFilter").value = "";
    document.getElementById("fromDate").value = "";
    document.getElementById("toDate").value = "";
    searchOrders();
}

function cancelOrder(orderId)
{
    if(!confirm("Are you sure you want to cancel this pending order?")){
        return;
    }

    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function(){
        if(this.readyState == 4 && this.status == 200){
            var data = JSON.parse(this.responseText);
            if(data.success){
                showOrderDetailMsg(data.message, "success");
                setTimeout(function(){
                    location.reload();
                }, 800);
            } else {
                showOrderDetailMsg(data.message || "Failed to cancel", "error");
            }
        }
    };
    xhttp.open("POST", "../control/customer_order_cancel_api.php", true);
    xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhttp.send("order_id=" + encodeURIComponent(orderId));
}

function reorderPurchase(orderId)
{
    if(!confirm("Add all available items from this order to your cart?")){
        return;
    }

    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function(){
        if(this.readyState == 4 && this.status == 200){
            var data = JSON.parse(this.responseText);
            var msgType = data.success ? "success" : "error";

            if(data.warnings && data.warnings.length > 0){
                showOrderDetailMsg(data.message + "<br>" + data.warnings.join("<br>"), "warning");
            } else {
                showOrderDetailMsg(data.message, msgType);
            }

            if(data.success && data.cart_count !== undefined){
                var navCount = document.getElementById("navCartCount");
                if(navCount){
                    navCount.textContent = data.cart_count;
                }
            }
        }
    };
    xhttp.open("POST", "../control/customer_order_reorder_api.php", true);
    xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhttp.send("order_id=" + encodeURIComponent(orderId));
}

function addToCartFromDetail(medicineId)
{
    var qtyInput = document.getElementById("detailQty");
    var quantity = qtyInput ? parseInt(qtyInput.value) : 1;
    var stock    = parseInt(qtyInput ? qtyInput.getAttribute("data-stock") : "0");

    if(isNaN(quantity) || quantity <= 0){
        alert("Quantity must be at least 1");
        return;
    }
    if(quantity > stock){
        alert("Only " + stock + " units available in stock");
        return;
    }

    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function(){
        if(this.readyState == 4 && this.status == 200){
            var data = JSON.parse(this.responseText);
            if(data.success){
                var navCount = document.getElementById("navCartCount");
                if(navCount){
                    navCount.textContent = data.cart_count;
                }
                alert(data.message);
            } else {
                alert(data.message || "Failed to add to cart");
            }
        }
    };
    xhttp.open("POST", "../control/cart_add_api.php", true);
    xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhttp.send(
        "medicine_id=" + encodeURIComponent(medicineId) +
        "&quantity=" + encodeURIComponent(quantity)
    );
}

function showOrderMsg(message, type)
{
    var el = document.getElementById("orderMsg");
    if(el){
        el.innerHTML = "<div class='msg-" + type + "'>" + message + "</div>";
    }
}

function showOrderDetailMsg(message, type)
{
    var el = document.getElementById("orderDetailMsg");
    if(el){
        el.innerHTML = "<div class='msg-" + type + "'>" + message + "</div>";
    }
}
