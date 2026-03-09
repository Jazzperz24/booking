const catEmoji = { Dance:"💃", Fitness:"🏋️", Sports:"⚽", "Wellness/Yoga":"🧘", Belle:"✨" };

const selectedCategory = sessionStorage.getItem("selectedCategory");
const selectedCoaches  = JSON.parse(sessionStorage.getItem("selectedCoaches") || "[]");

if (!selectedCategory || !selectedCoaches.length) {
    document.getElementById("guardMsg").style.display = "block";
} else {
    document.getElementById("bookingContent").style.display = "block";
    document.getElementById("summaryCategory").textContent = selectedCategory;
    document.getElementById("summaryCoaches").innerHTML = selectedCoaches.map(c => `
        <div class="summary-item">
            <div class="summary-avatar">${catEmoji[selectedCategory] || "👤"}</div>
            <div>
                <div class="summary-name">${esc(c.name)}</div>
                <div class="summary-spec">${esc(c.specialty || selectedCategory)}</div>
            </div>
        </div>
    `).join("");
}

document.getElementById("bookDate").min = new Date().toISOString().split("T")[0];

$("#bookingForm").submit(function(e) {
    e.preventDefault();
    $("#btnConfirm").prop("disabled", true).html("<i class='fa-solid fa-spinner fa-spin'></i> Booking...");

    $.ajax({
        type:        "POST",
        url:         "../includes/process.php",
        traditional: true,
        data: {
            book:         1,
            category:     selectedCategory,
            coach_ids:    JSON.stringify(selectedCoaches.map(c => c.id)),
            book_date:    $("#bookDate").val(),
            book_time:    $("#bookTime").val(),
            session_type: $("#sessionType").val(),
            duration:     $("#duration").val(),
            notes:        $("#bookNotes").val()
        },
        success: function(response) {
            $("#btnConfirm").prop("disabled", false).html("<i class='fa-solid fa-check'></i> Confirm Booking");
            if (response.trim() === "success") {
                sessionStorage.removeItem("selectedCategory");
                sessionStorage.removeItem("selectedCoaches");
                Swal.fire({
                    icon: "success",
                    title: "Booking Confirmed! 🎉",
                    html: `Your session with <b>${selectedCoaches.map(c => c.name).join(", ")}</b> has been booked.`,
                    confirmButtonColor: "#d4a853"
                }).then(() => {
                    window.location.href = "/REGISTRATIONSFORM/index.php";
                });
            } else {
                Swal.fire({ icon: "error", title: "Booking Failed", text: response, confirmButtonColor: "#d4a853" });
            }
        },
        error: function() {
            $("#btnConfirm").prop("disabled", false).html("<i class='fa-solid fa-check'></i> Confirm Booking");
            Swal.fire({ icon: "error", title: "Error", text: "Something went wrong. Please try again.", confirmButtonColor: "#d4a853" });
        }
    });
});

function esc(s) {
    return String(s).replace(/&/g,"&amp;").replace(/</g,"&lt;").replace(/>/g,"&gt;");
}