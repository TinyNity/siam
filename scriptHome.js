function handleDropdownChange(dropdown) {
    var opt = dropdown.value;
    switch (opt) {
        case "disconnect":
            window.location.href = "./logout.php";
            break;
        case "changePassword":
            window.location.href = "./changePassword.php";
            break;
        case "adminDashboard":
            window.location.href = "./adminDashboard.php";
            break;
        default:
            break;
    }
}
