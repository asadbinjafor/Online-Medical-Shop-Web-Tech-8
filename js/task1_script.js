

function validateRegistration()
{
    var name     = document.getElementById("reg_name").value.trim();
    var email    = document.getElementById("reg_email").value.trim();
    var password = document.getElementById("reg_password").value;
    var address  = document.getElementById("address").value.trim();
    var phone    = document.getElementById("phone").value.trim();

    if(name == ""){
        alert("Full name is required");
        return false;
    }
    if(email == ""){
        alert("Email is required");
        return false;
    }
    if(password == ""){
        alert("Password is required");
        return false;
    }
    if(address == ""){
        alert("Address is required");
        return false;
    }
    if(phone == ""){
        alert("Phone number is required");
        return false;
    }
    if(!/^[0-9]+$/.test(phone)){
        alert("Phone number must contain digits only");
        return false;
    }
    if(password.length < 8){
        alert("Password must be at least 8 characters");
        return false;
    }
    if(email.indexOf("@") == -1 || email.indexOf(".") == -1){
        alert("Enter a valid email address");
        return false;
    }
    return true;
}

function validateLogin()
{
    var email    = document.getElementById("email").value.trim();
    var password = document.getElementById("password").value;

    if(email == ""){
        alert("Email is required");
        return false;
    }
    if(password == ""){
        alert("Password is required");
        return false;
    }
    return true;
}

function validateProfile()
{
    var name    = document.getElementById("name").value.trim();
    var email   = document.getElementById("email").value.trim();
    var address = document.getElementById("address").value.trim();
    var phone   = document.getElementById("phone").value.trim();
    var currentPassword = document.getElementById("current_password").value;
    var newPassword     = document.getElementById("new_password").value;

    if(name == "" || email == "" || address == "" || phone == ""){
        alert("Name, email, address and phone are required");
        return false;
    }
    if(email.indexOf("@") == -1 || email.indexOf(".") == -1){
        alert("Enter a valid email address");
        return false;
    }
    if(newPassword != "" && currentPassword == ""){
        alert("Current password is required to change password");
        return false;
    }
    if(newPassword != "" && newPassword.length < 8){
        alert("New password must be at least 8 characters");
        return false;
    }
    return true;
}

function searchMedicines()
{
    var list = document.getElementById("medicineList");
    if(!list){
        return;
    }

    var q      = document.getElementById("searchText").value.trim();
    var vendor = document.getElementById("vendorFilter").value;
    var genre  = document.getElementById("genreFilter").value;
    var type   = document.getElementById("typeFilter").value;

    var url = "../control/medicine_search.php?q=" + encodeURIComponent(q) +
              "&vendor=" + encodeURIComponent(vendor) +
              "&genre="  + encodeURIComponent(genre) +
              "&type="   + encodeURIComponent(type);

    list.innerHTML = "<div class='no-results'>Searching...</div>";

    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function(){
        if(this.readyState == 4){
            if(this.status == 200){
                var data = JSON.parse(this.responseText);
                if(data.success){
                    renderMedicineCards(data.medicines);
                }
                else{
                    list.innerHTML = "<div class='no-results'>Search failed. Please try again.</div>";
                }
            }
            else{
                list.innerHTML = "<div class='no-results'>Search failed. Please try again.</div>";
            }
        }
    };
    xhttp.open("GET", url, true);
    xhttp.send();
}

function renderMedicineCards(medicines)
{
    var list = document.getElementById("medicineList");
    list.innerHTML = "";

    if(medicines.length == 0){
        var noResult = document.createElement("div");
        noResult.className = "no-results";
        noResult.textContent = "No medicines found.";
        list.appendChild(noResult);
        return;
    }

    for(var i = 0; i < medicines.length; i++){
        var m    = medicines[i];
        var type = m.category_type ? m.category_type : "solid";

        var card = document.createElement("div");
        card.className = "medicine-card";
        card.setAttribute("data-medicine-id", m.id);

        var title = document.createElement("h3");
        var titleLink = document.createElement("a");
        titleLink.href = "medicine_detail.php?id=" + m.id;
        titleLink.textContent = m.name;
        title.appendChild(titleLink);

        var badge = document.createElement("span");
        badge.className = "badge badge-" + type;
        badge.textContent = type;

        var genre = document.createElement("p");
        genre.textContent = "Genre: " + (m.category_name || "");

        var vendor = document.createElement("p");
        vendor.textContent = "Vendor: " + m.vendor_name;

        var availability = document.createElement("p");
        availability.textContent = "Availability: " + m.availability + " units";

        var price = document.createElement("p");
        price.className = "medicine-price";
        price.textContent = "BDT " + m.price;

        card.appendChild(title);
        card.appendChild(badge);
        card.appendChild(genre);
        card.appendChild(vendor);
        card.appendChild(availability);
        card.appendChild(price);

        var detailLink = document.createElement("a");
        detailLink.className = "btn-link";
        detailLink.href = "medicine_detail.php?id=" + m.id;
        detailLink.textContent = "View Details";
        card.appendChild(detailLink);

        list.appendChild(card);
    }
}
