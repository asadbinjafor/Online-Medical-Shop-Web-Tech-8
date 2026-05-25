

function validateCategoryForm()
{
    var name = document.getElementById("cat_name").value.trim();
    var type = document.getElementById("cat_type").value;

    if(name == ""){
        alert("Category name is required");
        return false;
    }
    if(type == ""){
        alert("Please select a category type (liquid or solid)");
        return false;
    }
    return true;
}

function validateMedicineForm()
{
    var name         = document.getElementById("name").value.trim();
    var categoryId   = document.getElementById("category_id").value;
    var vendorName   = document.getElementById("vendor_name").value.trim();
    var price        = document.getElementById("price").value.trim();
    var availability = document.getElementById("availability").value.trim();

    if(name == ""){
        alert("Medicine name is required");
        return false;
    }
    if(categoryId == "" || categoryId == "0"){
        alert("Please select a category");
        return false;
    }
    if(vendorName == ""){
        alert("Vendor name is required");
        return false;
    }
    if(price == "" || isNaN(price) || parseFloat(price) <= 0){
        alert("Price must be a number greater than 0");
        return false;
    }
    if(availability == "" || isNaN(availability) || parseInt(availability) < 0 || availability.indexOf(".") !== -1){
        alert("Availability must be a non-negative whole number");
        return false;
    }
    return true;
}

function confirmDelete(itemName)
{
    return confirm("Are you sure you want to delete this " + itemName + "? This action cannot be undone.");
}

function updateOrderStatus(orderId, status)
{
    var label = status === "accepted" ? "accept" : "reject";
    if(!confirm("Are you sure you want to " + label + " order #" + orderId + "?")){
        return;
    }

    var msgDiv = document.getElementById("orderMsg");
    if(msgDiv){
        msgDiv.innerHTML = "";
    }

    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function(){
        if(this.readyState == 4){
            if(this.status == 200){
                var data = JSON.parse(this.responseText);
                if(data.success){
                    var badge      = document.getElementById("status-badge-" + orderId);
                    var actionCell = document.getElementById("action-cell-" + orderId);

                    if(badge){
                        badge.className = "order-status-badge status-" + data.status;
                        badge.textContent = data.status.charAt(0).toUpperCase() + data.status.slice(1);
                    }
                    if(actionCell){
                        actionCell.innerHTML = "<span class='text-muted'>\u2014</span>";
                    }
                    if(msgDiv){
                        msgDiv.innerHTML = "<div class='msg-success'>Order #" + orderId + " has been " + data.status + ".</div>";
                    }
                }
                else{
                    if(msgDiv){
                        msgDiv.innerHTML = "<div class='msg-error'>" + (data.message || "Failed to update order.") + "</div>";
                    } else {
                        alert(data.message || "Failed to update order.");
                    }
                }
            }
            else{
                if(msgDiv){
                    msgDiv.innerHTML = "<div class='msg-error'>Server error. Please try again.</div>";
                } else {
                    alert("Server error. Please try again.");
                }
            }
        }
    };

    xhttp.open("POST", "../control/admin_order_status_api.php", true);
    xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhttp.send(
        "order_id=" + encodeURIComponent(orderId) +
        "&status="  + encodeURIComponent(status)
    );
}
