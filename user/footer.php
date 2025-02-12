</body>
<script>
const path = window.location.pathname;
const linkMap = {
    "/dts-vpaa/user/dashboard.php": "dashboard-link",
    "/dts-vpaa/user/transaction.php": "transaction-link",
    "/dts-vpaa/user/tracking": "track-files-link",
    "/dts-vpaa/user/user.php": "user-link",
    "/dts-vpaa/user/department.php": "department-link",
    "/dts-vpaa/user/campus.php": "campus-link",
    "/dts-vpaa/user/archiving.php": "archiving-link",
    "/dts-vpaa/user/incoming.php": "incoming-link",
    "/dts-vpaa/user/actioned-document.php": "actioned-link",
    "/dts-vpaa/user/completed.php": "completed-transaction-link",
    "/dts-vpaa/user/report.php": "report-link"
};

const activeLinkId = linkMap[path];
if (activeLinkId) {
    const activeLink = document.getElementById(activeLinkId);
    if (activeLink) {
        activeLink.classList.add("active");
        activeLink.style.backgroundColor = "#0d47a1";  // Set your preferred active background color
        activeLink.style.color = "white";  // Set text color to white for visibility
    } else {
        console.log("Element with ID not found:", activeLinkId);
    }
} else {
    console.log("No matching path found in linkMap.");
}
toastr.options = {
    "closeButton": true,
    "progressBar": true,
    "positionClass": "toast-top-right",
    "timeOut": "1500"
};
</script>
<script src="../assets/popper.js"></script>
<!-- <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script> --> -->
<script src="../assets/bootstrap.min.js"></script>
<!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script> -->
<!-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> -->
<script src="../assets/chart.js"></script>
<!-- <script src="https://cdn.datatables.net/2.1.8/js/dataTables.js"></script> -->
<script src="../assets/dataTables.js"></script>


</html>