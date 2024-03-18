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
        case "history":
            window.location.href = "./gameHistory.php";
            break;
        case "home":
            window.location.href="./home.php";
            break;
        default:
            break;
    }
}